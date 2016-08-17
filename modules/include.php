<?php
/*
 * SIF Private server
 * Contains common function like getting user ID from token
 */

function strpos_array(string $haystack, array $needles) {
	if(is_array($needles)) {
		if(count($needles) == 0)
			return false;
		foreach ($needles as $str) {
			if ( is_array($str) ) {
				$pos = strpos_array($haystack, $str);
			} else {
				$pos = strpos($haystack, $str);
			}
			if ($pos !== FALSE) {
				return $pos;
			}
		}
	} else {
		return strpos($haystack, $needles);
	}
}

function to_datetime(int $timestamp): string
{
	if($timestamp < 86400) $timestamp += 86400;
	
	return date('Y-m-d H:i:s', $timestamp);
}

function to_utcdatetime(int $timestamp): string
{
	if($timestamp < 86400) $timestamp += 86400;
	
	return gmdate('Y-m-d H:i:s', $timestamp);
}

function time_elapsed_string(int $datetime, bool $full = false): string {
	$now = new DateTime;
	$ago = new DateTime("@$datetime");
	$diff = $now->diff($ago);

	$diff->w = floor($diff->d / 7);
	$diff->d -= $diff->w * 7;

	$string = [
		'y' => 'year',
		'm' => 'month',
		'w' => 'week',
		'd' => 'day',
		'h' => 'hour',
		'i' => 'minute',
		's' => 'second',
	];
	foreach ($string as $k => &$v) {
		if ($diff->$k) {
			$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
		} else {
			unset($string[$k]);
		}
	}

	if (!$full) $string = array_slice($string, 0, 1);
	return $string ? implode(', ', $string) . ' ago' : 'just now';
}

/* Creates token */
function token_generate(): string
{
	token_invalidate();
	
	return hash("sha256", strval(rand(0, 32767)).strval(time()));
}

/* Check if token is there */
function token_exist($token): bool
{
	global $DATABASE;
	
	token_invalidate();
	
	if($token == NULL) return false;
	
	foreach($DATABASE->execute_query("SELECT token FROM `logged_in`") as $value)
		if(strcmp($value[0], $token) == 0)
			return true;
	
	return false;
}

/* Kick players not logged in for more than 3 days */
function token_invalidate()
{
	global $DATABASE;
	
	$unix_ts = time();
	
	foreach($DATABASE->execute_query('SELECT * FROM `logged_in`') as $value)
	{
		if(($unix_ts - $value[3]) > 259200)
			$DATABASE->execute_query("DELETE FROM `logged_in` WHERE time = ?", "i", $value[3]);
	}
}

/* Forcefully destroy the token */
function token_destroy(string $token)
{
	global $DATABASE;
	
	$DATABASE->execute_query('DELETE FROM `logged_in` WHERE token = ?', 's', $token);
}

/* Creates new user. For /login/startUp */
/* Returns the User ID or 0 if fail */
function user_create(string $key, string $pwd): int
{
	global $DATABASE;
	
	$unix_ts = time();
	$user_data = [
		$key,		// login_key
		$pwd,		// login_pwd
		$unix_ts,	// create_date
		$unix_ts,	// last_active
		11,			// next_exp
		$unix_ts,	// full_lp_recharge
		"",			// present_table
		"",			// assignment_table
		"",			// live_table
		"",			// unit_table
		"",			// deck_table
		"",			// friend_list
		"",			// sticker_table
		"",			// login_bonus_table
		"",			// album_table
	];
	if($DATABASE->execute_query('INSERT INTO `users` (login_key, login_pwd, create_date, last_active, next_exp, full_lp_recharge, present_table, assignment_table, live_table, unit_table, deck_table, friend_list, sticker_table, login_bonus_table, album_table, unlocked_badge, unlocked_background) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "", "")', 'ssiiiisssssssss', $user_data))
	{
		$user_id = $DATABASE->execute_query('SELECT LAST_INSERT_ID()');
		
		if($user_id)
			return $user_id[0][0];
	}
	return 0;
}

/* Configure user. It supports startWithoutInvite and startSetInvite */
/* Returns true on success, false on failure */
function user_configure(int $user_id, string $invite_code = NULL): bool
{
	global $DATABASE;
	
	if($invite_code == NULL)
	{
		do
		{
			$invite_code = sprintf('%09d', random_int(0,999999999));
		}
		while(count($DATABASE->execute_query('SELECT user_id FROM `users` WHERE invite_code = ?', 's', $invite_code)) > 0);
	}
	else
		if(count($DATABASE->execute_query('SELECT user_id FROM `users` WHERE invite_code = ?', 's', $invite_code)) > 0)
			return false;
	
	// Create users table
	if(
		$DATABASE->execute_query(<<<QUERY
BEGIN;
CREATE TABLE `present_$user_id` (
	item_pos INTEGER PRIMARY KEY AUTO_INCREMENT,	-- The item position
	item_type INTEGER NOT NULL,						-- The item type ID
	card_num INTEGER,								-- The card internal ID (can be other ID) or NULL.
	amount INTEGER NOT NULL,						-- Amount of the item
	message TEXT NOT NULL,							-- Additional message like: "Event achievement reward"
	expire INTEGER DEFAULT NULL						-- Unix timestamp when the item expire or NULL for no expiration
);
CREATE TABLE `information_$user_id` (
	info_pos INTEGER PRIMARY KEY AUTO_INCREMENT,	-- Information position
	message TEXT NOT NULL,							-- The message
	readed BOOL NOT NULL,							-- Is the notice already readed?
	replied BOOL DEFAULT NULL,						-- Reply flag.
	template INTEGER DEFAULT NULL,					-- The notice template ID. Used on friend activity
	from_user INTEGER NOT NULL						-- From user ID
);
CREATE TABLE `assignment_$user_id` (
	assignment_id INTEGER NOT NULL PRIMARY KEY,	-- The assignment id
	start_time INTEGER NOT NULL,				-- Unix timestamp when this assignment added
	end_time INTEGER,							-- Unix timestamp when this assignment end
	new_flag INTEGER NOT NULL DEFAULT 1,		-- Is new?
	count INTEGER DEFAULT 0,					-- Internal counter.
	complete_flag INTEGER NOT NULL DEFAULT 0,	-- Is complete?
	reward TEXT NOT NULL						-- Reward in format: <item ID>:<amount>[:<info ID>], ...
);
CREATE TABLE `live_$user_id` (
	live_id INTEGER NOT NULL PRIMARY KEY,		-- The live ID
	normal_live BOOL NOT NULL DEFAULT 1,		-- Is the live available in Hits? (used to track EX scores)
	score INTEGER NOT NULL DEFAULT 0,			-- Highest score
	combo INTEGER NOT NULL DEFAULT 0,			-- Highest combo
	times INTEGER NOT NULL DEFAULT 0			-- x times played
);
CREATE TABLE `unit_$user_id` (
	unit_id INTEGER PRIMARY KEY AUTO_INCREMENT,	-- The unit owning user ID
	card_id INTEGER NOT NULL,					-- The card internal ID
	current_exp INTEGER NOT NULL DEFAULT 0,		-- Current EXP
	next_exp INTEGER NOT NULL,					-- Next EXP before level up
	level INTEGER NOT NULL DEFAULT 1,			-- Card level
	max_level INTEGER NOT NULL,					-- Card max level
	skill_level INTEGER NOT NULL DEFAULT 1,		-- Skill level
	skill_level_exp INTEGER NOT NULL DEFAULT 0,	-- Skill level EXP. To follow JP v4.0 behaviour.
	health_points INTEGER NOT NULL,				-- Card max HP
	bond INTEGER NOT NULL DEFAULT 0,			-- Card bond
	max_bond INTEGER NOT NULL,					-- Card max bond
	favorite BOOL NOT NULL DEFAULT 0,			-- Flagged as favourite?
	added_time INTEGER NOT NULL					-- Unix timestamp when this card added
);
CREATE TABLE `deck_$user_id` (
	deck_num INTEGER NOT NULL PRIMARY KEY,	-- Deck number
	deck_name VARCHAR(10) NOT NULL,			-- Deck name
	deck_members TEXT NOT NULL				-- Deck list. In format: <unit_id>:<unit_id>. Unit id is unit_id field in `unit_$user_id` table or 0 if no unit is specificed.
);
CREATE TABLE `sticker_$user_id` (
	sticker_id INTEGER NOT NULL PRIMARY KEY,	-- The sticker ID
	amount_bought INTEGER NOT NULL DEFAULT 0	-- How much it already bought.
);
CREATE TABLE `login_bonus_$user_id` (
	login_bonus_id INTEGER NOT NULL PRIMARY KEY,	-- The login bonus ID. ID 0 is reserved for monthly logn bonus.
	counter INTEGER NOT NULL DEFAULT 0				-- The login bonus counter.
);
CREATE TABLE `album_$user_id` (
	card_id INTEGER NOT NULL PRIMARY KEY,			-- The card ID
	flags TINYINT NOT NULL DEFAULT 0,				-- Flags bit: 0 = ever have?; 1 = ever idolized?; 2 = ever max bond?; 3 = ever max level?
	total_bond INTEGER NOT NULL DEFAULT 0			-- Max total bond. To follow JP v4.0 behaviour.
);
INSERT INTO `deck_$user_id` VALUES (1, 'Team A', '0:0:0:0:0:0:0:0:0');
INSERT INTO `deck_$user_id` VALUES (2, 'Team B', '0:0:0:0:0:0:0:0:0');
INSERT INTO `deck_$user_id` VALUES (3, 'Team C', '0:0:0:0:0:0:0:0:0');
INSERT INTO `deck_$user_id` VALUES (4, 'Team D', '0:0:0:0:0:0:0:0:0');
INSERT INTO `deck_$user_id` VALUES (5, 'Team E', '0:0:0:0:0:0:0:0:0');
INSERT INTO `deck_$user_id` VALUES (6, 'Team F', '0:0:0:0:0:0:0:0:0');
INSERT INTO `deck_$user_id` VALUES (7, 'Team G', '0:0:0:0:0:0:0:0:0');
COMMIT;
QUERY
		)
	)
	{
		$update_info = [
			$invite_code,
			"present_$user_id",
			"assignment_$user_id",
			"live_$user_id",
			"unit_$user_id",
			"deck_$user_id",
			"sticker_$user_id",
			"login_bonus_$user_id",
			"album_$user_id",
			'1',
			'1',
		];
		if($DATABASE->execute_query("UPDATE `users` SET invite_code = ?, present_table = ?, assignment_table = ?, live_table = ?, unit_table = ?, deck_table = ?, sticker_table = ?, login_bonus_table = ?, album_table = ?, unlocked_badge = ?, unlocked_background = ? WHERE user_id = $user_id", 'sssssssssss', $update_info))
		{
			if(
				/* Add Bokura no LIVE Kimi to no LIVE (easy, normal, hard) */
				live_unlock($user_id, 1) &&	// easy
				live_unlock($user_id, 2) &&	// normal
				live_unlock($user_id, 3)	// hard
			)
				return true;
		}
	}
	return false;
}

/* Returns User ID or 0 if fail. Negative value means the user is banned */
function user_id_from_credentials(string $uid, string $pwd, string $tkn): int
{
	global $DATABASE;
	
	$arr = $DATABASE->execute_query('SELECT * FROM `logged_in` WHERE token = ?', 's', $tkn);
	
	if($arr && isset($arr[0]))
	{
		$user_id = $DATABASE->execute_query('SELECT user_id, locked FROM `users` WHERE login_key = ? AND login_pwd = ?', 'ss', $uid, $pwd);
		
		if($user_id && isset($user_id[0]))
		{
			if($user_id[0][1])
				return $user_id[0][0] * (-1);
			else
				return $user_id[0][0];
		}
	}
	
	return 0;
}

function user_set_last_active(string $uid, string $tkn)
{
	global $DATABASE;
	
	$unix_ts = time();
	$DATABASE->execute_query("UPDATE `users` SET last_active = $unix_ts WHERE user_id = $uid");
	$DATABASE->execute_query("UPDATE `logged_in` SET time = $unix_ts WHERE token = ?", 's', $tkn);
}

function user_exp_requirement(int $rank): int
{
	if($rank <= 0) return 0;
	
	return floor((21 + $rank ** 2.12) / 2 + 0.5);
}

function live_unlock(int $user_id, int $live_id): bool
{
	global $DATABASE;
	
	$live_table = $DATABASE->execute_query("SELECT live_table FROM `users` WHERE user_id = $user_id")[0][0];
	return $DATABASE->execute_query("INSERT INTO `$live_table` (live_id) VALUES (?)", 'i', $live_id);
}

/* It adds immediately without checking if the member is full */
function card_add_direct(int $user_id, int $card_id): int
{
	global $DATABASE;
	global $UNIX_TIMESTAMP;
	
	$next_exp = NULL;
	$max_level = 1;
	$max_hp = 1;
	$max_bond = 25;
	
	{
		$unit_db = new SQLite3Database('data/unit.db_');
		
		$temp = $unit_db->execute_query("SELECT hp_max, unit_level_up_pattern_id, normal_card_id, rank_max_card_id, before_level_max, after_level_max, before_love_max, after_love_max FROM `unit_m` WHERE unit_id = $card_id")[0];
		$is_promo = $temp[2] == $temp[3];
		$next_exp = $unit_db->execute_query("SELECT next_exp, hp_diff FROM `unit_level_up_pattern_m` WHERE unit_level_up_pattern_id = {$temp[1]} LIMIT 1")[0];
		$max_level = $is_promo ? $temp[5] : $temp[4];
		$max_hp = $temp[0] - $next_exp[1];
		$max_bond = $is_promo ? $temp[7] : $temp[6];
	}
	
	$temp = $DATABASE->execute_query("SELECT unit_table, album_table FROM `users` WHERE user_id = $user_id")[0];
	if($DATABASE->execute_query("INSERT INTO `{$temp[0]}` (card_id, next_exp, max_level, health_points, max_bond, added_time) VALUES(?, ?, ?, ?, ?, ?)", 'iiiiii', $card_id, $next_exp[0], $max_level, $max_hp, $max_bond, $UNIX_TIMESTAMP))
	{
		$unit_id = $DATABASE->execute_query('SELECT LAST_INSERT_ID()')[0][0];
		$flags = 1;
		
		if($is_promo)
			$flags = 2;
		
		$DATABASE->execute_query("INSERT OR IGNORE INTO `{$temp[1]}` VALUES (?, ?, 0)", 'ii', $card_id, $flags);
		
		return $unit_id;
	}
	else
		return 0;
}

/* Also removes it from deck */
/* Returns true if removed, false if it's in main deck */
function card_remove(int $user_id, int $unit_own_id): bool
{
	global $DATABASE;
	
	$info = $DATABASE->execute_query("SELECT unit_table, deck_table, main_deck FROM `users` WHERE user_id = $user_id")[0];
	$deck_list = [];
	
	foreach($DATABASE->execute_query("SELECT deck_num, deck_members FROM `{$info[1]}`") as $a)
	{
		$b = explode(':', $a[1]);
		$deck_list[$a[0]] = $b;
		
		foreach($b as &$unit)
		{
			if($unit == $unit_own_id)
			{
				if($info[2] == $a[0])
					/* In main deck. Cannot remove */
					return false;
				else
					/* Remove */
					$unit = 0;
			}
		}
	}
	
	foreach($deck_list as $k => $v)
		deck_alter($user_id, $k, $v);
	
	/* Last: update database */
	$DATABASE->execute_query("DELETE FROM `{$info[0]}` WHERE unit_id = $unit_own_id");
	
	return true;
}

/* Returns 0 if not in deck, 1 if in deck (but not main), 2 if in main deck */
function deck_card_in_deck(int $user_id, int $unit_id): int
{
	global $DATABASE;
	
	$data = $DATABASE->execute_query("SELECT deck_table, main_deck FROM `users` WHERE user_id = $user_id")[0];
	
	foreach($DATABASE->execute_query("SELECT deck_num, deck_members FROM `{$data[0]}`") as $deck)
	{
		foreach(explode(':', $deck[1]) as $member)
		{
			if($member == $unit_id)
				return $deck[0] == $data[1] ? 2 : 1;
		}
	}
	
	return 0;
}

/* position 0 = leftmost; position 8 = rightmost; */
function deck_alter(int $user_id, int $deck_num, array $unit_list): bool
{
	global $DATABASE;
	
	$deck_table = $DATABASE->execute_query("SELECT deck_table FROM `users` WHERE user_id = $user_id")[0][0];
	$current = explode(':', $DATABASE->execute_query("SELECT deck_members FROM `$deck_table` WHERE deck_num = $deck_num")[0][0]);
	
	foreach($unit_list as $k => $v)
	{
		if($v)
			$current[$k] = $v;
	}
	
	return $DATABASE->execute_query("UPDATE `$deck_table` SET deck_members = ? WHERE deck_num = $deck_num", 's', implode(':', $current));
}

/* returns array: score, combo, clear. clear = 0 means never played (add "new" label) */
function live_get_info(int $user_id, int $live_id): array
{
	global $DATABASE;
	
	$out = [
		'score' => 0,
		'combo' => 0,
		'clear' => 0
	];
	
	$live_table = $DATABASE->execute_query("SELECT live_table FROM `users` WHERE user_id = $user_id")[0][0];
	$data = $DATABASE->execute_query("SELECT * FROM `$live_table` WHERE live_id = $live_id");
	
	if(count($data) > 0)
	{
		$data = $data[0];
		
		$out['score'] = $data['score'];
		$out['combo'] = $data['combo'];
		$out['clear'] = $data['times'];
	}
	
	return $out;
}

/* Returns list of current daily rotation in group. */
/* The ID returned is live difficulty id or simply called "live ID" */
function live_get_current_daily(): array
{
	global $DATABASE;
	global $UNIX_TIMESTAMP;
	
	$out = [];
	$group = $DATABASE->execute_query('SELECT MAX(daily_category), MIN(daily_category) FROM `daily_rotation`')[0];
	$max_group = $group[0];
	$min_group = $group[1];
	
	for($i = $min_group; $i <= $max_group; $i++)
	{
		$current_days = intdiv($UNIX_TIMESTAMP, 86400);
		$current_rot = $DATABASE->execute_query("SELECT live_id FROM `daily_rotation` WHERE daily_category = $i");
		$out[] = $current_rot[$current_days % count($current_rot)][0];
	}
	
	return $out;
}

/* Retrieve icon user info */
/*
	name - user name
	level - user level
	badge - user badge
	unit_info
		unit_id - center unit id
		level - center unit level
		skill - leader skill
		smile - center smile
		pure - center pure
		cool - center cool
		hp - max HP
		idolized - is idolized?
		bond_max - is max bonded?
		level_max - is max leveled?

*/
function user_get_basic_info(int $user_id, SQLite3Database $unit_db): array
{
	global $DATABASE;
	
	$info = $DATABASE->execute_query("SELECT name, level, main_deck, badge_id, deck_table, unit_table FROM `users` WHERE user_id = $user_id")[0];
	$leader_own_uid = explode(':', $DATABASE->execute_query("SELECT deck_members FROM `{$info[4]}` WHERE deck_num = {$info[2]}")[0][0])[4];
	$leader_unit = $DATABASE->execute_query("SELECT card_id, level, max_level, CASE WHEN level = max_level THEN 1 ELSE 0 END, CASE WHEN bond = max_bond THEN 1 ELSE 0 END, bond, skill_level FROM `{$info[5]}` WHERE unit_id = $leader_own_uid")[0];
	$unit_info = $unit_db->execute_query("SELECT before_level_max, default_leader_skill_id, unit_level_up_pattern_id, smile_max, pure_max, cool_max, hp_max FROM `unit_m` WHERE unit_id = {$leader_unit[0]}")[0];
	$stats_diff = $unit_db->execute_query("SELECT smile_diff, pure_diff, cool_diff, hp_diff FROM `unit_level_up_pattern_m` WHERE unit_level_up_pattern_id = {$unit_info[2]} AND unit_level = {$leader_unit[1]}")[0];
	
	return [
		'name' => $info[0],
		'level' => $info[1],
		'badge' => $info[3],
		'unit_info' => [
			'unit_id' => $leader_unit[0],
			'level' => $leader_unit[1],
			'skill' => $unit_info[1] ?? 0,
			'skill_level' => $leader_unit[6],
			'smile' => $unit_info[3] - $stats_diff[0],
			'pure' => $unit_info[4] - $stats_diff[1],
			'cool' => $unit_info[5] - $stats_diff[2],
			'hp' => $unit_info[6] - $stats_diff[3],
			'bond' => $leader_unit[5],
			'idolized' => $leader_unit[2] > $unit_info[0],
			'bond_max' => !!$leader_unit[4],
			'level_max' => !!$leader_unit[3]
		]
	];
}

/* Calculate smile, pure, and cool (array in that order) */
function deck_calculate_stats_value(array $base, array $bond, int $user_leader_skill, int $guest_leader_skill = 0): array
{
	$lsk = [$user_leader_skill, $guest_leader_skill];
	$add = [0.0, 0.0, 0.0];
	
	foreach($lsk as $skill)
	{
		if($skill == 0)
			continue;
		
		if($skill >= 1 && $skill <= 9)
		{
			/* Non-cross attribute */
			$i = intdiv($skill - 1, 3);
			$add[$i] += (float)$base[$i] * 0.03 * ((($skill - 1) % 3) + 1);
		}
		else
		{
			switch($skill)
			{
				case 31:
				{
					/* Smile Angel */
					$add[0] += (float)$base[1] * 0.12;
					break;
				}
				case 32:
				{
					/* Smile Empress */
					$add[0] += (float)$base[2] * 0.12;
					break;
				}
				case 33:
				{
					/* Pure Princess */
					$add[1] += (float)$base[0] * 0.12;
					break;
				}
				case 34:
				{
					/* Pure Empress */
					$add[1] += (float)$base[2] * 0.12;
					break;
				}
				case 35:
				{
					/* Cool Princess */
					$add[2] += (float)$base[0] * 0.12;
					break;
				}
				case 36:
				{
					/* Cool Angel */
					$add[2] += (float)$base[1] * 0.12;
					break;
				}
				default:
				{
					break;
				}
			}
		}
	}
	
	$out = [];
	
	for($i = 0; $i < 3; $i++)
		$out[$i] = $base[$i] + $bond[$i] + (int)round($add[$i]);
	
	return $out;
}

function user_add_lp(int $user_id, int $amount)
{
	global $DATABASE;
	global $UNIX_TIMESTAMP;
	
	$lp_info = $DATABASE->execute_query("SELECT full_lp_recharge, overflow_lp FROM `users` WHERE user_id = $user_id")[0];
	$lp_amount_current = (int)ceil(($lp_info[0] - $UNIX_TIMESTAMP) / 360);
	
	/* If LP is already full, add to overflow LP count instead */
	if($lp_amount_current <= 0)
	{
		$lp_amount_current = 0;
		$DATABASE->execute_query("UPDATE `users` SET overflow_lp = overflow_lp + $amount WHERE user_id = $user_id");
		
		return;
	}
	
	/* Well, check if amount is enough to full charge the LP */
	$amount_time = $amount * 360;
	$time_remaining = $lp_info[0] - $amount_time;
	
	if($time_remaining < $UNIX_TIMESTAMP)
	{
		/* Some overflow occur */
		$overflow_amount = (int)ceil(($UNIX_TIMESTAMP - $time_remaining) / 360);
		$DATABASE->execute_query("UPDATE `users` SET full_lp_recharge = $UNIX_TIMESTAMP, overflow_lp = $overflow_amount WHERE user_id = $user_id");
		
		return;
	}
	
	/* Simply decrease the time */
	$DATABASE->execute_query("UPDATE `users` SET full_lp_recharge = $time_remaining WHERE user_id = $user_id");
}

function user_sub_lp(int $user_id, int $amount)
{
	global $DATABASE;
	
	$lp_info = $DATABASE->execute_query("SELECT full_lp_recharge, overflow_lp FROM `users` WHERE user_id = $user_id")[0];
	$overflow_amount = $lp_info[1] - $amount;
	
	if($overflow_amount < 0)
	{
		/* Decrease full_lp_recharge too */
		$time_recharge = $UNIX_TIMESTAMP + ($overflow_amount * (-1)) * 360;
		$DATABASE->execute_query("UPDATE `users` SET full_lp_recharge = $time_recharge, overflow_lp = 0 WHERE user_id = $user_id");
		
		return;
	}
	
	/* Simply decrease the overflow LP */
	$DATABASE->execute_query("UPDATE `users` SET overflow_lp = $overflow_amount WHERE user_id = $user_id");
}

function user_is_enough_lp(int $user_id, int $amount): bool
{
	global $DATABASE;
	global $UNIX_TIMESTAMP;
	
	$lp_info = $DATABASE->execute_query("SELECT full_lp_recharge, overflow_lp, max_lp FROM `users` WHERE user_id = $user_id")[0];
	$overflow_amount = $lp_info[1] - $amount;
	
	if($overflow_amount >= 0)
		/* Enough LP */
		return true;
	
	if(($lp_info[0] + $amount * 360 - $UNIX_TIMESTAMP) >= ($lp_info[2] * 360))
		/* Enough LP */
		return true;
	
	/* Not enough LP */
	return false;
}

/* increase user experience and returns these infos (before and after) */
/* - exp
   - level
   - max_lp
   - max_friend
*/
function user_add_exp(int $user_id, int $exp): array
{
	global $DATABASE;
	
	$before_rank_up = $DATABASE->execute_query("SELECT level, current_exp, max_lp, max_friend FROM `users` WHERE user_id = $user_id")[0];
	$current_level = $before_rank_up[0];
	$need_exp = user_exp_requirement($before_rank_up[0]);
	$now_exp = $before_rank_up[1] + $exp;
	
	while($now_exp >= $need_exp)
	{
		user_add_lp($user_id, 25 + intdiv(++$current_level, 2));
		$need_exp += user_exp_requirement($current_level);
	}
	
	$now_lp = 25 + intdiv($current_level, 2);
	$now_friend = 10 + intdiv($current_level, 5);
	
	$DATABASE->execute_query("UPDATE `users` SET level = $current_level, current_exp = $now_exp, next_exp = $need_exp, max_lp = $now_lp, max_friend = $now_friend WHERE user_id = $user_id");
	
	return [
		'before' => [
			'exp' => $before_rank_up[0],
			'level' => $before_rank_up[1],
			'max_lp' => $before_rank_up[2],
			'max_friend' => $before_rank_up[3]
		],
		'after' => [
			'exp' => $now_exp,
			'level' => $current_level,
			'max_lp' => $now_lp,
			'max_friend' => $now_friend
		]
	];
}

/* Translates item IDs to this array or NULL if fail:
	item_id
	add_type
	amount
	item_category_id
*/
function item_translate_to_array(int $item_id, int $amount = 1, int $info_id = NULL)
{
	switch($item_id)
	{
		case 1000:
		case 1001:
		{
			if($info_id === NULL)
				return NULL;
			
			return [
				'item_id' => $info_id,
				'add_type' => $item_id,
				'amount' => $amount,
				'item_category_id' => 0
			];
		}
		case 3000:
		{
			return [
				'item_id' => 3,
				'add_type' => $item_id,
				'amount' => $amount,
				'item_category_id' => 3
			];
		}
		case 3001:
		{
			return [
				'item_id' => 4,
				'add_type' => $item_id,
				'amount' => $amount,
				'item_category_id' => 4
			];
		}
		case 3002:
		{
			return [
				'item_id' => 2,
				'add_type' => $item_id,
				'amount' => $amount,
				'item_category_id' => 2
			];
		}
		case 5100:
		case 5200:
		{
			if($info_id === NULL)
				return NULL;
			
			return [
				'item_id' => $info_id,
				'add_type' => $item_id,
				'amount' => 1,
				'item_category_id' => 0
			];
		}
		default:
		{
			return NULL;
		}
	}
}

/* item_data must be array with these values:
	message - Present message. Defaults to "Present Box Item" (optional field)
	expire - unix timestamp when the item is expired or NULL (optional field; default to NULL)
	
returns item_incentive_id
*/
function item_add_present_box(int $user_id, int $item_id, array $item_data = [], int $amount = 1, int $info_id = NULL): int
{
	global $DATABASE;
	
	$present_table = $DATABASE->execute_query("SELECT present_table FROM `users` WHERE user_id = $user_id")[0][0];
	$data = [
		$item_id,
		$info_id,
		$amount,
		$item_data['message'] ?? 'Present Box Item',
		$item_data['expire'] ?? NULL
	];
	
	$DATABASE->execute_query("INSERT INTO `$present_table` (item_type, card_num, amount, message, expire) VALUES (?, ?, ?, ?, ?)", 'iiisi', $data);
	return $DATABASE->execute_query('SELECT LAST_INSERT_ID()')[0][0];
}

function item_default_expiration(int $item_id, int $info_id = NULL)
{
	global $UNIX_TIMESTAMP;
	
	switch($item_id)
	{
		case 3000:
		case 3002:
		{
			return $UNIX_TIMESTAMP + 5184000;	// 60 days
		}
		default:
		{
			return NULL;
		}
	}
}

/* Add card (returns unit_own_user_id) or insert it to present box (returns 0) if member slot is full */
function card_add(int $user_id, int $card_id, array $item_data = []): int
{
	global $DATABASE;
	
	$user_unit_info = $DATABASE->execute_query("SELECT unit_table, max_unit FROM `users` WHERE user_id = $user_id")[0];
	$unit_current = $DATABASE->execute_query("SELECT COUNT(unit_id) FROM `{$user_unit_info[0]}`")[0][0];
	
	if($unit_current >= $user_unit_info[1])
	{
		item_add_present_box($user_id, 1001, $item_data, 1, $card_id);
		return 0;
	}
	
	return card_add_direct($user_id, $card_id);
}

/* Is player can do free gacha? */
function user_is_free_gacha(int $user_id): bool
{
	global $DATABASE;
	global $UNIX_TIMESTAMP;
	
	$temp = $DATABASE->execute_query("SELECT next_free_gacha FROM `free_gacha_tracking` WHERE user_id = $user_id");
	
	return count($temp) == 0 ? true : $temp[0][0] >= $UNIX_TIMESTAMP;
}

/* Set "free gacha" flag to false */
function user_disable_free_gacha(int $user_id)
{
	global $DATABASE;
	global $UNIX_TIMESTAMP;
	
	$DATABASE->execute_query('INSERT OR IGNORE INTO `free_gacha_tracking` VALUES (?, ?)', 'ii', $user_id, $UNIX_TIMESTAMP - ($UNIX_TIMESTAMP % 86400) + 86400);
}

function user_get_free_gacha_timestamp(int $user_id): int
{
	global $DATABASE;
	
	$temp = $DATABASE->execute_query("SELECT next_free_gacha FROM `free_gacha_tracking` WHERE user_id = $user_id");
	
	return count($temp) == 0 ? 0 : $temp[0][0];
}

function user_get_gauge(int $user_id): int
{
	global $DATABASE;
	
	$temp = $DATABASE->execute_query("SELECT gauge FROM `secretbox_gauge` WHERE user_id = $user_id");
	
	return count($temp) == 0 ? 0 : $temp[0][0] * 10;
}

/* returns cycle how many times it already beyond 100 */
function user_increase_gauge(int $user_id, int $amount = 1): int
{
	global $DATABASE;
	
	$temp = user_get_gauge($user_id) + $amount;
	$cycle = 0;
	
	for(; $temp >= 10; $temp -= 10)
		$cycle++;
	
	$DATABASE->execute_query('REPLACE INTO `secretbox_gauge` VALUES(?, ?)', 'ii', $user_id, $temp);
	return $cycle;
}

function token_use_pseudo_unit_own_id(string $token): int
{
	global $DATABASE;
	
	$pseudo_curnum = $DATABASE->execute_query('SELECT pseudo_unit_own_id FROM `logged_in` WHERE token = ?', 's', $token)[0][0];
	$DATABASE->execute_query('UPDATE `logged_in` SET pseudo_unit_own_id = pseudo_unit_own_id - 1 WHERE token = ?', 's', $token);
	return $pseudo_curnum;
}

/* returns true if success, false if not enough loveca */
function user_sub_loveca(int $user_id, int $amount): bool
{
	$DATABASE = $GLOBALS['DATABASE'];
	
	$loveca = $DATABASE->execute_query("SELECT paid_loveca, free_loveca FROM `users` WHERE user_id = $user_id")[0];
	
	if($loveca['paid_loveca'] >= $amount)
		$loveca['paid_loveca'] -= $amount;
	else
		if($loveca['free_loveca'] + $loveca['paid_loveca'] >= $amount)
		{
			$loveca['free_loveca'] -= $amount - $loveca['paid_loveca'];
			$loveca['paid_loveca'] = 0;
		}
		else
			return false;
	
	return !!$DATABASE->execute_query('UPDATE `users` SET paid_loveca = ?, free_loveca = ? WHERE user_id = ?', 'iii', $loveca['paid_loveca'], $loveca['free_loveca'], $user_id);
}

<?php
/*
 * Null Pointer Private server
 * User-related common functions
 */

/* Creates new user. For /login/startUp */
/* Returns the User ID or 0 if fail */
function user_create(string $key, string $pwd): int
{
	global $UNIX_TIMESTAMP;
	
	$unix_ts = $UNIX_TIMESTAMP;
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
	if(npps_query('INSERT INTO `users` (login_key, login_pwd, create_date, last_active, next_exp, full_lp_recharge, present_table, assignment_table, live_table, unit_table, deck_table, friend_list, sticker_table, login_bonus_table, album_table, unlocked_badge, unlocked_background) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "", "")', 'ssiiiisssssssss', $user_data))
	{
		$user_id = npps_query('SELECT LAST_INSERT_ID()');
		
		if($user_id)
			return $user_id[0][0];
	}
	return 0;
}

/* Configure user. It supports startWithoutInvite and startSetInvite */
/* Returns true on success, false on failure */
function user_configure(int $user_id, string $invite_code = NULL): bool
{
	if($invite_code == NULL)
	{
		do
		{
			$invite_code = sprintf('%09d', random_int(0,999999999));
		}
		while(count(npps_query('SELECT user_id FROM `users` WHERE invite_code = ?', 's', $invite_code)) > 0);
	}
	else
		if(count(npps_query('SELECT user_id FROM `users` WHERE invite_code = ?', 's', $invite_code)) > 0)
			return false;
	
	// Create users table
	if(
		npps_query(<<<QUERY
BEGIN;
CREATE TABLE `present_$user_id` (
	item_pos INTEGER PRIMARY KEY AUTO_INCREMENT,	-- The item position
	item_type INTEGER NOT NULL,						-- The item type ID
	card_num INTEGER,								-- The card internal ID (can be other ID) or NULL.
	amount INTEGER NOT NULL,						-- Amount of the item
	message TEXT NOT NULL,							-- Additional message like: "Event achievement reward"
	expire INTEGER DEFAULT NULL,					-- Unix timestamp when the item expire or NULL for no expiration
	collected INTEGER DEFAULT NULL					-- Unix timestamp for when the item was collected or NULL for not collected
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
		if(npps_query("UPDATE `users` SET invite_code = ?, present_table = ?, assignment_table = ?, live_table = ?, unit_table = ?, deck_table = ?, sticker_table = ?, login_bonus_table = ?, album_table = ?, unlocked_badge = ?, unlocked_background = ? WHERE user_id = $user_id", 'sssssssssss', $update_info))
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
	$arr = npps_query('SELECT * FROM `logged_in` WHERE token = ?', 's', $tkn);
	
	if($arr && isset($arr[0]))
	{
		$user_id = npps_query('SELECT user_id, locked FROM `users` WHERE login_key = ? AND login_pwd = ?', 'ss', $uid, $pwd);
		
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

/* Returns current user information */
function user_current_info(int $user_id): array
{
	global $UNIX_TIMESTAMP;
	
	$user_info = npps_query(<<<QUERY
	SELECT
		name,
		level,
		current_exp,
		next_exp,
		gold,
		paid_loveca,
		free_loveca,
		friend_point,
		max_unit,
		max_lp,
		full_lp_recharge,
		overflow_lp,
		max_friend,
		invite_code,
		tutorial_state
	FROM `users` WHERE user_id = $user_id
QUERY
	)[0];

	$lp_time_charge = $user_info[10] - $UNIX_TIMESTAMP;
	$total_lp = intdiv($lp_time_charge, 360);

	if($lp_time_charge < 0 || $user_info[11] > 0)
	{
		$lp_time_charge = 0;
		$total_lp = $user_info[9];
	}

	$total_lp += $user_info[11];

	return [
		"user_id" => $user_id,
		"name" => $user_info[0],												// name
		"level" => $user_info[1],												// level
		"exp" => $user_info[2],													// current_exp
		"previous_exp" => $user_info[3] - user_exp_requirement($user_info[1]),	// next_exp - level
		"next_exp" => $user_info[3],											// next_exp
		"game_coin" => $user_info[4],											// gold
		"sns_coin" => $user_info[5] + $user_info[6],							// paid_loveca + free_loveca
		"paid_sns_coin" => $user_info[5],										// paid_loveca
		"free_sns_coin" => $user_info[6],										// free_loveca
		"social_point" => $user_info[7],										// friend_point
		"unit_max" => $user_info[8],											// max_unit
		"energy_max" => $user_info[9],											// max_lp
		"energy_full_time" => to_datetime($user_info[10]),						// full_lp_recharge
		"energy_full_need_time" => $lp_time_charge,								// full_lp_recharge - time()
		"over_max_energy" => $total_lp,											// overflow_lp
		"friend_max" => $user_info[12],											// max_friend
		"invite_code" => $user_info[13],										// invite_code
		"tutorial_state" => $user_info[14]										// tutorial_state
	];
}

function user_set_last_active(int $uid, string $tkn)
{
	global $UNIX_TIMESTAMP;
	
	npps_query("UPDATE `users` SET last_active = $UNIX_TIMESTAMP WHERE user_id = $uid");
	npps_query("UPDATE `logged_in` SET time = $UNIX_TIMESTAMP WHERE token = ?", 's', $tkn);
}

function user_exp_requirement(int $rank): int
{
	if($rank <= 0) return 0;
	
	return intval(floor((21 + $rank ** 2.12) / 2 + 0.5));
}

function user_exp_requirement_recursive(int $rank): int
{
	if($rank <= 0) return 0;
	
	$sum = 0;
	
	for($i = 1; $i <= $rank; $i++)
		$sum += user_exp_requirement($i);
	
	return $sum;
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
function user_get_basic_info(int $user_id): array
{
	$unit_db = npps_get_database('unit');
	
	$info = npps_query("SELECT name, level, main_deck, badge_id, deck_table, unit_table FROM `users` WHERE user_id = $user_id")[0];
	$leader_own_uid = explode(':', npps_query("SELECT deck_members FROM `{$info[4]}` WHERE deck_num = {$info[2]}")[0][0])[4];
	$leader_unit = npps_query("SELECT card_id, level, max_level, CASE WHEN level = max_level THEN 1 ELSE 0 END, CASE WHEN bond = max_bond THEN 1 ELSE 0 END, bond, skill_level FROM `{$info[5]}` WHERE unit_id = $leader_own_uid")[0];
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

function user_add_lp(int $user_id, int $amount)
{
	global $UNIX_TIMESTAMP;
	
	$lp_info = npps_query("SELECT full_lp_recharge, overflow_lp FROM `users` WHERE user_id = $user_id")[0];
	$lp_amount_current = (int)ceil(($lp_info[0] - $UNIX_TIMESTAMP) / 360);
	
	/* If LP is already full, add to overflow LP count instead */
	if($lp_amount_current <= 0)
	{
		$lp_amount_current = 0;
		npps_query("UPDATE `users` SET overflow_lp = overflow_lp + $amount WHERE user_id = $user_id");
		
		return;
	}
	
	/* Well, check if amount is enough to full charge the LP */
	$amount_time = $amount * 360;
	$time_remaining = $lp_info[0] - $amount_time;
	
	if($time_remaining < $UNIX_TIMESTAMP)
	{
		/* Some overflow occur */
		$overflow_amount = (int)ceil(($UNIX_TIMESTAMP - $time_remaining) / 360);
		npps_query("UPDATE `users` SET full_lp_recharge = $UNIX_TIMESTAMP, overflow_lp = $overflow_amount WHERE user_id = $user_id");
		
		return;
	}
	
	/* Simply decrease the time */
	npps_query("UPDATE `users` SET full_lp_recharge = $time_remaining WHERE user_id = $user_id");
}

function user_sub_lp(int $user_id, int $amount)
{
	$lp_info = npps_query("SELECT full_lp_recharge, overflow_lp FROM `users` WHERE user_id = $user_id")[0];
	$overflow_amount = $lp_info[1] - $amount;
	
	if($overflow_amount < 0)
	{
		/* Decrease full_lp_recharge too */
		$time_recharge = $UNIX_TIMESTAMP + ($overflow_amount * (-1)) * 360;
		npps_query("UPDATE `users` SET full_lp_recharge = $time_recharge, overflow_lp = 0 WHERE user_id = $user_id");
		
		return;
	}
	
	/* Simply decrease the overflow LP */
	npps_query("UPDATE `users` SET overflow_lp = $overflow_amount WHERE user_id = $user_id");
}

function user_is_enough_lp(int $user_id, int $amount): bool
{
	global $UNIX_TIMESTAMP;
	
	$lp_info = npps_query("SELECT full_lp_recharge, overflow_lp, max_lp FROM `users` WHERE user_id = $user_id")[0];
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
	$before_rank_up = npps_query("SELECT level, current_exp, max_lp, max_friend FROM `users` WHERE user_id = $user_id")[0];
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
	
	npps_query("UPDATE `users` SET level = $current_level, current_exp = $now_exp, next_exp = $need_exp, max_lp = $now_lp, max_friend = $now_friend WHERE user_id = $user_id");
	
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

/* Is player can do free gacha? */
function user_is_free_gacha(int $user_id): bool
{
	global $UNIX_TIMESTAMP;
	
	$temp = npps_query("SELECT next_free_gacha FROM `free_gacha_tracking` WHERE user_id = $user_id");
	
	return count($temp) == 0 ? true : $temp[0][0] >= $UNIX_TIMESTAMP;
}

/* Set "free gacha" flag to false */
function user_disable_free_gacha(int $user_id)
{
	global $DATABASE;
	global $UNIX_TIMESTAMP;
	
	npps_query('INSERT OR IGNORE INTO `free_gacha_tracking` VALUES (?, ?)', 'ii', $user_id, $UNIX_TIMESTAMP - ($UNIX_TIMESTAMP % 86400) + 86400);
}

function user_get_free_gacha_timestamp(int $user_id): int
{
	$temp = npps_query("SELECT next_free_gacha FROM `free_gacha_tracking` WHERE user_id = $user_id");
	
	return count($temp) == 0 ? 0 : $temp[0][0];
}

function user_get_gauge(int $user_id, bool $unmul = false): int
{
	$temp = npps_query("SELECT gauge FROM `secretbox_gauge` WHERE user_id = $user_id");
	
	return count($temp) == 0 ? 0 : ($unmul ? $temp[0][0] : $temp[0][0] * 10);
}

/* returns cycle how many times it already beyond 100 */
function user_increase_gauge(int $user_id, int $amount = 1): int
{
	$temp = user_get_gauge($user_id, true) + $amount;
	$cycle = 0;
	
	for(; $temp >= 10; $temp -= 10)
		$cycle++;
	
	npps_query('REPLACE INTO `secretbox_gauge` VALUES(?, ?)', 'ii', $user_id, $temp);
	return $cycle;
}

/* returns true if success, false if not enough loveca */
function user_sub_loveca(int $user_id, int $amount): bool
{
	$loveca = npps_query("SELECT paid_loveca, free_loveca FROM `users` WHERE user_id = $user_id")[0];
	
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
	
	return !!npps_query('UPDATE `users` SET paid_loveca = ?, free_loveca = ? WHERE user_id = ?', 'iii', $loveca['paid_loveca'], $loveca['free_loveca'], $user_id);
}

/* returns the subscenario id, 0 if already unlocked, -1 on fail */
function user_subscenario_unlock(int $user_id, int $unit_id): int
{
	$subscenario_db = npps_get_database('subscenario');
	$subscenario_tracking = npps_query("SELECT subscenario_tracking FROM `users` WHERE user_id = $user_id")[0]['subscenario_tracking'];
	$subscenario_data = strlen($subscenario_tracking) > 0 ? explode(',', $subscenario_tracking) : [];
	$subscenario_id = $subscenario_db->execute_query("SELECT subscenario_id FROM `subscenario_m` WHERE unit_id = $unit_id");
	
	if(count($subscenario_id) > 0)
		$subscenario_id = $subscenario_id[0]['subscenario_id'];
	else
		return -1;
	
	$is_unlocked = false;
	array_walk($subscenario_data, function($v, $k) use($subscenario_id, &$is_unlocked)
		{
			if($is_unlocked == false && (strcmp(strval($v), strval($subscenario_id)) == 0 || strcmp(strval($v), "!$subscenario_id") == 0))
				$is_unlocked = true;
		}
	);
	
	if($is_unlocked)
		return 0;
	
	$subscenario_data[] = strval($subscenario_id);
	npps_query("UPDATE `users` SET subscenario_tracking = ? WHERE user_id = $user_id",
		's', implode(',', $subscenario_data));
	
	return $subscenario_id;
}

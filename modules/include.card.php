<?php
/*
 * Null Pointer Private Server
 * Card addition and removal
 */

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
	
	$temp = npps_query("SELECT unit_table, album_table FROM `users` WHERE user_id = $user_id")[0];
	if(npps_query("INSERT INTO `{$temp[0]}` (card_id, next_exp, max_level, health_points, max_bond, added_time) VALUES(?, ?, ?, ?, ?, ?)", 'iiiiii', $card_id, $next_exp[0], $max_level, $max_hp, $max_bond, $UNIX_TIMESTAMP))
	{
		$unit_id = npps_query('SELECT LAST_INSERT_ID()')[0][0];
		$flags = 1;
		
		if($is_promo)
			$flags = 2;
		
		npps_query("INSERT OR IGNORE INTO `{$temp[1]}` VALUES (?, ?, 0)", 'ii', $card_id, $flags);
		
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
	
	$info = npps_query("SELECT unit_table, deck_table, main_deck FROM `users` WHERE user_id = $user_id")[0];
	$deck_list = [];
	
	foreach(npps_query("SELECT deck_num, deck_members FROM `{$info[1]}`") as $a)
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
	npps_query("DELETE FROM `{$info[0]}` WHERE unit_id = $unit_own_id");
	
	return true;
}

/* Add card (returns unit_own_user_id) or insert it to present box (returns 0) if member slot is full */
function card_add(int $user_id, int $card_id, array $item_data = []): int
{
	global $DATABASE;
	
	$user_unit_info = npps_query("SELECT unit_table, max_unit FROM `users` WHERE user_id = $user_id")[0];
	$unit_current = npps_query("SELECT COUNT(unit_id) FROM `{$user_unit_info[0]}`")[0][0];
	
	if($unit_current >= $user_unit_info[1])
	{
		item_add_present_box($user_id, 1001, $item_data, 1, $card_id);
		return 0;
	}
	
	return card_add_direct($user_id, $card_id);
}

/* To give player card rewards after completing live or for regular scouting */
function card_random_regular(): array
{
	static $n_list = [];
	static $r_list = [];
	static $data_initialized = false;
	
	if($data_initialized == false)
	{
		$unit_db = npps_get_database('unit');
		
		foreach($unit_db->execute_query('SELECT unit_id, unit_level_up_pattern_id, hp_max, rarity, before_love_max, before_level_max FROM `unit_m` WHERE rarity < 3 AND normal_card_id <> rank_max_card_id') as $x)
		{
			$level_up_pattern = $unit_db->execute_query("SELECT next_exp, hp_diff FROM `unit_level_up_pattern_m` WHERE unit_level_up_pattern_id = {$x['unit_level_up_pattern_id']}")[0];
			
			$unit_data = [
				'unit_owning_user_id' => 0,
				'unit_id' => $x['unit_id'],
				'exp' => 0,
				'next_exp' => $level_up_pattern['next_exp'],
				'level' => 1,
				'max_level' => $x['before_level_max'],
				'rank' => 1,
				'max_rank' => 2,
				'love' => 0,
				'max_love' => $x['before_love_max'],
				'skill_level' => 1,
				'max_hp' => $x['hp_max'] - $level_up_pattern['hp_diff'],
				'is_rank_max' => false,
				'is_love_max' => false,
				'is_level_max' => false
			];
			
			switch($x['rarity'])
			{
				case 1:
				{
					$n_list[] = $unit_data;
					break;
				}
				case 2:
				{
					$r_list[] = $unit_data;
					break;
				}
				default: break;
			}
		}
		
		$data_initialized = true;
	}
	
	// 10% R, 90% N
	if(random_int(0, 100000) / 1000 - 90.0 <= 0.0)
		return $r_list[random_int(0, count($r_list) - 1)];
	else
		return $n_list[random_int(0, count($n_list) - 1)];
}

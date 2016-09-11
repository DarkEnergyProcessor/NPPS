<?php
$unit_db = npps_get_database('unit');
$some_tables = npps_query("SELECT unit_table, deck_table, gold, level, current_exp, next_exp, paid_loveca + free_loveca, friend_point, max_unit, max_lp, max_friend, album_table FROM `users` WHERE user_id = $USER_ID")[0];
$unit_table = $some_tables[0];
$deck_table = $some_tables[1];
$album_table = $some_tables[11];

$practice_list = $REQUEST_DATA['unit_owning_user_ids'] ?? [];
$practice_base = intval($REQUEST_DATA['base_owning_unit_user_id'] ?? 0);

if(count($practice_list) == 0 || $practice_base == 0)
{
	echo 'No practice target';
	return false;
}

$base_before = npps_query("SELECT * FROM `$unit_table` WHERE unit_id = $practice_base")[0];
$base_rarity = 1;
$base_idolized = false;
$base_promo = false;
$base_is_supprot_card = false;
$base_max_level = 30;
$base_attribute = 0;
$base_pattern = 0;
$base_skill = [$base_before[6], $base_before[7]];
$base_skill_id = 0;
$base_hp = $base_before[8];
$base_hp_max = $base_hp;

$total_gained_exp = 0;
$total_skill_gained = 0;
$needed_gold = 0;
$seal_gained = [0, 0, 0];

/* Get base card info */
{
	$uid = $base_before[1];
	$temp = $unit_db->execute_query("SELECT attribute_id, after_level_max, unit_level_up_pattern_id, rarity, default_unit_skill_id, normal_card_id = rank_max_card_id, disable_rank_up, hp_max FROM `unit_m` WHERE unit_id = $uid")[0];
	
	$base_promo = $temp[5] > 0;
	$base_max_level = $base_before[5];
	$base_attribute = $temp[0];
	$base_idolized = $base_max_level >= $temp[1];
	$base_pattern = $temp[2];
	$base_rarity = $temp[3];
	$base_skill_id = $temp[4] ?? 0;
	$base_is_support_card = $temp[6] > 0;
	$base_hp_max = $temp[7];
}

foreach($practice_list as $used_own_id)
{
	if($used_own_id == $practice_base || $used_own_id == 0)
	{
		echo 'Invalid member ID';
		return false;
	}
	
	if(deck_card_in_deck($USER_ID, $used_own_id) == 2)
	{
		echo 'Practice list is in main deck!';
		return false;
	}
	
	$unit_id = npps_query("SELECT card_id, level, max_level FROM `$unit_table` WHERE unit_id = ?", 'i', $used_own_id)[0];
	$unit_info = $unit_db->execute_query("SELECT unit_level_up_pattern_id, attribute_id, rarity, default_unit_skill_id, normal_card_id, rank_max_card_id, disable_rank_up, before_level_max FROM `unit_m` WHERE unit_id = {$unit_id[0]}")[0];
	$merge_info = $unit_db->execute_query("SELECT merge_exp, merge_cost FROM `unit_level_up_pattern_m` WHERE unit_level_up_pattern_id = {$unit_info[0]} AND unit_level == {$unit_id[1]}")[0];
	$unit_idolized = $unit_id[2] > $unit_info[7];
	
	/* 20% add if same attribute */
	$total_gained_exp += $unit_info[1] == $base_attribute ? intval((float)$merge_info[0] * 1.2) : $merge_info[0];
	$needed_gold += $merge_info[1];
	
	/* Check if it's possible to increase skill level */
	if($base_skill_id > 0 && $unit_info[3] > 0 && $base_before[6] < 8 && $base_rarity > 1)
		if(
			/* Same Skill */
			$unit_info[3] == $base_skill_id || 
			/* Rare support cards */
			($base_rarity == 2 && (
				($unit_id[0] == 379 && $base_attribute == 1) || /* Cocoa Yazawa, R Smile */
				($unit_id[0] == 380 && $base_attribute == 2) || /* Cotaro Yazawa, R Pure */
				($unit_id[0] == 381 && $base_attribute == 3)    /* Cocoro Yazawa, R Cool */
			)) ||
			/* SR support cards */
			($base_rarity == 3 && (
				($unit_id[0] == 383 && $base_attribute == 1) || /* Mika, SR Smile */
				($unit_id[0] == 384 && $base_attribute == 2) || /* Fumiko, SR Pure */
				($unit_id[0] == 385 && $base_attribute == 3) || /* Hideko, SR Cool */
				$unit_id[0] == 386								/* Hiroko Yamada, SR all attributes */
			)) ||
			/* UR support cards */
			($base_rarity == 3 && (
				($unit_id[0] == 387 && $base_attribute == 1) ||	/* Nico's mother, UR Smile */
				($unit_id[0] == 388 && $base_attribute == 2) || /* Kotori's mother, UR Pure */
				($unit_id[0] == 389 && $base_attribute == 3) ||	/* Maki's mother, UR Cool */
				$unit_id[0] == 390								/* Honoka's mother, UR all attributes */
			))
		)
			$total_skill_gained++;	// Increase skill EXP
	
	/* Check if it's possible to get seal */
	if($base_rarity > 1)
	{
		if($unit_info[4] == $unit_info[5])
			/* Is promo card. Give pink seal regardless of type */
			$seal_gained[0]++;
		else
		{
			/* Support card or normal card */
			$seal_index = &$seal_gained[$unit_info[2] - 2];
			
			if($unit_info[6] == 0 && $unit_idolized)
				/* Idolized. Not support card */
				$seal_index += 2;
			else
				/* Not idolized/support card */
				$seal_index++;
		}
	}
}

if($some_tables[2] < $needed_gold)
{
	echo 'Now enough money!';
	return false;
}

foreach($practice_list as $practice_unit_id)
	card_remove($USER_ID, intval($practice_unit_id));

/* Deduce player money and add seals*/
npps_query("UPDATE `users` SET gold = gold - $needed_gold, normal_sticker = normal_sticker + {$seal_gained[0]}, silver_sticker = silver_sticker + {$seal_gained[1]}, gold_sticker = gold_sticker + {$seal_gained[2]} WHERE user_id = $USER_ID");

/* Calculate Super Success or Ultra Success */
$practice_bonus = 1;
$practice_chance = random_int(0, 100000) / 1000;
$bonus_value = 1;

if($practice_chance <= 2.0)
{
	$practice_bonus = 3;
	$total_gained_exp *= 2;
	$bonus_value = 2;
}
else if($practice_chance <= 8.0)
{
	$practice_bonus = 2;
	$total_gained_exp = intval((float)$total_gained_exp * 1.5);
	$bonus_value = 1.5;
}
/* No need else for normal Success */

/* Get card next_exp */
$new_hp = $base_hp_max;
$next_exp = 0;
$limit_exp = 2147483647; // No limit
$new_card_level = $base_before[4];
{
	$temp = $unit_db->execute_query("SELECT unit_level, next_exp, hp_diff FROM `unit_level_up_pattern_m` WHERE unit_level_up_pattern_id = $base_pattern AND next_exp > ? LIMIT 1", 'i', $total_gained_exp + $base_before[2]);
	$limit_exp_temp_data = $unit_db->execute_query("SELECT unit_level, next_exp FROM `unit_level_up_pattern_m` WHERE unit_level_up_pattern_id = $base_pattern ORDER BY unit_level DESC LIMIT 2");
	
	$limit_exp = $limit_exp_temp_data[1][1];
	$next_exp = $limit_exp_temp_data[0][1];
	$new_card_level = $limit_exp_temp_data[0][0];
	
	if(count($temp) > 0)
	{
		$new_hp -= $temp[0][2];
		$new_card_level = $temp[0][0];
		$next_exp = $temp[0][1];
	}
}

/* Get new skill level and EXP */
$skill_level_exp = $total_skill_gained + $base_before[7];
$new_skill_level = $base_before[6];

while($skill_level_exp >= 2 ** ($new_skill_level - 1))
	$skill_level_exp -= $new_skill_level++;

if($new_skill_level >= 8)
{
	$new_skill_level = 8;
	$skill_level_exp = 0;
}

$new_max_level = $new_card_level >= $base_max_level;
$new_after_exp = $new_max_level ? $limit_exp : $base_before[2] + $total_gained_exp;

/* Update card */
npps_query("UPDATE `$unit_table` SET level = ?, current_exp = ?, next_exp = ?, skill_level = ?, skill_level_exp = ?, health_points = ? WHERE unit_id = $practice_base", 'iiiiii', $new_card_level, $new_after_exp, $next_exp, $new_skill_level, $skill_level_exp, $new_hp);

/* Update album if max level */
if($new_max_level && $base_idolized)
{
	$temp_album_data = npps_query("SELECT flags FROM `$album_table` WHERE card_id = {$base_before[1]}")[0][0];
	
	if(($temp_album_data & 8) == 0)
		npps_query("UPDATE `$album_table` SET flags = ? WHERE card_id = ?", 'ii', $temp_album_data | 8, $base_before[1]);
}

/* Send response seal */
$seal_list = [];
foreach($seal_gained as $k => $v)
{
	if($v > 0)
		$seal_list[] = [
			'rarity' => $k + 2,
			'exchange_point' => $v
		];
}

user_set_last_active($USER_ID, $TOKEN);

/* Out */
return [
	[
		'before' => [
			'unit_owning_user_id' => $practice_base,
			'unit_id' => $base_before[1],
			'exp' => $base_before[2],
			'next_exp' => $base_before[3],
			'level' => $base_before[4],
			'max_level' => $base_before[5],
			'rank' => $base_promo || $base_idolized ? 2 : 1,
			'max_rank' => 2,
			'love' => $base_before[9],
			'max_love' => $base_before[10],
			'unit_skill_level' => $base_before[6],
			'max_hp' => $base_before[8],
			'favorite_flag' => $base_before[11],
			'is_rank_max' => $base_idolized,
			'is_love_max' => $base_idolized ? $base_before[9] >= $base_before[10] : false,
			'is_level_max' => $base_idolized ? $base_before[4] >= $base_before[5] : false
		],
		'after' => [
			'unit_owning_user_id' => $practice_base,
			'unit_id' => $base_before[1],
			'exp' => $new_after_exp,
			'next_exp' => $next_exp,
			'level' => $new_card_level,
			'max_level' => $base_before[5],
			'rank' => $base_promo || $base_idolized ? 2 : 1,
			'max_rank' => 2,
			'love' => $base_before[9],
			'max_love' => $base_before[10],
			'unit_skill_level' => $new_skill_level,
			'max_hp' => $new_hp,
			'favorite_flag' => $base_before[11],
			'is_rank_max' => $base_idolized,
			'is_love_max' => $base_idolized ? $base_before[9] >= $base_before[10] : false,
			'is_level_max' => $base_idolized ? $new_card_level >= $base_before[5] : false
		],
		'before_user_info' => [
			'level' => $some_tables[3],
			'exp' => $some_tables[4],
			'next_exp' => $some_tables[5],
			'game_coin' => $some_tables[2],
			'sns_coin' => $some_tables[6],
			'social_point' => $some_tables[7],
			'unit_max' => $some_tables[8],
			'energy_max' => $some_tables[9],
			'friend_max' => $some_tables[10]
		],
		'after_user_info' => [
			'level' => $some_tables[3],
			'exp' => $some_tables[4],
			'next_exp' => $some_tables[5],
			'game_coin' => $some_tables[2] - $needed_gold,
			'sns_coin' => $some_tables[6],
			'social_point' => $some_tables[7],
			'unit_max' => $some_tables[8],
			'energy_max' => $some_tables[9],
			'friend_max' => $some_tables[10]
		],
		'use_game_coin' => $needed_gold,
		'evolution_setting_id' => $practice_bonus,
		'bonus_value' => $bonus_value,
		'open_subscenario_id' => NULL,
		'get_exchange_point_list' => $seal_list
	],
	200
];

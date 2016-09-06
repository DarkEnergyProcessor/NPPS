<?php
$target_user_id = intval($REQUEST_DATA['user_id']);

if($target_user_id == 0)
{
	echo 'Invalid user ID';
	return false;
}

$unit_db = npps_get_database('unit');
$unit_table = NULL;
$deck_table = NULL;
$main_deck = 0;
$leader_unit_own_id = 0;

{
	$temp = $DATABASE->execute_query("SELECT unit_table, deck_table, main_deck FROM `users` WHERE user_id = $target_user_id")[0];
	
	$unit_table = $temp[0];
	$deck_table = $temp[1];
	$main_deck = $temp[2];
	$leader_unit_own_id = intval(explode(':', $DATABASE->execute_query("SELECT deck_members FROM `$deck_table` WHERE deck_num = $main_deck")[0][0])[4]);
}

$unit_out = NULL;
$is_friend = false;

{
	$unit = $DATABASE->execute_query("SELECT * FROM `$unit_table` WHERE unit_id = $leader_unit_own_id")[0];
	$rarity = $unit_db->execute_query("SELECT rarity FROM `unit_m` WHERE unit_id = {$unit[1]}")[0][0];
	$is_promo = count($unit_db->execute_query("SELECT unit_id FROM `unit_m` WHERE unit_id = {$unit[1]} AND normal_card_id = rank_max_card_id")) > 0;
	$is_idolized = $unit_db->execute_query("SELECT after_level_max FROM `unit_m` WHERE unit_id = {$unit[1]}")[0][0] == $unit[5];
	
	$unit_out = [
		'unit_owning_user_id' => token_use_pseudo_unit_own_id($TOKEN),
		'unit_id' => $unit[1],
		'exp' => $unit[2],
		'next_exp' => $unit[3],
		'level' => $unit[4],
		'max_level' => $unit[5],
		'rank' => $is_promo || $is_idolized ? 2 : 1,
		'max_rank' => 2,
		'love' => $unit[9],
		'max_love' => $unit[10],
		'unit_skill_level' => $unit[6],
		'max_hp' => $unit[8],
		'is_rank_max' => $is_idolized,
		'favorite_flag' => $unit[11] > 0,
		'is_love_max' => $is_idolized ? $unit[9] >= $unit[10] : false,
		'is_level_max' => $is_idolized ? $unit[4] >= $unit[5] : false,
		'is_skill_level_max' => $rarity > 1 ? $unit[6] >= 8 : true,
		'insert_date' => to_datetime($unit[12])
	];
	
	if($target_user_id != $USER_ID)
	{
		$friend_list = explode(',', $DATABASE->execute_query("SELECT friend_list FROM `users` WHERE user_id = $USER_ID")[0][0]) ?? [];
		
		foreach($friend_list as $id)
		{
			if($id == $target_user_id)
			{
				$is_friend = true;
				break;
			}
		}
	}
}

user_set_last_active($USER_ID, $TOKEN);

$target_info = $DATABASE->execute_query("SELECT name, level, max_unit, max_lp, max_friend, unit_table, last_active, bio, badge_id, background_id, invite_code FROM `users` WHERE user_id = $target_user_id")[0];

return [
	[
		'user_info' => [
			'user_id' => $target_user_id,
			'name' => $target_info[0],
			'level' => $target_info[1],
			'cost_max' => 100,			// TODO
			'unit_max' => $target_info[2],
			'energy_max' => $target_info[3],
			'friend_max' => $target_info[4],
			'cost' => 0,
			'unit_cnt' => $DATABASE->execute_query("SELECT COUNT(unit_id) FROM `{$target_info[5]}`")[0][0],
			'invite_code' => $target_info[10],
			'elapsed_time_from_login' => time_elapsed_string($target_info[6]),
			'introduction' => $target_info[7]
		],
		'center_unit_info' => $unit_out,
		'is_alliance' => $is_friend,
		'friend_status' => $is_friend ? 1 : 0,
		'setting_award_id' => $target_info[8],
		'setting_background_id' => $target_info[9]
	],
	200
];
?>
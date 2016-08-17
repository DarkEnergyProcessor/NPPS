<?php
$cards = [];
$scout_info = $DATABASE->execute_query('SELECT * FROM `secretbox_list` WHERE id = ?', 'i', $secretbox_id)[0];

if($before_user_info[0]['user']['sns_coin'] < $scout_info['loveca_single_cost'])
{
	echo 'Not enough loveca!';
	return false;
}
	
// Calculate gauge info
{
	$cycle = user_increase_gauge($USER_ID);
	
	for(; $cycle > 0; $cycle--)
		// Add item
		$add_item_list[] = [
			'item_id' => 5,
			'item_category_id' => 5,
			'add_type' => 1000,
			'amount' => 1,
			'owning_item_id' => 0,
			'reward_box_flag' => false
		];
}
$gauge_info = [
	'max_gauge_point' => 100,
	'gauge_point' => user_get_gauge($USER_ID),
	'added_gauge_point' => 10
];
$temp_cost = [
	'priority' => 2,
	'type' => 1,
	'item_id' => NULL,
	'amount' => $scout_info['loveca_single_cost']
];
$secretbox_info = [
	'secret_box_id' => $secretbox_id,
	'name' => $scout_info['name'],
	'title_asset' => NULL,
	'description' => $scout_info['description'],
	'start_date' => to_datetime(0),
	'end_time' => to_datetime(2147483647),
	'add_gauge' => 10,
	'is_multi' => true,
	'multi_count' => 11,
	'is_pay_cost' => ($before_user_info[1][1] - $scout_info['loveca_single_cost']) >= $scout_info['loveca_single_cost'],
	'is_pay_multi_cost' => ($before_user_info[1][1] - $scout_info['loveca_single_cost'] * 10) >= $scout_info['loveca_single_cost'] * 10,
	'cost' => $temp_cost,
	'next_cost' => $temp_cost
];
$cards = [
	[100 - $scout_info['ur_chance'] - $scout_info['sr_chance'], explode(',', preg_replace_callback('/(\d+)-(\d+)/', $common_unrange, $scout_info['r_list'] ?? ''))],
	[$scout_info['sr_chance'], explode(',', preg_replace_callback('/(\d+)-(\d+)/', $common_unrange, $scout_info['sr_list'] ?? ''))],
	[$scout_info['ur_chance'], explode(',', preg_replace_callback('/(\d+)-(\d+)/', $common_unrange, $scout_info['ur_list'] ?? ''))],
];
$album_table = $before_user_info[1][2];

// Gacha unit
$unit_get = 0;
{
	$got = $common_chance_percent(...$cards);
	$unit_get = intval($got[random_int(0, count($got) - 1)]);
}

// Get the card info.
{
	$is_new = count($DATABASE->execute_query("SELECT card_id FROM `$album_table` WHERE card_id = $unit_get")) == 0;
	$data = $unit_db->execute_query("SELECT unit_level_up_pattern_id, hp_max, normal_card_id, rank_max_card_id, rarity FROM `unit_m` WHERE unit_id = $unit_get")[0];
	$current_lvl = $unit_db->execute_query("SELECT next_exp, hp_diff FROM `unit_level_up_pattern_m` WHERE unit_level_up_pattern_id = {$data[0]} ORDER BY unit_level LIMIT 1")[0];
	$is_promo = $data[2] == $data[3];
	$unit_own_id = card_add($USER_ID, $unit_get, ['message' => 'Gained from scouting']);
	
	$add_unit_list[] = [
		'unit_id' => $unit_get,
		'unit_owning_user_id' => $unit_own_id,
		'exp' => 0,
		'next_exp' => $current_lvl[0],
		'max_hp' => $data[1] - $current_lvl[1],
		'level' => 1,
		'skill_level' => 1,
		'rank' => $is_promo ? 2 : 1,
		'love' => 0,
		'is_level_max' => false,
		'is_rank_max' => $is_promo,
		'is_love_max' => false,
		'description' => 'nil',
		'comment' => 'nil',
		'unit_rarity_id' => $data[4],
		'reward_box_flag' => $unit_max,
		'new_unit_flag' => $unit_max ? false : $is_new
	];
}

// Update
user_sub_loveca($USER_ID, $scout_info['loveca_single_cost']);

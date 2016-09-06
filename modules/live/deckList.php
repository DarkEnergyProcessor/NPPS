<?php
$unit_db = npps_get_database('unit');
$guest_info = NULL;
$guest_leader_skill = 0;

if($DATABASE->execute_query("SELECT tutorial_state FROM `users` WHERE user_id = $USER_ID")[0][0] == 3)
{
	$guest_info = [
		'user_info' => [
			'user_id' => 0,
			'name' => 'lovelive',
			'level' => 100
		],
		'center_unit_info' => [
			'unit_id' => 49,
			'level' => 60,
			'smile' => 4060,
			'cute' => 1850,
			'cool' => 1980,
			'is_rank_max' => false,
			'is_love_max' => false,
			'is_level_max' => false
		],
		'setting_award_id' => 1
	];
	$guest_leader_skill = 1;
}
else
{
	$guest_id = intval($REQUEST_DATA['party_user_id'] ?? 0);
	
	if($guest_id == 0)
	{
		echo 'Invalid guest ID!';
		return false;
	}
	
	$info = user_get_basic_info($guest_id);
	$uifo = $info['unit_info'];
	$guest_info = [
		'user_info' => [
			'user_id' => $guest_id,
			'name' => $info['name'],
			'level' => $info['level']
		],
		'center_unit_info' => [
			'unit_id' => $uifo['unit_id'],
			'level' => $uifo['level'],
			'smile' => $uifo['smile'],
			'cute' => $uifo['pure'],
			'cool' => $uifo['cool'],
			'is_rank_max' => $uifo['idolized'],
			'is_love_max' => $uifo['bond_max'],
			'is_level_max' => $uifo['level_max']
		],
		'setting_award_id' => $info['badge']
	];
	$guest_leader_skill = $uifo['skill'];
}

$deck_unit_table = $DATABASE->execute_query("SELECT deck_table, unit_table, main_deck FROM `users` WHERE user_id = $USER_ID")[0];
$deck_table = $deck_unit_table[0];
$unit_table = $deck_unit_table[1];
$usable_deck_list = [];

foreach($DATABASE->execute_query("SELECT * FROM `$deck_table`") as $deck)
{
	$base_smile = 0;
	$base_pure = 0;
	$base_cool = 0;
	$hp = 0;
	$bond = [0, 0, 0];
	$member_list = explode(':', $deck[2]);
	$has_empty = false;
	$leader_skill = 0;
	$member_own_list = [];
	
	foreach($member_list as $i => $unit_own_id)
	{
		if($unit_own_id == 0)
		{
			$has_empty = true;
			break;
		}
		
		$unit_id = $DATABASE->execute_query("SELECT card_id, level, bond FROM `$unit_table` WHERE unit_id = $unit_own_id")[0];
		$card_stats = $unit_db->execute_query("SELECT attribute_id, hp_max, smile_max, pure_max, cool_max, unit_level_up_pattern_id, default_leader_skill_id FROM `unit_m` WHERE unit_id = {$unit_id[0]}")[0];
		$stats_diff = $unit_db->execute_query("SELECT hp_diff, smile_diff, pure_diff, cool_diff FROM `unit_level_up_pattern_m` WHERE unit_level_up_pattern_id = {$card_stats[5]} AND unit_level = {$unit_id[1]}")[0];
		
		$base_smile += $card_stats[2] - $stats_diff[1];
		$base_pure += $card_stats[3] - $stats_diff[2];
		$base_cool += $card_stats[4] - $stats_diff[3];
		$bond[$card_stats[0] - 1] += $unit_id[2];
		$hp += $card_stats[1] - $stats_diff[0];
		
		$member_own_list[] = [
			'unit_owning_user_id' => intval($unit_own_id)
		];
		
		if($i == 4)
			$leader_skill = $card_stats[6] ?? 0;
	}
	
	if($has_empty)
		continue;
	
	$new_stats = deck_calculate_stats_value([$base_smile, $base_pure, $base_cool], $bond, $leader_skill, $guest_leader_skill);
	
	$usable_deck_list[] = [
		'unit_deck_id' => $deck[0],
		'main_flag' => $deck[0] == $deck_unit_table[2],
		'deck_name' => $deck[1],
		'unit_list' => $member_own_list,
		'party_info' => $guest_info,
		'subtotal_smile'  => $base_smile,
		'subtotal_cute' => $base_pure,
		'subtotal_cool' => $base_cool,
		'subtotal_skill' => 0,
		'subtotal_hp' => $hp,
		'total_smile' => $new_stats[0],
		'total_cute' => $new_stats[1],
		'total_cool' => $new_stats[2],
		'total_skill' => 0,
		'total_hp' => $hp,
		'prepared_hp_damage' => 0
	];
}

return [
	[
		'unit_deck_list' => $usable_deck_list
	],
	200
];
?>
<?php
// Soloyolo scouting
$secretbox_id = $REQUEST_DATA['secret_box_id'] ?? '';
$cost = $REQUEST_DATA['cost_priority'] ?? '';

if(preg_match('/\D/', $secretbox_id) == 1 || preg_match('/\D/', $cost) == 1)
{
	echo 'secretbox ID or cost priority is not string';
	return false;
}


$common_unrange = function(array $m): string {
    return implode(',', range($m[1], $m[2]));
};

$common_chance_percent = function(array ...$arg)
{
	$chance = random_int(0, 100000) / 1000;
	
	foreach($arg as $target)
		if(($chance -= $target[0]) <= 0) return $target[1];
	
	return NULL;
};
$userinfo_temp = include('modules/user/userInfo.php');
$before_user_info = [
	$userinfo_temp[0],
	$DATABASE->execute_query("SELECT scouting_ticket, scouting_coupon, album_table FROM `users` WHERE user_id = $USER_ID")[0]
];
$unit_db = new SQLite3Database('data/unit.db_');
$secretbox_id = intval($secretbox_id);
$cost = intval($cost);
$unit_max = false;
$gauge_info = [];
$add_unit_list = [];
$add_item_list = [];
$secretbox_info = [];
$unit_own_placeholder = 2147483647;

// Unit max flag
{
	$temp = $DATABASE->execute_query("SELECT unit_table, max_unit FROM `users` WHERE user_id = $USER_ID")[0];
	$unit_max = $DATABASE->execute_query("SELECT COUNT(unit_id) FROM `{$temp[0]}`")[0][0] >= $temp[1];
}

if($secretbox_id == 2147483647)		// Reguler scouting
{
	// TODO
}
elseif(($secretbox_id >> 16) > 0)	// Coupon scouting
{
	$cards = [];
	$scout_info = $DATABASE->execute_query('SELECT * FROM `coupon_secretbox_list` WHERE id = ?', 'i', $secretbox_id >> 16)[0];
	
	if($before_user_info[1][1] < $scout_info[5])
	{
		echo 'Not enough scouting coupon!';
		return false;
	}
	
	$gauge_info = [
		'max_gauge_point' => 100,
		'gauge_point' => user_get_gauge($USER_ID),
		'added_gauge_point' => 0
	];
	$temp_cost = [
		'priority' => 1,
		'type' => 2,
		'item_id' => 5,
		'amount' => $scout_info[5]
	];
	$secretbox_info = [
		'secret_box_id' => $secretbox_id,
		'name' => $scout_info[1],
		'title_asset' => NULL,
		'description' => $scout_info[4],
		'start_date' => to_datetime(0),
		'end_time' => to_datetime(2147483647),
		'add_gauge' => 0,
		'is_multi' => false,
		'multi_count' => 10,
		'is_pay_cost' => ($before_user_info[1][1] - $scout_info[5]) >= $scout_info[5],
		'is_pay_multi_cost' => false,
		'cost' => $temp_cost,
		'next_cost' => $temp_cost
	];
	$cards = [
		[$scout_info[9], explode(',', preg_replace_callback('/(\d+)-(\d+)/', $common_unrange, $scout_info[6] ?? ''))],
		[$scout_info[10], explode(',', preg_replace_callback('/(\d+)-(\d+)/', $common_unrange, $scout_info[7] ?? ''))],
		[$scout_info[11], explode(',', preg_replace_callback('/(\d+)-(\d+)/', $common_unrange, $scout_info[8] ?? ''))],
	];
	
	// Gacha unit
	$unit_get = 0;
	{
		$got = $common_chance_percent(...$cards);
		$unit_get = intval($got[random_int(0, count($got) - 1)]);
	}
	
	// Get the card info.
	{
		$album_table = $before_user_info[1][2];
		$is_new = count($DATABASE->execute_query("SELECT card_id FROM `$album_table` WHERE card_id = $unit_get")) == 0;
		$data = $unit_db->execute_query("SELECT unit_level_up_pattern_id, hp_max, normal_card_id, rank_max_card_id, rarity FROM `unit_m` WHERE unit_id = $unit_get")[0];
		$current_lvl = $unit_db->execute_query("SELECT next_exp, hp_diff FROM `unit_level_up_pattern_m` WHERE unit_level_up_pattern_id = {$data[0]} ORDER BY unit_level LIMIT 1")[0];
		$is_promo = $data[2] == $data[3];
		$unit_own_id = card_add($USER_ID, $unit_get, ['message' => 'Gained from scouting']);
		
		if($unit_own_id == 0)
			$unit_own_id = $unit_own_placeholder--;
		
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
	$DATABASE->execute_query("UPDATE `users` SET scouting_coupon = scouting_coupon - {$scout_info[5]} WHERE user_id = $USER_ID");
}
else	// Honor scouting
{
	// TODO
}

$after_user_info = (include('modules/user/userInfo.php'))[0];
$after_tickets = $DATABASE->execute_query("SELECT scouting_ticket, scouting_coupon FROM `users` WHERE user_id = $USER_ID")[0];

user_set_last_active($USER_ID, $TOKEN);

return [
	[
		'is_unit_max' => $unit_max,
		'item_list' => [
			[
				'item_id' => 1,
				'amount' => $after_tickets[0]
			],
			[
				'item_id' => 5,
				'amount' => $after_tickets[1]
			]
		],
		'gauge_info' => $gauge_info,
		'secret_box_info' => $secretbox_info,
		'secret_box_items' => [
			'unit' => $add_unit_list,
			'item' => $add_item_list
		],
		'before_user_info' => $before_user_info[0],
		'after_user_info' => $after_user_info,
		'next_free_gacha_timestamp' => user_get_free_gacha_timestamp($USER_ID)
	],
	200
];
?>
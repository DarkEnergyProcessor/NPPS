<?php
$secretbox_id = $REQUEST_DATA['secret_box_id'] ?? '';
$cost = $REQUEST_DATA['cost_priority'] ?? '';

if(preg_match('/\D/', $secretbox_id) == 1 || preg_match('/\D/', $cost) == 1)
{
	echo 'secretbox ID or cost priority is not int';
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
$unit_db = npps_get_database('unit');
$secretbox_id = intval($secretbox_id);
$cost = intval($cost);
$unit_max = false;
$gauge_info = [];
$add_unit_list = [];
$add_item_list = [];
$secretbox_info = [];

// Unit max flag
{
	$temp = $DATABASE->execute_query("SELECT unit_table, max_unit FROM `users` WHERE user_id = $USER_ID")[0];
	$unit_max = $DATABASE->execute_query("SELECT COUNT(unit_id) FROM `{$temp[0]}`")[0][0] >= $temp[1];
}

if($secretbox_id == 2147483647)		// Reguler scouting
{
	// TODO
}
elseif(($secretbox_id >> 16) > 0)
{
	// Impossible coupon scouting with 10+1
	echo 'Coupon doesn\'t support multi';
	return false;
}
else								// Honor scouting
{
	include('modules/secretbox/pon_mode/multi_loveca.php');
}

// Use placeholder unit_own_id if necessary
foreach($add_unit_list as &$scout_result)
	if($scout_result['unit_owning_user_id'] == 0)
		$scout_result['unit_owning_user_id'] = token_use_pseudo_unit_own_id($TOKEN);

$after_user_info = (include('modules/user/userInfo.php'))[0];
$after_tickets = $DATABASE->execute_query("SELECT scouting_ticket, scouting_coupon FROM `users` WHERE user_id = $USER_ID")[0];

// Add scouting coupon
{
	$bt_amount = count($add_item_list);
	
	if($bt_amount > 0)
		$DATABASE->execute_query("UPDATE `users` SET scouting_coupon = scouting_coupon + $bt_amount WHERE user_id = $USER_ID");
}

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
		'before_user_info' => $before_user_info[0]['user'],
		'after_user_info' => $after_user_info['user'],
		'next_free_gacha_timestamp' => user_get_free_gacha_timestamp($USER_ID)
	],
	200
];

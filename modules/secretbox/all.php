<?php
$infos = $DATABASE->execute_query("SELECT scouting_ticket, scouting_coupon, paid_loveca + free_loveca, friend_point FROM `users` WHERE user_id = $USER_ID")[0];
$honor_scouting = $DATABASE->execute_query('SELECT banner_preview, banner_big, description, loveca_single_cost FROM `secretbox_list` WHERE id = 0')[0];
$h_scouting = $DATABASE->execute_query('SELECT id, banner_preview, banner_big, banner_title, description, loveca_single_cost, name FROM `secretbox_list` WHERE id > 0');
$bt_scouting = $DATABASE->execute_query('SELECT id, banner_big, banner_title, description, coupon_cost, name FROM `coupon_secretbox_list`');
$free_gacha = user_is_free_gacha($USER_ID);
$honor_page_list = [];
$bt_page_list = [];
$secretbox_id_alloc = 2147483646;
$cost_hon = NULL;
$is_unit_max = false;

// unit max check
{
	$temp = $DATABASE->execute_query("SELECT max_unit, unit_table FROM `users` WHERE user_id = $USER_ID")[0];
	$temp2 = $DATABASE->execute_query("SELECT COUNT(unit_id) FROM `{$temp[1]}`")[0][0];
	
	$is_unit_max = $temp2 >= $temp[0];
}

// Secretbox ID 0
{
	$cost_reg = NULL;
	
	if($infos[0] > 0)
		$cost_hon = [
			'priority' => 1,
			'type' => 2,
			'item_id' => 1,
			'amount' => 1
		];
	else
		$cost_hon = [
			'priority' => 2,
			'type' => 1,
			'item_id' => NULL,
			'amount' => $honor_scouting[3]
		];
	
	if($free_gacha)
		$cost_reg = [
			'priority' => 1,
			'type' => 4,
			'item_id' => NULL,
			'amount' => 1
		];
	else
		$cost_reg = [
			'priority' => 2,
			'type' => 3,
			'item_id' => NULL,
			'amount' => 100
		];
	
	$honor_page_list[] = [
		'secret_box_page_id' => $secretbox_id_alloc--,
		'page_order' => NULL,
		'page_layout' => 1,
		'default_img_info' => [
			'banner_img_asset' => $honor_scouting[0],
			'banner_se_img_asset' => substr($honor_scouting[0], 0, strpos($honor_scouting[0], '.')).'se.png',
			'img_asset' => $honor_scouting[1],
			'url' => '/webview.php/secretbox/view_id0'
		],
		'limited_img_info' => [],
		'effect_list' => [],
		'secret_box_list' => [
			[
				'secret_box_id' => 2147483647,
				'name' => 'Scout Regular Student(s)',
				'title_asset' => NULL,
				'description' => 'dummy',
				'start_date' => to_datetime(0),
				'end_date' => to_datetime(2147483647),
				'add_gauge' => 0,
				'is_multi' => !$free_gacha,
				'multi_count' => 10,
				'is_pay_cost' => $free_gacha ?: $infos[3] >= 100,
				'is_pay_multi_cost' => $free_gacha ? false : $infos[3] >= 1000,
				'cost' => $cost_reg
			],
			[
				'secret_box_id' => 0,
				'name' => 'Honor Student Scouting',
				'title_asset' => NULL,
				'description' => 'dummy',
				'start_date' => to_datetime(0),
				'end_date' => to_datetime(2147483647),
				'add_gauge' => 10,
				'is_multi' => $infos[0] == 0,
				'multi_count' => 11,
				'is_pay_cost' => $infos[0] > 0 ?: $infos[2] >= $cost_hon['amount'],
				'is_pay_multi_cost' => $infos[0] > 0 ? false : $infos[2] >= $cost_hon['amount'] * 10,
				'cost' => $cost_hon
			]
		],
	];
}

foreach($h_scouting as $x)
{
	if($infos[0] > 0)
		$cost_hon = [
			'priority' => 1,
			'type' => 2,
			'item_id' => 1,
			'amount' => 1
		];
	else
		$cost_hon = [
			'priority' => 2,
			'type' => 1,
			'item_id' => NULL,
			'amount' => $x[5]
		];

	$honor_page_list[] = [
		'secret_box_page_id' => $secretbox_id_alloc--,
		'page_order' => NULL,
		'page_layout' => 0,
		'default_img_info' => [
			'banner_img_asset' => $x[1],
			'banner_se_img_asset' => substr($x[1], 0, strpos($x[1], '.')).'se.png',
			'img_asset' => $x[2],
			'url' => "/webview.php/secretbox/honor_view?secretbox_id={$x[0]}"
		],
		'limited_img_info' => [],
		'effect_list' => [],
		'secret_box_list' => [
			[
				'secret_box_id' => $x[0],
				'name' => $x[6] ?? 'nil',
				'title_asset' => $x[3],
				'description' => $x[4] ?? 'dummy',
				'start_date' => to_datetime(0),
				'end_date' => to_datetime(2147483647),
				'add_gauge' => 10,
				'is_multi' => $infos[0] == 0,
				'multi_count' => 11,
				'is_pay_cost' => $infos[0] > 0 ?: $infos[2] >= $cost_hon['amount'],
				'is_pay_multi_cost' => $infos[0] > 0 ? false : $infos[2] >= $cost_hon['amount'] * 11,
				'cost' => $cost_hon
			]
		]
	];
}

foreach($bt_scouting as $x)
{
	$bt_page_list[] = [
		'secret_box_page_id' => $secretbox_id_alloc--,
		'page_order' => NULL,
		'page_layout' => 0,
		'default_img_info' => [
			'banner_img_asset' => NULL,
			'banner_se_img_asset' => NULL,
			'img_asset' => $x[1],
			'url' => "/webview.php/secretbox/bt_view?coupon_secretbox_id={$x[0]}"
		],
		'limited_img_info' => [],
		'effect_list' => [],
		'secret_box_list' => [
			[
				'secret_box_id' => $x[0] * 65536,
				'name' => $x[5],
				'title_asset' => $x[2],
				'description' => $x[3] ?? 'dummy',
				'start_date' => to_datetime(0),
				'end_date' => to_datetime(2147483647),
				'add_gauge' => 0,
				'is_multi' => false,
				'multi_count' => 10,
				'is_pay_cost' => $infos[1] >= $x[4],
				'is_pay_multi_cost' => false,
				'cost' => [
					'priority' => 1,
					'type' => 2,
					'item_id' => 5,
					'amount' => $x[4]
				]
			]
		]
	];
}

$item_list_temp = [];

if($infos[0] > 0)
	$item_list_temp[] = [
		'item_id' => 1,
		'amount' => $infos[0]
	];

if($infos[1] > 0)
	$item_list_temp[] = [
		'item_id' => 5,
		'amount' => $infos[1]
	];

return [
	[
		'use_cache' => 1,
		'is_unit_max' => $is_unit_max,
		'item_list' => $item_list_temp,
		'gauge_info' => [
			'max_gauge_point' => 100,
			'gauge_point' => user_get_gauge($USER_ID)
		],
		'tab_list' => [
			[
				'tab_id' => 1,
				'page_list' => $honor_page_list
			],
			[
				'tab_id' => 2,
				'page_list' => $bt_page_list
			]
		]
	],
	200
]
?>
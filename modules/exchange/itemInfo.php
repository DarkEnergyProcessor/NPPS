<?php

$unit_db = new SQLite3Database('data/unit.db_');
$rarity_names = [NULL, 'n', 'r', 'sr', 'ur'];

/* returns
	item_id
	item_category_id
	add_type
	title
*/
$get_item_info = function(int $item_id, int $card_num) use($unit_db, $rarity_names)
{
	switch($item_id)
	{
		case 1000:
		{
			switch($card_num)
			{
				case 1:
					return [
						'item_id' => 1,
						'item_category_id' => 1,
						'add_type' => 1000,
						'title' => 'Scouting Ticket'
					];
				case 5:
					return [
						'item_id' => 5,
						'item_category_id' => 5,
						'add_type' => 1000,
						'title' => 'Scouting Coupon'
					];
				default:
					return NULL;
			}
		}
		case 1001:
		{
			/* Naming convention: <Rarity uppercase> <Idol name> */
			$card_info = $unit_db->execute_query("SELECT rarity, name FROM `unit_m` WHERE unit_id = $card_num")[0];
			
			return [
				'item_id' => $card_num,
				'item_category_id' => 0,
				'add_type' => 1001,
				'title' => sprintf('%s %s', strtoupper($rarity_names[$card_info[0]]), $card_info[1])
			];
		}
		default:
			return NULL;
	}
};

$exchange_point = [];
$exchange_list = [];
$sticker_related = $DATABASE->execute_query("SELECT sticker_table, normal_sticker, silver_sticker, silver_sticker FROM `users` WHERE user_id = $USER_ID")[0];
$sticker_table = $sticker_related[0];

for($i = 0; $i < 3; $i++)
	$exchange_point[] = [
		'rarity' => $i + 2,
		'exchange_point' => $sticker_related[$i + 1]
	];

foreach($DATABASE->execute_query("SELECT * FROM `sticker_shop_item` WHERE expire IS NULL OR expire > $UNIX_TIMESTAMP") as $sticker_data)
{
	$sticker_rarity_info = explode(':', $sticker_data['cost']);
	$rarity_num = array_search($sticker_rarity_info[0], $rarity_names);
	$item_info = $get_item_info($sticker_data['item_id'], $sticker_data['card_num']);
	
	if($rarity_num == 0 || $item_info == NULL) continue;
	
	$bought_count = $DATABASE->execute_query("SELECT amount_bought FROM `$sticker_table` WHERE sticker_id = {$sticker_data['sticker_id']}");
	
	if(count($bought_count) > 0)
		$bought_count = $bought_count[0][0];
	else
		$bought_count = 0;
	
	$temp_data = [
		'exchange_item_id' => $sticker_data['sticker_id'],
		'add_type' => $item_info['add_type'],
		'item_id' => $item_info['item_id'],
		'item_category_id' => $item_info['item_category_id'],
		'amount' => 1,
		'option' => NULL,
		'title' => $item_info['title'],
		'rarity' => $rarity_num,
		'cost_value' => intval($sticker_rarity_info[1]),
		'got_item_count' => $bought_count,
		'term_count' => $sticker_data['max_amount'] == (-1) ? 0 : $sticker_data['max_amount']
	];
	
	if($sticker_data['expire'])
		$temp_data['term_end_date'] = to_utcdatetime($sticker_data['expire']);
	
	$exchange_list[] = $temp_data;
}

return [
	[
		'exchange_item_info' => $exchange_list,
		'exchange_point_list' => $exchange_point
	],
	200
];

<?php
$reward_list_out = [];
$reward_category = intval($REQUEST_DATA['category'] ?? (-1));
$reward_order = intval($REQUEST_DATA['order'] ?? (-1));
$reward_filter = $REQUEST_DATA['filter'];

if($reward_category == (-1) || $reward_order == (-1))
{
	echo 'Invalid reward category!';
	return false;
}

$present_table = $DATABASE->execute_query("SELECT present_table FROM `users` WHERE user_id = $USER_ID")[0][0];
$order_list = [
	'DESC',
	'ASC'
];

if($reward_filter > 0)
	$reward_filter = sprintf('AND item_type IN(%s)', implode(',', $reward_filter));
else
	$reward_filter = '';

$reward_stmt = "SELECT * FROM `$present_table` ORDER BY item_pos {$order_list[$reward_order]} LIMIT 20";

switch($reward_category)
{
	case 1:
	{
		// Members
		$reward_stmt = "SELECT * FROM `$present_table` WHERE item_type = 1001 $reward_filter ORDER BY item_pos {$order_list[$reward_order]} LIMIT 20";
		break;
	}
	case 2:
	{
		// Items
		$reward_stmt = "SELECT * FROM `$present_table` WHERE item_type <> 1001 $reward_filter ORDER BY item_pos {$order_list[$reward_order]} LIMIT 20";
		break;
	}
	default:
	{
		break;
	}
}

$translate_incentive = function(int $add_type): int
{
	switch($add_type)
	{
		case 3000:
			return 1;
		case 3001:
			return 4;
		case 3002:
			return 2;
		default:
			return 0;
	}
};

foreach($DATABASE->execute_query($reward_stmt) as $items)
{
	$reward_list_out[] = [
		'incentive_id' => $items[0],
		'incentive_item_id' => $items[2] ?? $translate_incentive($items[1]),
		'amount' => $items[3],
		'item_category_id' => 0,
		'add_type' => $items[1],
		'incentive_type' => 6000,	// Always 6000?
		'incentive_message' => $items[4],
		'insert_date' => to_datetime(0),
		'remaining_time' => $items[5] == NULL ? 'No expiration' : sprintf('Expire: %s', date('d/m/y', $items[5])),
		'rank_max_flag' => false,	// Does not support idolized card.
		'item_option' => NULL
	];
}

return [
	[
		'item_count' => count($reward_list_out),
		'limit' => 20,
		'order' => $reward_order,
		'items' => $reward_list_out
	],
	200
];
?>
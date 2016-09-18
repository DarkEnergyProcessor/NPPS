<?php
$reward_category = intval($REQUEST_DATA['category'] ?? (-1));
$reward_order = intval($REQUEST_DATA['order'] ?? (-1));
$reward_filter = $REQUEST_DATA['filter'];

if($reward_category == (-1) || $reward_order == (-1))
{
	echo 'Invalid reward category!';
	return false;
}

$present_table = npps_query("SELECT present_table FROM `users` WHERE user_id = $USER_ID")[0][0];
$order_list = [
	'DESC',
	'ASC'
];

if($reward_filter > 0)
	$reward_filter = sprintf('AND item_type IN(%s)', implode(',', $reward_filter));
else
	$reward_filter = '';

$reward_where_statement = 'WHERE `collected` IS NULL';

switch($reward_category)
{
	case 1:
	{
		// Members
		$reward_where_statement = 'WHERE item_type = 1001 AND `collected` IS NULL';
		break;
	}
	case 2:
	{
		// Items
		$reward_where_statement = 'WHERE item_type <> 1001 AND `collected` IS NULL';
		break;
	}
	default:
	{
		break;
	}
}

$reward_stmt = "SELECT * FROM `$present_table` $reward_where_statement $reward_filter ORDER BY item_pos {$order_list[$reward_order]} LIMIT 1000";

$reward_list = [];
$reward_count = 0;
$total_count = npps_query("SELECT COUNT(item_pos) FROM `$present_table` WHERE `collected` IS NULL")[0][0];

npps_begin_transaction();
var_dump($reward_stmt);
foreach(npps_query($reward_stmt) as $items)
{
	$reward_count++;
	
	if(item_collect($USER_ID, $items["item_type"], $items["card_num"], $items["amount"]) !== false)
	{
		$reward_list[] = [
			"incentive_id" => $items["item_pos"],
			"item_id" => $items["card_num"],
			"add_type" => $items["item_type"],
			"amount" => $items["amount"],
			"item_category_id" => $items["item_type"] == 1001 ? 1 : 0
		];
		npps_query("UPDATE `$present_table` SET collected = $UNIX_TIMESTAMP WHERE item_pos = {$items['item_pos']}");
	}
}
npps_end_transaction();

return [
	[
		"reward_num" => $reward_count,
		"opened_num" => count($reward_list),
		"total_num" => $total_count,
		"order" => $reward_order,
		"reward_item_list" => $reward_list,
		"bushimo_reward_info" => []
		
	],
	200
];

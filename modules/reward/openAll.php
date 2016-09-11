<?php

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

$reward_stmt = "SELECT * FROM `$present_table` WHERE `collected` IS NULL ORDER BY item_pos {$order_list[$reward_order]} LIMIT 1000";

switch($reward_category)
{
	case 1:
	{
		// Members
		$reward_stmt = "SELECT * FROM `$present_table` WHERE item_type = 1001 AND `collected` IS NULL $reward_filter ORDER BY item_pos {$order_list[$reward_order]} LIMIT 1000";
		break;
	}
	case 2:
	{
		// Items
		$reward_stmt = "SELECT * FROM `$present_table` WHERE item_type <> 1001 AND `collected` IS NULL $reward_filter ORDER BY item_pos {$order_list[$reward_order]} LIMIT 1000";
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

$reward_list = [];
$reward_count = 0;
$total_count = $DATABASE->execute_query('SELECT COUNT(item_pos) FROM `'.$DATABASE->execute_query("SELECT present_table FROM `users` WHERE user_id = $USER_ID")[0][0].'` WHERE `collected` IS NULL')[0][0];
foreach($DATABASE->execute_query($reward_stmt) as $items)
{
	$reward_count++;
	
	switch($items['item_type']){
		case 3001: { /*		LOVECA		*/			
			if (npps_query(<<<QUERY
BEGIN;
UPDATE `users` SET `free_loveca`=`free_loveca`+$items[amount] WHERE `user_id` = $USER_ID;
UPDATE `$present_table` SET collected=$UNIX_TIMESTAMP WHERE `item_pos` = $items[item_pos];
COMMIT;
QUERY
			)){
				$reward_list[] = [
					"incentive_id"=>$items["item_pos"],
					"item_id"=>$items["card_num"],
					"add_type"=>$items["item_type"],
					"amount"=>$items["amount"],
					"item_category_id"=>($items["item_type"]==1001?1:0)
				];

			}		
			break;
		}
	}
	
	
	
}

return [
	[
		"reward_num"=>$reward_count,
		"opened_num"=>count($reward_list),
		"total_num"=>$total_count,
		"order"=>$reward_order,
		"reward_item_list"=>$reward_list,
		"bushimo_reward_info"=>[]
		
	],
	200
];



<?php
global $UNIX_TIMESTAMP;
$incentive_id = intval($REQUEST_DATA['incentive_id'] ?? (-1));

if($incentive_id == (-1))
{
	echo 'Invalid incentive_id!';
	return false;
}

$present_table = $DATABASE->execute_query("SELECT present_table FROM `users` WHERE user_id = $USER_ID")[0][0];

$success = [];
$fail = [];

foreach($DATABASE->execute_query("SELECT * FROM `$present_table` WHERE item_pos=$incentive_id LIMIT 1") as $items){
	if ($items["collected"] != null){
		echo "Reward already collected.";
		return false;
	}
	switch($items['item_type']){
		case 3001: { /*		LOVECA		*/			
			if (npps_query(<<<QUERY
BEGIN;
UPDATE `users` SET `free_loveca`=`free_loveca`+$items[amount] WHERE `user_id` = $USER_ID;
UPDATE `$present_table` SET collected=$UNIX_TIMESTAMP WHERE `item_pos` = $incentive_id;
COMMIT;
QUERY
			)){
				$success[] = [
					"incentive_id"=>$items["item_pos"],
					"item_id"=>$items["card_num"],
					"add_type"=>$items["item_type"],
					"amount"=>$items["amount"],
					"item_category_id"=>($items["item_type"]==1001?1:0)
				];

			}else{
				$fail[] = [
					"incentive_id"=>$items["item_pos"],
					"item_id"=>$items["card_num"],
					"add_type"=>$items["item_type"],
					"amount"=>$items["amount"],
					"item_category_id"=>($items["item_type"]==1001?1:0)
				];
			}			
			break;
		}
		default: {
			echo "This item [$items[item_type]] cannot be claimed at this time.";
			return false;
		}
	}
}

return [
	[
		"opened_num"=>count($success),
		"success"=>$success,
		"fail"=>$fail,
		"bushimo_reward_info"=>[]
	],
	200
];
<?php
$incentive_id = intval($REQUEST_DATA['incentive_id'] ?? (-1));

if($incentive_id == (-1))
{
	incentive_invalid:
	echo 'Invalid incentive_id!';
	return false;
}

$present_table = npps_query("SELECT present_table FROM `users` WHERE user_id = $USER_ID")[0][0];
$item_data = npps_query("SELECT * FROM `$present_table` WHERE item_pos = $incentive_id LIMIT 1")[0];

if(count($item_data) == 0)
	goto incentive_invalid;

$item_data_array = [
	'incentive_id' => $item_data['item_pos'],
	'item_id' => $item_data['card_num'],
	'add_type' => $item_data['item_type'],
	'amount' => $item_data['amount'],
	'item_category_id' => $item_data['item_type'] == 1001 ? 1 : 0
];
$success = [];
$fail = [];

if(item_collect($USER_ID, $item_data['item_type'], $item_data['card_num'], $item_data['amount']))
	$success[] = $item_data_array;
else
	$fail[] = $item_data_array;

return [
	[
		"opened_num" => count($success),
		"success" => $success,
		"fail" => $fail,
		"bushimo_reward_info"=> []
	],
	200
];

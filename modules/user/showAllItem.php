<?php
$item = $DATABASE->execute_query("SELECT scouting_ticket, scouting_coupon FROM `users` WHERE user_id = $USER_ID")[0];

return [
	[
		'items' => [
			[
				'item_id' => 1,
				'item_category_id' => 1,
				'item_sub_category_id' => 1,
				'amount' => $item[0],
				'insert_date' => to_datetime(0)
			],
			[
				'item_id' => 5,
				'item_category_id' => 1,
				'item_sub_category_id' => 1,
				'amount' => $item[1],
				'insert_date' => to_datetime(0)
			],
		]
	],
	200
];
?>
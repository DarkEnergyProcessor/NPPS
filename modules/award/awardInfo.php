<?php
$award_data = $DATABASE->execute_query("SELECT badge_id, unlocked_badge FROM `users` WHERE user_id = $USER_ID")[0];
$award_out = [];

foreach(explode(',', $award_data[1]) as $id)
{
	$award_out[] = [
		'award_id' => intval($id),
		'is_set' => $id == $award_data[0],
		'insert_date' => to_datetime(0)
	];
}

return [
	[
		'award_info' => $award_out
	],
	200
];
?>
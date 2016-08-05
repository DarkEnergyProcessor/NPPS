<?php
$subscenario = $DATABASE->execute_query("SELECT subscenario_tracking FROM `users` WHERE user_id = $USER_ID")[0][0] ?? '';

if(strlen($subscenario) == 0)
	return [
		[
			'subscenario_status_list' => false
		],
		200
	];

$subscenario_data = [];

foreach(explode(',', $subscenario) as $s)
{
	$is_complete = strpos($s, '!') !== false;
	
	$subscenario_data[] = [
		'subscenario_id' => $is_complete ? intval(substr($s, 1)) : intval($s),
		'status' => $is_complete ? 2 : 1
	];
}

return [
	[
		'subscenario_status_list' => $subscenario_data
	],
	200
];
?>
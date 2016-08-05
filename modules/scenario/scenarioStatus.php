<?php
$scenario_tracking = intval($DATABASE->execute_query("SELECT latest_scenario FROM `users` WHERE user_id = $USER_ID")[0][0]);
$new_scenario = intval(substr(strstr($scenario_tracking, '.') ?: ".$scenario_tracking", 1));

$scenario_data = [];
for($i = 1; $i <= $scenario_tracking; $i++)
	$scenario_data[] = [
		'scenario_id' => $i,
		'status' => 2
	];

if($new_scenario > $scenario_tracking)
	$scenario_data[] = [
		'scenario_id' => $new_scenario,
		'status' => 1
	];

return [
	[
		'scenario_status_list' => $scenario_data
	],
	200
];
?>
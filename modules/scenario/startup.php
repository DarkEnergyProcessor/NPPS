<?php
// TODO: Figure how does this work.
$scenario = intval($REQUEST_DATA['scenario_id'] ?? 0);

if($scenario == 0)
{
	echo 'Invalid scenario ID!';
	return false;
}

return [
	[
		'scenario_id' => $scenario,
		'scenario_adjustment' => 50
	],
	200
];
?>
<?php
$subscenario_id = intval($REQUEST_DATA['subscenario_id'] ?? 0);

if($subscenario_id == 0)
{
	echo 'Invalid subscenario ID!';
	return false;
}

return [
	[
		'subscenario_id' => $subscenario_id,
		'scenario_adjustment' => 50
	],
	200
];
?>
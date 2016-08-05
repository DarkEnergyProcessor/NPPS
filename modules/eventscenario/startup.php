<?php
if(!defined('UNLOCK_ALL_EVENTSCENARIO'))
{
	echo 'Eventscenario is not supported on this server!';
	return false;
}

$event_scenario_id = intval($REQUEST_DATA['event_scenario_id'] ?? 0);
$pseudo_event_id = intdiv($event_scenario_id - 1, 5) - 9;

if($event_scenario_id < 1 || $event_scenario_id > 45)
{
	echo 'Invalid eventscenario ID!';
	return false;
}

return [
	[
		'event_scenario_list' => [
			'event_id' => $pseudo_event_id,
			'progress' => 4,
			'status' => 2,
			'event_scenario_id' => $event_scenario_id
		],
		'scenario_adjustment' => 50
	],
	200
];
?>
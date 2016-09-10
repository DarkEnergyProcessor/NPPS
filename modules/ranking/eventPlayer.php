<?php
// Placeholder only
// TODO: Support event ranking specific ID
/*
$rank_page = intval($REQUEST_DATA['page'] ?? -1);
$rank_limit = intval($REQUEST_DATA['limit'] ?? -1);
$rank_event = intval($REQUEST_DATA['event_id'] ?? 0);

if($rank_page == (-1) || $rank_limit == (-1) || $rank_event == 0)
{
	echo 'Invalid page, limit, or event_id';
	return false;
}

$ranking_table = $DATABASE->execute_query("SELECT event_ranking_table FROM `event_list` WHERE event_id = $rank_event");

if(count($ranking_table) == 0)
{
	echo 'Non-existent event ID';
	return false;
}*/

return [
	[
		'error_code' => 1602,
	],
	600
];

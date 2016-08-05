<?php
$banner_list = [];

foreach($DATABASE->execute_query("SELECT event_id, banner_preview, event_end FROM `event_list` WHERE event_close > $UNIX_TIMESTAMP AND event_start <= $UNIX_TIMESTAMP") as $event)
{
	$banner_list[] = [
		'banner_type' => 0,
		'target_id' => $event[0],
		'asset_path' => $event[1],
		'asset_path_se' => substr($event[1], 0, strpos($event[1], '.')).'se.png',
		'master_is_active_event' => $event[2] > $UNIX_TIMESTAMP
	];
}

foreach($DATABASE->execute_query('SELECT id, banner_preview FROM `secretbox_list`') as $secretbox)
{
	$banner_list[] = [
		'banner_type' => 1,
		'target_id' => $secretbox[0],
		'asset_path' => $secretbox[1],
		'asset_path_se' => substr($secretbox[1], 0, strpos($secretbox[1], '.')).'se.png',
	];
}

return [
	[
		'time_limit' => to_datetime($UNIX_TIMESTAMP - ($UNIX_TIMESTAMP % 86400) + 86399),
		'banner_list' => $banner_list
	],
	200
];
?>
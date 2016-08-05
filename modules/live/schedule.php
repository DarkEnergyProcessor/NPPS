<?php
$live_db = new SQLite3Database('data/live.db_');
$event_list = [];
$live_time = [];

$get_stage_level = function(int $live_id, SQLite3Database $live_db): int
{
	$stage_level = $live_db->execute_query("SELECT stage_level FROM `live_setting_m` WHERE live_setting_id = (SELECT live_setting_id FROM `special_live_m` WHERE live_difficulty_id = $live_id)");
	
	if(count($stage_level) == 0)
		$stage_level = $live_db->execute_query("SELECT stage_level FROM `live_setting_m` WHERE live_setting_id = (SELECT live_setting_id FROM `normal_live_m` WHERE live_difficulty_id = $live_id)")[0][0];
	else
		$stage_level = $stage_level[0][0];
	
	return $stage_level;
};

// Event songs.
foreach($DATABASE->execute_query("SELECT * FROM `event_list` WHERE event_start <= $UNIX_TIMESTAMP AND event_close > $UNIX_TIMESTAMP") as $ev)
{
	$event_list[] = [
		'event_id' => $ev[0],
		'event_category_id' => $ev[5] == 1 ? 3 : ($ev[8] != NULL ? 2 : 1),
		'name' => $ev[4],
		'open_date' => to_datetime($ev[1]),
		'start_date' => to_datetime($ev[1] + 1),
		'end_date' => to_datetime($ev[2]),
		'close_date' => to_datetime($ev[3]),
		'banner_asset_name' => $ev[6],
		'banner_se_asset_name' => substr($ev[6], 0, strpos($ev[6], '.')).'se.png',
		'result_banner_asset_name' => substr($ev[6], 0, strpos($ev[6], '.')).'_re.png',
		'description' => 'nil'
	];
	
	if($ev[8] != NULL)
	{
		for($i = 10; $i <= 13; $i++)
			if($ev[$i])
				foreach(explode(',', $ev[$i]) as $live_id)
				{
					$stage_level = $get_stage_level($live_id, $live_db);
					
					$live_time[] = [
						'live_difficulty_id' => intval($live_id),
						'start_date' => to_datetime($ev[1]),
						'end_date' => to_datetime($ev[2]),
						'is_random' => false,				// TODO
						'dangerous' => $stage_level >= 11,
						'use_quad_point' => false			// TODO
					];
				}
	}
}

// B-side songs
foreach($DATABASE->execute_query("SELECT * FROM `b_side_schedule` WHERE end_available_time > $UNIX_TIMESTAMP AND start_available_time < $UNIX_TIMESTAMP") as $v)
{
	$stage_level = $get_stage_level($v[0], $live_db);
	
	$live_time[] = [
		'live_difficulty_id' => intval($v[0]),
		'start_date' => to_datetime($v[1]),
		'end_date' => to_datetime($v[2]),
		'is_random' => false,				// TODO
		'dangerous' => $stage_level >= 11,
		'use_quad_points' => false			// TODO
	];
}

// Daily rotation
foreach(live_get_current_daily() as $live_id)
{
	$stage_level = $get_stage_level($live_id, $live_db);
	
	$live_time[] = [
		'live_difficulty_id' => intval($live_id),
		'start_date' => to_utcdatetime($UNIX_TIMESTAMP - ($UNIX_TIMESTAMP % 86400)),
		'end_date' => to_utcdatetime($UNIX_TIMESTAMP - ($UNIX_TIMESTAMP % 86400) + 86399),
		'is_random' => false,				// TODO
		'dangerous' => $stage_level >= 11,
		'use_quad_points' => false			// TODO
	];
}

return [
	[
		'event_list' => $event_list,
		'live_list' => $live_time,
		'limited_bonus_list' => []
	],
	200
];
?>
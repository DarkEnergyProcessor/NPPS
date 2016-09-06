<?php
$live_db = npps_get_database('live');
$event_common_db = npps_get_database('event/event_common');
$event_list = [];
$live_time = [];

$event_common_db->execute_query('ATTACH DATABASE `data/event/marathon.db_` AS marathon');

$get_stage_level = function(int $live_id, SQLite3Database $event_common_db = NULL) use($live_db): int
{
	$stage_level = $live_db->execute_query("SELECT stage_level FROM `live_setting_m` WHERE live_setting_id = (SELECT live_setting_id FROM `special_live_m` WHERE live_difficulty_id = $live_id)");
	
	if(count($stage_level) == 0)
	{
		$stage_level = $live_db->execute_query("SELECT stage_level FROM `live_setting_m` WHERE live_setting_id = (SELECT live_setting_id FROM `normal_live_m` WHERE live_difficulty_id = $live_id)");
		
		if(count($stage_level) == 0)
			if($event_common_db != NULL)
			{
				$lsid = $event_common_db->execute_query("SELECT live_setting_id FROM `event_marathon_live_m` WHERE live_difficulty_id = $live_id")[0][0];
				
				return $live_db->execute_query("SELECT stage_level FROM `live_setting_m` WHERE live_setting_id = $lsid")[0][0];
			}
			else
				return 0;
		else
			return $stage_level[0][0];
	}
	else
		return $stage_level[0][0];
	
	return 0;
};

// Event songs.
foreach($DATABASE->execute_query("SELECT * FROM `event_list` WHERE event_start <= $UNIX_TIMESTAMP AND event_close > $UNIX_TIMESTAMP") as $ev)
{
	$event_info = $event_common_db->execute_query("SELECT event_category_id, name, banner_asset_name, banner_se_asset_name, result_banner_asset_name FROM `event_m` WHERE event_id = {$ev['event_id']}")[0];
	$event_list[] = [
		'event_id' => $ev['event_id'],
		'event_category_id' => $event_info[0],
		'name' => $event_info[1],
		'open_date' => to_utcdatetime($ev['event_start']),
		'start_date' => to_utcdatetime($ev['event_start'] + 1),
		'end_date' => to_utcdatetime($ev['event_end']),
		'close_date' => to_utcdatetime($ev['event_close']),
		'banner_asset_name' => $event_info[2],
		'banner_se_asset_name' => $event_info[3],
		'result_banner_asset_name' => $event_info[4],
		'description' => 'nil'
	];
	
	if($ev['token_image'] != NULL)
	{
		/* Token event, get it's live list */
		foreach(['easy_song_list','normal_song_list','hard_song_list','expert_song_list'] as $i)
			if($ev[$i] && strlen($ev[$i]) > 0)
				foreach(explode(',', $ev[$i]) as $live_id)
				{
					$stage_level = $get_stage_level($live_id, $event_common_db);
					$event_song_info = $event_common_db->execute_query("SELECT special_setting, random_flag FROM `event_marathon_live_m` WHERE live_difficulty_id = $live_id")[0];
					
					$live_time[] = [
						'live_difficulty_id' => intval($live_id),
						'start_date' => to_datetime($ev['event_start']),
						'end_date' => to_datetime($ev['event_end']),
						'is_random' => !!$event_song_info[1],
						'dangerous' => $stage_level >= 11,
						'use_quad_point' => !!$event_song_info[0]
					];
				}
	}
}

// B-side songs
foreach($DATABASE->execute_query("SELECT * FROM `b_side_schedule` WHERE end_available_time > $UNIX_TIMESTAMP AND start_available_time < $UNIX_TIMESTAMP") as $v)
{
	$stage_level = $get_stage_level($v[0]);
	
	$live_time[] = [
		'live_difficulty_id' => intval($v[0]),
		'start_date' => to_datetime($v[1]),
		'end_date' => to_datetime($v[2]),
		'is_random' => false,
		'dangerous' => $stage_level >= 11,
		'use_quad_points' => false
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
		'is_random' => false,
		'dangerous' => $stage_level >= 11,
		'use_quad_points' => false
	];
}

return [
	[
		'event_list' => $event_list,
		'live_list' => $live_time,
		'limited_bonus_list' => []	// Gold bonus is not supported in NPPS
	],
	200
];
?>
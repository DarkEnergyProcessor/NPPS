<?php
$live_id = intval($REQUEST_DATA['live_difficulty_id'] ?? 0);
$deck_id = intval($REQUEST_DATA['unit_deck_id'] ?? 0);
$party_user_id = intval($REQUEST_DATA['party_user_id'] ?? 0);

if($live_id == 0 || $deck_id == 0 || $party_user_id == 0)
{
	echo 'Invalid live ID or deck ID!';
	return false;
}

// Only 1 live show can be done at time
if(count(npps_query("SELECT live_id FROM `wip_live` WHERE user_id = $USER_ID")) > 0)
{
	echo 'Another live is in progress!';
	return false;
}

// verify live existence
if(live_search($USER_ID, $live_id) == false)
	return ERROR_CODE_LIVE_NOT_FOUND;

$live_db = npps_get_database('live');
$live_setting_id = 0;
$needed_lp = 0;
$needed_token = 0;
$live_is_random = 0;
$live_x4_points = 0;

npps_attach_database($live_db, 'event/battle', 'event/festival', 'event/marathon');

{
	$temp = $live_db->execute_query(<<<QUERY
		SELECT live_setting_id, capital_type, capital_value, random_flag, special_setting FROM
		(
			SELECT live_difficulty_id, live_setting_id, capital_type, capital_value, 0 as random_flag, 0 as special_setting FROM `normal_live_m` UNION
			SELECT live_difficulty_id, live_setting_id, capital_type, capital_value, 0 as random_flag, 0 as special_setting FROM `special_live_m` UNION
			SELECT live_difficulty_id, live_setting_id, capital_type, capital_value, random_flag, special_setting FROM `event_marathon_live_m`
		)
		WHERE live_difficulty_id = $live_id
QUERY
	);
	
	if(count($temp) == 0)
	{
		echo 'Invalid live_difficulty_id!';
		return false;
	}
	
	$temp = $temp[0];
	$live_setting_id = $temp['live_setting_id'];
	$live_is_random = $temp['random_flag'];
	$live_x4_points = $temp['special_setting'];
	
	switch($temp['capital_type'])
	{
		case 1:
		{
			if(!user_is_enough_lp($USER_ID, $temp['capital_value']))
				return ERROR_CODE_LIVE_NOT_ENOUGH_CURRENT_ENERGY;
			
			//user_sub_lp($USER_ID, $needed_lp);
			break;
		}
		case 2:
		{
			$current_token = 0;
			$needed_token = $temp['capital_value'];
			$ev = npps_query("SELECT event_ranking_table FROM `event_list` WHERE token_image IS NOT NULL AND event_start <= $UNIX_TIMESTAMP AND event_close > $UNIX_TIMESTAMP");
			
			// get current token
			if(count($ev) == 0)
			{
				echo 'No active event!';
				return false;
			}
			
			$user_event_info = npps_query("SELECT current_token FROM `{$ev['event_ranking_table']}` WHERE user_id = $USER_ID");
			
			if(count($user_event_info) > 0)
				$current_token = $user_event_info['current_token'];
			
			if($current_token < $needed_token)
				return ERROR_CODE_LIVE_NOT_ENOUGH_TOKEN;
			
			//npps_query("UPDATE `{$ev['event_ranking_table']}` SET current_token = current_token - $needed_token WHERE user_id = $USER_ID");
			break;
		}
	}
}

if(live_notes_exist($live_setting_id) == false)
{
	echo "Notes data for live_setting_id = $live_setting_id not found!";
	http_response_code(500);
	return false;
}

$live_notes = live_load_notes($live_setting_id);
$live_info = $live_db->execute_query("SELECT stage_level, notes_speed, c_rank_score, b_rank_score, a_rank_score, s_rank_score FROM `live_setting_m` WHERE live_setting_id = $live_setting_id")[0];
$lp_info = [];

{
	// Get LP
	$temp = npps_query("SELECT full_lp_recharge, overflow_lp, max_lp FROM `users` WHERE user_id = $USER_ID")[0];
	$lp_time_charge = $temp[0] - $UNIX_TIMESTAMP;
	$total_lp = intdiv($lp_time_charge, 360);
	
	if($lp_time_charge < 0 || $temp[1] > 0)
	{
		$lp_time_charge = 0;
		$total_lp = $temp[2];
	}
	
	$total_lp += $temp[1];
	
	$lp_info['time'] = to_datetime($temp[0]);
	$lp_info['overflow'] = $total_lp;
}

// Get first marathon event
$active_event = npps_query("SELECT event_id FROM `event_list` WHERE token_image IS NOT NULL AND event_start <= $UNIX_TIMESTAMP AND event_end > $UNIX_TIMESTAMP ORDER BY event_start LIMIT 1");

if(count($active_event) > 0)
	$active_event = $active_event[0][0];
else
	$active_event = NULL;

// Add to wip_live
npps_query("INSERT INTO `wip_live` (user_id, live_id, deck_num, guest_user_id, started) VALUES(?, ?, ?, ?, $UNIX_TIMESTAMP)", 'iiii',
	$USER_ID,
	$live_id,
	$deck_id,
	$party_user_id
);

// TODO: Add support for MedFes live/play
return [
	[
		'rank_info' => [
			[
				'rank' => 5,
				'rank_min' => 0,
				'rank_max' => $live_info[2] - 1
			],
			[
				'rank' => 4,
				'rank_min' => $live_info[2],
				'rank_max' => $live_info[3] - 1
			],
			[
				'rank' => 3,
				'rank_min' => $live_info[3],
				'rank_max' => $live_info[4] - 1
			],
			[
				'rank' => 2,
				'rank_min' => $live_info[4],
				'rank_max' => $live_info[5] - 1
			],
			[
				'rank' => 1,
				'rank_min' => $live_info[5],
				'rank_max' => 0
			],
		],
		'live_info' => [
			[
				'live_difficulty_id' => $live_id,
				'is_random' => $live_is_random > 0,
				'dangerous' => $live_info[0] > 10,
				'use_quad_point' => $live_x4_points > 0,
				'notes_speed' => $live_info[1],
				'notes_list' => $live_notes
			]
		],
		'is_marathon_event' => $active_event !== NULL,
		'marathon_event_id' => $active_event,
		'energy_full_time' => $lp_info['time'],
		'over_max_energy' => $lp_info['overflow']
	],
	200
];

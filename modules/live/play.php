<?php
$live_id = intval($REQUEST_DATA['live_difficulty_id'] ?? 0);
$deck_id = intval($REQUEST_DATA['unit_deck_id'] ?? 0);

if($live_id == 0 || $deck_id == 0)
{
	echo 'Invalid live ID or deck ID!';
	return false;
}

if(!file_exists("data/notes/$live_id.json"))
{
	echo "Notes data for live_difficulty_id = $live_id doesn't exist!";
	http_response_code(500);
	return false;
}

$live_notes = json_decode(file_get_contents("data/notes/$live_id.json"), true);
$live_db = new SQLite3Database('data/live.db_');
$live_setting_id = 0;
$needed_lp = 0;

{
	$temp = $live_db->execute_query("SELECT live_setting_id, capital_value FROM (SELECT live_difficulty_id, live_setting_id, capital_value FROM `normal_live_m` UNION SELECT live_difficulty_id, live_setting_id, capital_value FROM `special_live_m`) WHERE live_difficulty_id = $live_id");
	
	if(count($temp) == 0)
	{
		echo 'Invalid live_difficulty_id!';
		return false;
	}
	
	$live_setting_id = $temp[0][0];
	$needed_lp = $temp[0][1];
}

$live_info = $live_db->execute_query("SELECT stage_level, notes_speed, c_rank_score, b_rank_score, a_rank_score, s_rank_score FROM `live_setting_m` WHERE live_setting_id = $live_setting_id")[0];

// Deduce LP
if(!user_is_enough_lp($USER_ID, $needed_lp))
{
	echo 'Not enough LP!';
	return false;
}

//user_sub_lp($USER_ID, $needed_lp);

$lp_info = [];

{
	// Get LP
	$temp = $DATABASE->execute_query("SELECT full_lp_recharge, overflow_lp, max_lp FROM `users` WHERE user_id = $USER_ID")[0];
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
$active_event = $DATABASE->execute_query("SELECT event_id FROM `event_list` WHERE token_image IS NOT NULL AND event_start <= $UNIX_TIMESTAMP AND event_end > $UNIX_TIMESTAMP ORDER BY event_start LIMIT 1");

if(count($active_event) > 0)
	$active_event = $active_event[0][0];
else
	$active_event = NULL;

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
				'is_random' => false,		// TODO
				'dangerous' => $live_info[0] > 10,
				'use_quad_point' => false,	// TODO
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

?>
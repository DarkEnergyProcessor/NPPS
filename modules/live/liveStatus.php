<?php
$live_db = new SQLite3Database('data/live.db_');
$normal_live = [];
$special_live = [];
$event_live = [];

// Normal live
foreach(
	$DATABASE->execute_query('SELECT * FROM `'.$DATABASE->execute_query("SELECT live_table FROM `users` WHERE user_id = $USER_ID")[0][0].'` WHERE normal_live = 1')
	as $k => $v
)
{
	$lsid = $live_db->execute_query("SELECT live_setting_id FROM `normal_live_m` WHERE live_difficulty_id = {$v[0]}")[0][0];	// live_setting_id
	$sc_clears = $live_db->execute_query("SELECT c_rank_score, b_rank_score, a_rank_score, s_rank_score, 
		c_rank_combo, b_rank_combo, a_rank_combo, s_rank_combo FROM `live_setting_m` WHERE live_setting_id = $lsid")[0];
	$cleared = [];
	
	/* Score clear check */
	{
		$goal_clears = $live_db->execute_query("SELECT live_goal_reward_id FROM `live_goal_reward_m` WHERE live_difficulty_id = {$v[0]} AND live_goal_type = 1 ORDER BY rank DESC");
		
		foreach($goal_clears as $i => $id)
		{
			if($v[2] >= $sc_clears[$i])
				$cleared[] = $id;
		}
	}
	
	/* Combo clear check */
	{
		$goal_clears = $live_db->execute_query("SELECT live_goal_reward_id FROM `live_goal_reward_m` WHERE live_difficulty_id = {$v[0]} AND live_goal_type = 2 ORDER BY rank DESC");
		
		foreach($goal_clears as $i => $id)
		{
			if($v[3] >= $sc_clears[$i + 4])
				$cleared[] = $id;
		}
	}
	
	/* "Clear times" clear check */
	{
		$clears = $live_db->execute_query("SELECT c_rank_complete, b_rank_complete, a_rank_complete, s_rank_complete FROM `normal_live_m` WHERE live_setting_id = $lsid")[0];
		$goal_clears = $live_db->execute_query("SELECT live_goal_reward_id FROM `live_goal_reward_m` WHERE live_difficulty_id = {$v[0]} AND live_goal_type = 3 ORDER BY rank DESC");
		
		foreach($goal_clears as $i => $id)
		{
			if($v[4] >= $clears[$i])
				$cleared[] = $id;
		}
	}
	
	$normal_live[] = [
		'live_difficulty_id' => $v[0],
		'status' => $v[4] > 0 ? 2 : 1,
		'hi_score' => $v[2],
		'hi_combo_cnt' => $v[3],
		'clear_cnt' => $v[4],
		'achieved_goal_id_list' => $cleared
	];
}

// Special live: B-side (non-daily)
foreach($DATABASE->execute_query("SELECT live_id FROM `b_side_schedule` WHERE end_available_time > $UNIX_TIMESTAMP AND start_available_time < $UNIX_TIMESTAMP") as $x)
{
	$v = $x[0];
	$live_data = live_get_info($USER_ID, $v);
	$lsid = $live_db->execute_query("SELECT live_setting_id FROM `special_live_m` WHERE live_difficulty_id = $v")[0][0];
	$sc_clears = $live_db->execute_query("SELECT c_rank_score, b_rank_score, a_rank_score, s_rank_score, 
		c_rank_combo, b_rank_combo, a_rank_combo, s_rank_combo FROM `live_setting_m` WHERE live_setting_id = $lsid")[0];
	$cleared = [];
	
	/* Score clear check */
	{
		$goal_clears = $live_db->execute_query("SELECT live_goal_reward_id FROM `live_goal_reward_m` WHERE live_difficulty_id = $v AND live_goal_type = 1 ORDER BY rank DESC");
		
		foreach($goal_clears as $i => $id)
		{
			if($live_data['score'] >= $sc_clears[$i])
				$cleared[] = $id;
		}
	}
	
	/* Combo clear check */
	{
		$goal_clears = $live_db->execute_query("SELECT live_goal_reward_id FROM `live_goal_reward_m` WHERE live_difficulty_id = $v AND live_goal_type = 2 ORDER BY rank DESC");
		
		foreach($goal_clears as $i => $id)
		{
			if($live_data['combo'] >= $sc_clears[$i + 4])
				$cleared[] = $id;
		}
	}
	
	/* "Clear times" clear check */
	{
		$clears = $live_db->execute_query("SELECT c_rank_complete, b_rank_complete, a_rank_complete, s_rank_complete FROM `special_live_m` WHERE live_setting_id == $lsid")[0];
		$goal_clears = $live_db->execute_query("SELECT live_goal_reward_id FROM `live_goal_reward_m` WHERE live_difficulty_id = $v AND live_goal_type = 3 ORDER BY rank DESC");
		
		foreach($goal_clears as $i => $id)
		{
			if($live_data['clear'] >= $clears[$i])
				$cleared[] = $id;
		}
	}
	
	$special_live[] = [
		'live_difficulty_id' => $v,
		'status' => $live_data['clear'] > 0 ? 2 : 1,
		'hi_score' => $live_data['score'],
		'hi_combo_cnt' => $live_data['combo'],
		'clear_cnt' => $live_data['clear'],
		'achieved_goal_id_list' => $cleared
	];
}

// Special live: Daily
foreach(live_get_current_daily() as $v)
{
	$live_data = live_get_info($USER_ID, $v);
	$lsid = $live_db->execute_query("SELECT live_setting_id FROM `special_live_m` WHERE live_difficulty_id = $v")[0][0];
	$sc_clears = $live_db->execute_query("SELECT c_rank_score, b_rank_score, a_rank_score, s_rank_score, 
		c_rank_combo, b_rank_combo, a_rank_combo, s_rank_combo FROM `live_setting_m` WHERE live_setting_id = $lsid")[0];
	$cleared = [];
	
	/* Score clear check */
	{
		$goal_clears = $live_db->execute_query("SELECT live_goal_reward_id FROM `live_goal_reward_m` WHERE live_difficulty_id = $v AND live_goal_type = 1 ORDER BY rank DESC");
		
		foreach($goal_clears as $i => $id)
		{
			if($live_data['score'] >= $sc_clears[$i])
				$cleared[] = $id;
		}
	}
	
	/* Combo clear check */
	{
		$goal_clears = $live_db->execute_query("SELECT live_goal_reward_id FROM `live_goal_reward_m` WHERE live_difficulty_id = $v AND live_goal_type = 2 ORDER BY rank DESC");
		
		foreach($goal_clears as $i => $id)
		{
			if($live_data['combo'] >= $sc_clears[$i + 4])
				$cleared[] = $id;
		}
	}
	
	/* "Clear times" clear check */
	{
		$clears = $live_db->execute_query("SELECT c_rank_complete, b_rank_complete, a_rank_complete, s_rank_complete FROM `special_live_m` WHERE live_setting_id == $lsid")[0];
		$goal_clears = $live_db->execute_query("SELECT live_goal_reward_id FROM `live_goal_reward_m` WHERE live_difficulty_id = $v AND live_goal_type = 3 ORDER BY rank DESC");
		
		foreach($goal_clears as $i => $id)
		{
			if($live_data['clear'] >= $clears[$i])
				$cleared[] = $id;
		}
	}
	
	$special_live[] = [
		'live_difficulty_id' => $v,
		'status' => $live_data['clear'] > 0 ? 2 : 1,
		'hi_score' => $live_data['score'],
		'hi_combo_cnt' => $live_data['combo'],
		'clear_cnt' => $live_data['clear'],
		'achieved_goal_id_list' => $cleared
	];
}

// Event live
foreach($DATABASE->execute_query('SELECT easy_song_list, normal_song_list, hard_song_list, expert_song_list FROM `event_list` WHERE token_image IS NOT NULL') as $ev)
{
	foreach($ev as $live)
	{
		$live_data = live_get_info($USER_ID, $v);
		
		$event_live[] = [
			'live_difficulty_id' => $v,
			'status' => $live_data['clear'] > 0 ? 2 : 1,
			'hi_score' => $live_data['score'],
			'hi_combo_cnt' => $live_data['combo'],
			'clear_cnt' => $live_data['clear'],
			'achieved_goal_id_list' => [] 				// TODO
		];
	}
}

return [
	[
		'normal_live_status_list' => $normal_live,
		'special_live_status_list' => $special_live,
		'marathon_live_status_list' => $event_live
	],
	200
];
?>
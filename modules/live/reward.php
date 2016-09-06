<?php
// TODO: Complete it
$is_int_list = function(int ...&$varlist): bool
{
	foreach($varlist as &$x)
		if(is_int($x) == false)
			return false;
	
	return true;
};

$live_coin_table = [	// index: [score][difficulty]
	[2250, 3000, 3750, 4500],
	[1800, 2400, 3000, 3600],
	[1200, 1600, 2000, 2400],
	[600 , 800 , 1000, 1200],
	[300 , 400 , 500 , 600 ]
];
$live_exp_table = [12, 26, 43, 83];

$live_difficulty_id = $REQUEST_DATA['live_difficulty_id'] ?? 0;

// combo-related data
$perfect_cnt = $REQUEST_DATA['perfect_cnt'] ?? 0;
$great_cnt = $REQUEST_DATA['great_cnt'] ?? 0;
$good_cnt = $REQUEST_DATA['good_cnt'] ?? 0;
$bad_cnt = $REQUEST_DATA['bad_cnt'] ?? 0;
$miss_cnt = $REQUEST_DATA['miss_cnt'] ?? 0;
$max_combo = $REQUEST_DATA['max_combo'] ?? 0;

// score-related data
$score_smile = $REQUEST_DATA['score_smile'] ?? 0;
$score_cute = $REQUEST_DATA['score_cute'] ?? 0;
$score_cool = $REQUEST_DATA['score_cool'] ?? 0;

// other
$love_cnt = $REQUEST_DATA['love_cnt'] ?? 0;
$event_point = $REQUEST_DATA['event_point'] ?? 0;
$event_id = $REQUEST_DATA['event_id'] ?? 0;

if(!$is_int_list($perfect_cnt, $great_cnt, $good_cnt, $bad_cnt, $miss_cnt, $max_combo,
	$score_smile, $score_cute, $score_cool, $live_difficulty_id, $love_cnt) &&
	$live_difficulty_id > 0 &&
	($event_id > 0 && !is_int($event_point)))
{
	echo 'Invalid argument passed!';
	return false;
}

$wip_live_data = NULL;
$live_db = npps_get_database('live');
$unit_db = npps_get_database('unit');
$live_db->execute_query('ATTACH DATABASE `data/event/marathon.db_` AS `marathon`');
$unit_db->execute_query('ATTACH DATABASE `data/subscenario.db_` AS `subscenario`');
// $live_notes_data = json_decode(file_get_contents("data/notes/$live_difficulty_id.json"), true);
$clear_times_data = NULL;
$live_setting_id = 0;
$live_difficulty_level = 1;	// not live_difficulty_id
$live_guest_id = 0;

// get data from WIP live
{
	$wip_live = $DATABASE->execute_query("SELECT * FROM `wip_live` WHERE user_id = $USER_ID");
	
	if(count($wip_live) == 0)
	{
		echo 'No live in progress!';
		return false;
	}
	
	if($wip_live['live_id'] != $live_difficulty_id)
	{
		echo 'Invalid live_difficulty_id!';
		return false;
	}
	
	$DATABASE->execute_query("DELETE FROM `wip_live` WHERE user_id = $USER_ID");
	
	// get live info
	$live_data = $live_db->execute_query("SELECT live_setting_id, capital_type, capital_value, random_flag, special_setting,
		c_rank_complete, b_rank_complete, a_rank_complete, s_rank_complete FROM (
			SELECT live_difficulty_id, live_setting_id, capital_type, capital_value, 0 as random_flag, 0 as special_setting,
				c_rank_complete, b_rank_complete, a_rank_complete, s_rank_complete FROM `normal_live_m` UNION
			SELECT live_difficulty_id, live_setting_id, capital_type, capital_value, 0 as random_flag, 0 as special_setting,
				c_rank_complete, b_rank_complete, a_rank_complete, s_rank_complete FROM `special_live_m` UNION
			SELECT live_difficulty_id, live_setting_id, capital_type, capital_value, random_flag, special_setting,
				c_rank_complete, b_rank_complete, a_rank_complete, s_rank_complete FROM `event_marathon_live_m`
		)
		WHERE live_difficulty_id = $live_difficulty_id")[0];
	$stage_data = $live_db->execute_query("SELECT stage_level, difficulty FROM `live_setting_m` WHERE live_setting_id = {$live_data['live_setting_id']}")[0];
	$live_difficulty_level = $stage_data['difficulty'];
	$player_exp = $live_exp_table[min($live_difficulty_level, 4) - 1];
	
	$clear_times_data = [$live_data['c_rank_complete'], $live_data['b_rank_complete'], $live_data['a_rank_complete'], $live_data['s_rank_complete']];
	$wip_live_data = [
		'live_difficulty_id' => $live_difficulty_id,
		'is_random' => $live_data['random_flag'] > 0,
		'dangerous' => $stage_data['stage_level'] > 10,
		'use_quad_point' => $live_data['special_setting']
	];
	$live_setting_id = $live_data['live_setting_id'];
	$live_guest_id = $wip_live['guest_user_id'];
}

$before_user_info = (include('modules/user/userInfo.php'))[1]['user'];
$player_tables = $DATABASE->execute_query("SELECT album_table, live_table, unit_table, present_table, deck_table, friend_list FROM `users` WHERE user_id = $USER_ID");

// get last live tracking data and goals data
$live_data_goals = [];	// [score_list, combo_list, clear_list] with format [live_goal_data, target]
$last_live_data = [
	'score' => 0,
	'combo' => 0,
	'clear' => 0,
	'normal' => 0
];
{
	$temp = $DATABASE->execute_query("SELECT * FROM `{$player_tables['live_table']}` WHERE live_id = $live_difficulty_id");
	
	if(count($temp) > 0)
	{
		$last_live_data['score'] = $temp['score'];
		$last_live_data['combo'] = $temp['combo'];
		$last_live_data['clear'] = $temp['times'];
		$last_live_data['normal'] = $temp['normal_live'];
	}
	
	// create live goals data
	$temp = $live_db->execute_query("SELECT c_rank_score, b_rank_score, a_rank_score, s_rank_score
		c_rank_combo, b_rank_combo, a_rank_combo, s_rank_combo FROM `live_setting_m` WHERE live_setting_id = $live_setting_id")[0];
	$goal_list = $live_db->execute_query("SELECT * FROM `live_goal_reward_m` WHERE live_difficulty_id = $live_difficulty_id
		ORDER BY live_goal_type ASC, rank DESC");
	
	$live_data_goals[0] = [
		[NULL, $temp['c_rank_score']],
		[NULL, $temp['b_rank_score']],
		[NULL, $temp['a_rank_score']],
		[NULL, $temp['s_rank_score']]
	];
	$live_data_goals[1] = [
		[NULL, $temp['c_rank_combo']],
		[NULL, $temp['b_rank_combo']],
		[NULL, $temp['a_rank_combo']],
		[NULL, $temp['s_rank_combo']]
	];
	$live_data_goals[2] = [
		[NULL, $clear_times_data[0]],
		[NULL, $clear_times_data[1]],
		[NULL, $clear_times_data[2]],
		[NULL, $clear_times_data[3]]
	];
	
	foreach($goal_list as $goal)
		$live_data_goals[$goal['live_goal_type'] - 1][0] = $goal
}


$score_total = $score_smile + $score_cute + $score_cool;
$live_score_rank = 5;
$live_combo_rank = 0;
$cleared_live_goals = [];
$cleared_live_rewards = [];

// get live rank and combo (including rewards)
{
	foreach($live_data_goals[0] as $score_goal)
	{
		if($score_total >= $score_goal[1] && $last_live_data['score'] < $score_goal[1])
		{
			item_collect($USER_ID, $score_goal[0]['add_type'], $score_goal[0]['item_id'], $score_goal[0]['amount']);
			
			$cleared_live_goals[] = $score_goal[0]['live_goal_reward_id'];
			$cleared_live_rewards[] = [
				'item_id' => $score_goal[0]['item_id'],
				'add_type' => $score_goal[0]['add_type'],
				'amount' => $score_goal[0]['amount'],
				'item_category_id' => $score_goal[0]['item_category_id']
			];
			$live_score_rank = $score_goal[0]['rank'];
		}
	}
	
	foreach($live_data_goals[1] as $combo_goal)
	{
		if($max_combo >= $combo_goal[1] && $last_live_data['combo'] < $combo_goal[1])
		{
			item_collect($USER_ID, $combo_goal[0]['add_type'], $combo_goal[0]['item_id'], $combo_goal[0]['amount']);
			
			$cleared_live_goals[] = $combo_goal[0]['live_goal_reward_id'];
			$cleared_live_rewards[] = [
				'item_id' => $combo_goal[0]['item_id'],
				'add_type' => $combo_goal[0]['add_type'],
				'amount' => $combo_goal[0]['amount'],
				'item_category_id' => $combo_goal[0]['item_category_id']
			];
			$live_combo_rank = $combo_goal[0]['rank'];
		}
	}
	
	$play_times = $last_live_data['clear'] + 1;
	foreach($live_data_goals[2] as $clear_goal)
	{
		if($play_times == $clear_goal[1])
		{
			item_collect($USER_ID, $clear_goal[0]['add_type'], $clear_goal[0]['item_id'], $clear_goal[0]['amount']);
			
			$cleared_live_goals[] = $clear_goal[0]['live_goal_reward_id'];
			$cleared_live_rewards[] = [
				'item_id' => $clear_goal[0]['item_id'],
				'add_type' => $clear_goal[0]['add_type'],
				'amount' => $clear_goal[0]['amount'],
				'item_category_id' => $clear_goal[0]['item_category_id']
			];
		}
	}
	
	// modify last live table
	$put_score = max($score_total, $last_live_data['score']);
	$put_combo = max($max_combo, $last_live_data['combo']);
	$DATABASE->execute_query("REPLACE INTO `{$player_tables['live_table']}` VALUES($live_difficulty_id, {$last_live_data['normal']}, $put_score, $put_combo, $play_times");
}

// increase EXP, add gold, and add FP
$player_data_info = NULL;
$player_exp = 0;
$player_gained_fp = 5;
$player_gained_gold = $live_coin_table[$live_score_rank][min($live_difficulty_level, 4) - 1];
{
	if($live_score_rank == 5)
		// half exp
		$player_exp = intval(ceil($player_exp / 2));
	
	if(array_search(strval($guest_user_id), explode(',', $player_tables['friend_list']) ?: []) !== false)
		$player_gained_fp = 10;
	
	$player_data_info = user_add_exp($USER_ID, $player_exp);
	$DATABASE->execute_query("UPDATE `users` SET gold = gold + $player_gained_gold, friend_point = friend_point + $player_gained_fp WHERE user_id = $USER_ID");
}

// return data
return [
	[
		'live_info' => [$wip_live_data],
		'rank' => $live_score_rank,
		'combo_rank' => $live_combo_rank,
		'total_love' => $love_cnt,
		'is_high_score' => $score_total > $last_live_data['score'],
		'hi_score' => max($score_total, $last_live_data['score']);
		'base_reward_info' => [
			'player_exp' => $player_exp,
			'player_exp_unit_max' => [
				// TODO
				// before =>
				// after =>
			],
			'player_exp_friend_max' => [
				'before' => $player_data_info['before']['max_friend'],
				'after' => $player_data_info['after']['max_friend']
			],
			'player_exp_lp_max' => [
				'before' => $player_data_info['before']['max_lp'],
				'after' => $player_data_info['after']['max_lp']
			],
			'game_coin' => $player_gained_gold,
			'game_coin_reward_box_flag' => false,
			'social_point' => $player_gained_fp
		],
	],
	200
];

<?php
$is_int_list = function(int& ...$varlist): bool
{
	foreach($varlist as &$x)
		if(is_int($x) == false)
			return false;
	
	return true;
};

$live_coin_table = [	// index: [score_rank][difficulty]
	[600 , 800 , 1000, 1200],
	[1200, 1600, 2000, 2400],
	[1800, 2400, 3000, 3600],
	[2250, 3000, 3750, 4500],
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
// $live_notes_data = json_decode(file_get_contents("data/notes/$live_difficulty_id.json"), true);
$clear_times_data = NULL;
$live_setting_id = 0;
$live_difficulty_level = 1;	// not live_difficulty_id
$live_guest_id = 0;
$used_deck_id = 0;
$player_exp = 0;

// get data from WIP live
{
	$wip_live = npps_query("SELECT * FROM `wip_live` WHERE user_id = $USER_ID");
	
	if(count($wip_live) == 0)
	{
		echo 'No live in progress!';
		return false;
	}
	
	$wip_live = $wip_live[0];
	if($wip_live['live_id'] != $live_difficulty_id)
	{
		echo 'Invalid live_difficulty_id!';
		return false;
	}
	
	npps_query("DELETE FROM `wip_live` WHERE user_id = $USER_ID");
	
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
		'use_quad_point' => $live_data['special_setting'] > 0
	];
	$live_setting_id = $live_data['live_setting_id'];
	$live_guest_id = $wip_live['guest_user_id'];
	$used_deck_id = $wip_live['deck_num'];
}

$before_user_info = user_current_info($USER_ID);
$player_tables = npps_query("SELECT album_table, live_table, unit_table, present_table, deck_table, friend_list FROM `users` WHERE user_id = $USER_ID")[0];

// get last live tracking data and goals data
$live_data_goals = [];	// [score_list, combo_list, clear_list] with format [live_goal_data, target]
$last_live_data = [
	'score' => 0,
	'combo' => 0,
	'clear' => 0,
	'normal' => 0
];
{
	$temp = npps_query("SELECT * FROM `{$player_tables['live_table']}` WHERE live_id = $live_difficulty_id");
	
	if(count($temp) > 0)
	{
		$temp = $temp[0];
		$last_live_data['score'] = $temp['score'];
		$last_live_data['combo'] = $temp['combo'];
		$last_live_data['clear'] = $temp['times'];
		$last_live_data['normal'] = $temp['normal_live'];
	}
	
	// create live goals data
	$temp = $live_db->execute_query("SELECT c_rank_score, b_rank_score, a_rank_score, s_rank_score, 
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
		$live_data_goals[$goal['live_goal_type'] - 1][4 - $goal['rank']][0] = $goal;
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
		if($score_total >= $score_goal[1])
		{
			$live_score_rank = $score_goal[0]['rank'];
			
			if($last_live_data['score'] < $score_goal[1])
			{
				item_collect($USER_ID, $score_goal[0]['add_type'], $score_goal[0]['item_id'], $score_goal[0]['amount']);
				
				$cleared_live_goals[] = $score_goal[0]['live_goal_reward_id'];
				$cleared_live_rewards[] = [
					'item_id' => $score_goal[0]['item_id'],
					'add_type' => $score_goal[0]['add_type'],
					'amount' => $score_goal[0]['amount'],
					'item_category_id' => $score_goal[0]['item_category_id']
				];
				
			}
		}
	}
	
	foreach($live_data_goals[1] as $combo_goal)
	{
		if($max_combo >= $combo_goal[1])
		{
			$live_combo_rank = $combo_goal[0]['rank'];
			
			if($last_live_data['combo'] < $combo_goal[1])
			{
				item_collect($USER_ID, $combo_goal[0]['add_type'], $combo_goal[0]['item_id'], $combo_goal[0]['amount']);
				
				$cleared_live_goals[] = $combo_goal[0]['live_goal_reward_id'];
				$cleared_live_rewards[] = [
					'item_id' => $combo_goal[0]['item_id'],
					'add_type' => $combo_goal[0]['add_type'],
					'amount' => $combo_goal[0]['amount'],
					'item_category_id' => $combo_goal[0]['item_category_id']
				];
			}
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
	npps_query("REPLACE INTO `{$player_tables['live_table']}` VALUES($live_difficulty_id, {$last_live_data['normal']}, $put_score, $put_combo, $play_times)");
}

// increase EXP, add gold, and add FP
$player_data_info = NULL;
$player_gained_fp = 5;
$player_gained_gold = $live_coin_table[$live_score_rank - 1][min($live_difficulty_level, 4) - 1];
$player_exp_unit_max = 90;
{
	if($live_score_rank == 5)
		// half exp
		$player_exp = intval(ceil($player_exp / 2));
	
	if(array_search(strval($live_guest_id), explode(',', $player_tables['friend_list']) ?: []) !== false)
		$player_gained_fp = 10;
	
	$player_exp_unit_max = npps_query("SELECT max_unit FROM `users` WHERE user_id = $USER_ID")[0][0];
	$player_data_info = user_add_exp($USER_ID, $player_exp);
	
	npps_query("UPDATE `users` SET gold = gold + $player_gained_gold, friend_point = friend_point + $player_gained_fp WHERE user_id = $USER_ID");
}

// Live members
$reward_members = [
	'live_clear' => [],
	'live_rank' => [],
	'live_combo' => []
];
$common_present_box_data = [
	'message' => 'Live Show! Reward',
	'expire' => NULL
];
{
	// Live clear
	$member = card_random_regular();
	$member['add_type'] = 1001;
	$member['unit_owning_user_id'] = card_add($USER_ID, $member['unit_id'], $common_present_box_data);
	if($member['unit_owning_user_id'] == 0)
		$member['unit_owning_user_id'] = token_use_pseudo_unit_own_id($TOKEN);
	
	$reward_members['live_clear'][0] = $member;
	
	// At least C score
	if($live_score_rank < 5)
	{
		$member = card_random_regular();
		$member['add_type'] = 1001;
		$member['unit_owning_user_id'] = card_add($USER_ID, $member['unit_id'], $common_present_box_data);
		if($member['unit_owning_user_id'] == 0)
			$member['unit_owning_user_id'] = token_use_pseudo_unit_own_id($TOKEN);
		
		$reward_members['live_rank'][0] = $member;
	}
	
	// At least C combo
	if($live_combo_rank > 0)
	{
		$member = card_random_regular();
		$member['add_type'] = 1001;
		$member['unit_owning_user_id'] = card_add($USER_ID, $member['unit_id'], $common_present_box_data);
		if($member['unit_owning_user_id'] == 0)
			$member['unit_owning_user_id'] = token_use_pseudo_unit_own_id($TOKEN);
		
		$reward_members['live_combo'][0] = $member;
	}
}

// Calculate bond
$deck_data = [];
$unlocked_subscenario = [];
{
	$remaining_bond = $love_cnt;
	
	// get current deck
	{
		$unit_ids = explode(':', npps_query("SELECT deck_members FROM `{$player_tables['deck_table']}` WHERE deck_num = $used_deck_id")[0][0]);
		
		// convert unit_ids to number
		array_walk($unit_ids, function(&$v, $k)
		{
			$v = intval($v);
		});
		
		$comma_separated_units = implode(',', $unit_ids);
		$units = npps_query("SELECT * FROM `{$player_tables['unit_table']}` WHERE unit_id IN($comma_separated_units) ".$DATABASE->custom_ordering('unit_id', $unit_ids));
		
		foreach($units as $i => $unit)
		{
			$unit_info = $unit_db->execute_query("SELECT after_level_max, after_love_max FROM `unit_m` WHERE unit_id = {$unit['card_id']}")[0];
			
			$deck_data[] = [
				'unit_owning_user_id' => $unit['unit_id'],
				'unit_id' => $unit['card_id'],
				'position' => $i + 1,
				'level' => $unit['level'],
				'unit_skill_level' => $unit['skill_level'],
				'before_love' => $unit['bond'],
				'love' => $unit['bond'],
				'max_love' => $unit['max_bond'],
				'is_rank_max' => $unit['max_bond'] >= $unit_info['after_love_max'],
				'is_love_max' => $unit['bond'] >= $unit_info['after_love_max'],
				'is_level_max' => $unit['level'] >= $unit_info['after_level_max']
			];
		}
	}
	
	// now calculate
	$center_bond = intdiv($remaining_bond * 7, 10);
	$remaining_bond -= $center_bond;
	
	// add to leader
	$deck_data[4]['love'] += $center_bond;
	// add remaining_bond back if there's leftover
	$remaining_bond += max($deck_data[4]['love'] - $deck_data[4]['max_love'], 0);
	
	// calculate for the rest of the members
	$bond_finish_calculate = false;
	$common_bond_pos_arr = [0, 1, 2, 3, 5, 6, 7, 8];
	while($remaining_bond > 0 && $bond_finish_calculate == false)
	{
		$bond_finish_calculate = true;
		
		foreach($common_bond_pos_arr as $pos)
		{
			if($remaining_bond > 0)
				if($deck_data[$pos]['love'] < $deck_data[$pos]['max_love'])
				{
					$deck_data[$pos]['love']++;
					$remaining_bond--;
					$bond_finish_calculate = false;
				}
			else
				break;
		}
	}
	
	// if there's leftover again, give it to center
	if($remaining_bond > 0)
		$deck_data[4]['love'] = min($deck_data[4]['love'] + $remaining_bond, $deck_data[$pos]['max_love']);
	
	// update is_love_max table and update database and album
	npps_query('BEGIN');
	foreach($deck_data as &$unit)
	{
		$unit['is_love_max'] = $unit['is_rank_max'] ? ($unit['love'] >= $unit['max_love']) : false;
		$album_data = npps_query("SELECT total_bond, flags FROM `{$player_tables['album_table']}` WHERE card_id = {$unit['unit_id']}")[0];
		
		$album_data['total_bond'] += $unit['love'] - $unit['before_love'];
		$album_data['flags'] |= ($unit['is_love_max'] ? 4 : 0);
		
		if($unit['is_love_max'])
		{
			// unlock subscenario
			$subscenario = user_subscenario_unlock($USER_ID, $unit['unit_id']);
			
			if($subscenario > 0)
				$unlocked_subscenario[] = $subscenario;
		}
		
		npps_query("UPDATE `{$player_tables['album_table']}` SET flags = ?, total_bond = ? WHERE card_id = {$unit['unit_id']}",
			'ii', $album_data['flags'], $album_data['total_bond']);
		npps_query("UPDATE `{$player_tables['unit_table']}` SET bond = {$unit['love']} WHERE unit_id = {$unit['unit_owning_user_id']}");
	}
	npps_query('COMMIT');
}

// next_level_info data
$next_level_info_data = [];
{
	if($player_data_info['before']['level'] == $player_data_info['after']['level'])
		$next_level_info_data[] = [
			'level' => $player_data_info['before']['level'],
			'from_exp' => user_exp_requirement_recursive($player_data_info['before']['level'] - 1) + $player_data_info['before']['exp']
		];
	else
	{
		$next_level_info_data[] = [
			'level' => $player_data_info['before']['level'],
			'from_exp' => user_exp_requirement_recursive($player_data_info['before']['level'] - 1) + $player_data_info['before']['exp']
		];
		$next_level_info_data[] = [
			'level' => $player_data_info['after']['level'],
			'from_exp' => user_exp_requirement($player_data_info['after']['level'] - 1) + $player_data_info['after']['exp']
		];
	}
}

// transform score rank and combo rank
/*
if($live_score_rank < 5)
	$live_score_rank = 5 - $live_score_rank;

if($live_combo_rank > 0)
	$live_combo_rank = 5 - $live_combo_rank;
*/

// Calculate event points
// Source: http://decaf.kouhi.me/lovelive/index.php?title=Gameplay#New_events_.28after_June_5.2C_2016.29
$event_points_table = [	// [difficulty][score_rank][combo_rank]
	[
		// Easy
		// Format: no combo, S combo, A combo, B combo, C combo
		[66, 71, 70, 69, 67],	// S score
		[64, 70, 68, 66, 65],	// A score
		[63, 68, 66, 65, 64],	// B score
		[60, 64, 63, 62, 61],	// C score
		[57, 61, 60, 59, 58]	// Less than C score
	],
	[
		// Normal
		[137, 148, 145, 143, 140],
		[133, 143, 140, 137, 135],
		[125, 137, 135, 132, 129],
		[121, 131, 128, 126, 123],
		[114, 124, 122, 120, 117]
	],
	[
		// Hard
		[237, 261, 254, 246, 241],
		[226, 249, 242, 235, 230],
		[215, 237, 231, 224, 220],
		[204, 226, 219, 213, 209],
		[194, 214, 207, 202, 197]
	],
	[
		// Expert and above
		[498, 565, 549, 518, 508],
		[475, 540, 525, 495, 485],
		[448, 509, 495, 467, 458],
		[426, 484, 470, 444, 435],
		[403, 459, 446, 421, 413]
	]
];
$event_info_data = [];
// TODO: Complete it!!!
/*
if($event_id > 0)
{
	$event_db = npps_get_database('event');
	$event_data = npps_query(
<<<QUERY
	SELECT  easy_song_list, normal_song_list, hard_song_list, expert_song_list, event_ranking_data, event_song_data
	FROM `event_list` WHERE
		event_id = $event_id AND
		event_start <= $UNIX_TIMESTAMP AND
		event_end > $UNIX_TIMESTAMP AND
		token_image IS NOT NULL
QUERY;
	);
	
	if(count($event_data) > 0)
	{
		$event_info_data = [
			'event_id' => $event_id,
			
		];
		$easylist = npps_separate(',', $event_data['easy_song_list']);
		$normallist = npps_separate(',', $event_data['normal_song_list']);
		$hardlist = npps_separate(',', $event_data['hard_song_list']);
		$expertlist = npps_separate(',', $event_data['expert_song_list']);
		
		if(array_search($live_difficulty_id, $easylist) !== false ||
		   array_search($live_difficulty_id, $normallist) !== false ||
		   array_search($live_difficulty_id, $hardlist) !== false ||
		   array_search($live_difficulty_id, $expertlist) !== false)
		{
			// event song is in progress
		}
	}
}*/

// return data
return [
	[
		'live_info' => [$wip_live_data],
		'rank' => $live_score_rank,
		'combo_rank' => $live_combo_rank,
		'total_love' => $love_cnt,
		'is_high_score' => $score_total > $last_live_data['score'],
		'hi_score' => max($last_live_data['score'], $score_total),
		'base_reward_info' => [
			'player_exp' => $player_exp,
			'player_exp_unit_max' => [
				// TODO
				'before' => $player_exp_unit_max,
				'after' => $player_exp_unit_max
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
		'reward_unit_list' => $reward_members,
		'unlocked_subscenario_ids' => $unlocked_subscenario,
		'unit_list' => $deck_data,
		'before_user_info' => $before_user_info,
		'after_user_info' => user_current_info($USER_ID),
		'next_level_info' => $next_level_info_data,
		'goal_accomp_info' => [
			'achieved_ids' => $cleared_live_goals,
			'rewards' => $cleared_live_rewards
		],
		'special_reward_info' => [],	// TODO
		'event_info' => $event_info_data,
	],
	200
];

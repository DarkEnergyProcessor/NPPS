<?php
$friend_list = npps_query("SELECT friend_list FROM `users` WHERE user_id = $USER_ID")[0][0];
$random_people = npps_query("SELECT user_id FROM `users` WHERE level > 1 AND user_id <> $USER_ID AND user_id NOT IN($friend_list) ORDER BY RANDOM() LIMIT 3");
$unit_db = npps_get_database('unit');
$live_db = npps_get_database('live');
$live_id = intval($REQUEST_DATA['live_difficulty_id'] ?? 0);
$party_list_out = [];

$live_db->execute_query('ATTACH DATABASE `data/event/marathon.db_` AS `marathon`');

// verify live cost
{
	$temp = $live_db->execute_query("SELECT live_setting_id, capital_type, capital_value, random_flag, special_setting FROM (
		SELECT live_difficulty_id, live_setting_id, capital_type, capital_value, 0 as random_flag, 0 as special_setting FROM `normal_live_m` UNION
		SELECT live_difficulty_id, live_setting_id, capital_type, capital_value, 0 as random_flag, 0 as special_setting FROM `special_live_m` UNION
		SELECT live_difficulty_id, live_setting_id, capital_type, capital_value, random_flag, special_setting FROM `event_marathon_live_m`
		) WHERE live_difficulty_id = $live_id");
	
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
			break;
		}
		case 2:
		{
			$current_token = 0;
			$needed_token = $temp['capital_value'];
			$ev_rank_table = npps_query("SELECT event_ranking_table FROM `event_list` WHERE token_image IS NOT NULL AND event_start <= $UNIX_TIMESTAMP AND event_close > $UNIX_TIMESTAMP");
			
			// get current token
			if(count($ev_rank_table) == 0)
			{
				echo 'No active event!';
				return false;
			}
			else
				$ev_rank_table = $ev_rank_table[0][0];
			
			$user_event_info = npps_query("SELECT current_token FROM `$ev_rank_table` WHERE user_id = $USER_ID");
			
			if(count($user_event_info) > 0)
				$current_token = $user_event_info['current_token'];
			
			if($current_token < $needed_token)
				return 3412;	// Not enough token
			break;
		}
	}
}

$return_user_info = function(int $user_id, bool $is_friend = false): array
{
	$basic_info = user_get_basic_info($user_id);
	return [
		'user_info' => [
			'user_id' => $user_id,
			'name' => $basic_info['name'],
			'level' => $basic_info['level']
		],
		'center_unit_info' => [
			'unit_id' => $basic_info['unit_info']['unit_id'],
			'level' => $basic_info['unit_info']['level'],
			'smile' => $basic_info['unit_info']['smile'],
			'cute' => $basic_info['unit_info']['pure'],
			'cool' => $basic_info['unit_info']['cool'],
			'max_hp' => $basic_info['unit_info']['hp'],
			'unit_skill_level' => $basic_info['unit_info']['skill_level'],
			'love' => $basic_info['unit_info']['bond'],
			'is_rank_max' => $basic_info['unit_info']['idolized'],
			'is_love_max' => $basic_info['unit_info']['bond_max'],
			'is_level_max' => $basic_info['unit_info']['level_max']
		],
		'setting_award_id' => $basic_info['badge'],
		'available_social_point' => $is_friend ? 10 : 5,
		'friend_status' => intval($is_friend)
	];
};

if(strlen($friend_list) > 0)
	foreach(explode(',', $friend_list) as $fuid)
		$party_list_out[] = $return_user_info(intval($fuid), true);

foreach($random_people as $uids)
	$party_list_out[] = $return_user_info($uids[0]);

return [
	[
		'party_list' => $party_list_out
	],
	200
];

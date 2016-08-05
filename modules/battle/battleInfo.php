<?php
$score_match_list = [];

foreach($DATABASE->execute_query('SELECT event_id, event_ranking_table, easy_song_list, normal_song_list, hard_song_list, expert_song_list, technical_song_list, easy_lp, normal_lp, hard_lp, expert_lp, technical_lp FROM `event_list` WHERE token_image IS NULL AND is_medfes = 0') as $ev)
{
	$event_point = 0;
	$diff_list = [];
	
	if($user_event_info = $DATABASE->execute_query("SELECT total_points FROM `{$ev[1]}` user_id = $USER_ID"))
		if(count($user_event_info) > 0)
			$event_point = $user_event_info[0][0];
	
	for($i = 2; $i <= 6; $i++)
		if($ev[$i] != NULL)
			$diff_list[] = [
				'difficulty' => $i - 1,
				'capital_type' => 1,
				'capital_value' => $ev[$i + 5]
			];
	
	$score_match_list[] = [
		'event_id' => $ev[0],
		'point_name' => 'nil',
		'event_point' => 0,
		'total_event_point' => $event_point,
		'event_battle_difficulty_m' => $diff_list
	];
}

return [
	$score_match_list,
	200
];
?>
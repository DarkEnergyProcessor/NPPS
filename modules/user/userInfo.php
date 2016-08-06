<?php
$user_info = $DATABASE->execute_query(<<<'QUERY'
	SELECT
		name,
		level,
		current_exp,
		next_exp,
		gold,
		paid_loveca,
		free_loveca,
		friend_point,
		max_unit,
		max_lp,
		full_lp_recharge,
		overflow_lp,
		max_friend,
		invite_code,
		tutorial_state
	FROM `users` WHERE user_id = ?
QUERY
	, 'i', $USER_ID)[0];

$lp_time_charge = $user_info[10] - $UNIX_TIMESTAMP;
$total_lp = intdiv($lp_time_charge, 360);

if($lp_time_charge < 0 || $user_info[11] > 0)
{
	$lp_time_charge = 0;
	$total_lp = $user_info[9];
}

$total_lp += $user_info[11];

user_set_last_active($USER_ID, $TOKEN);

return [
	[
		"user" => [
			"user_id" => $USER_ID,
			"name" => $user_info[0],												// name
			"level" => $user_info[1],												// level
			"exp" => $user_info[2],													// current_exp
			"previous_exp" => $user_info[3] - user_exp_requirement($user_info[1]),	// next_exp - level
			"next_exp" => $user_info[3],											// next_exp
			"game_coin" => $user_info[4],											// gold
			"sns_coin" => $user_info[5] + $user_info[6],							// paid_loveca + free_loveca
			"paid_sns_coin" => $user_info[5],										// paid_loveca
			"free_sns_coin" => $user_info[6],										// free_loveca
			"social_point" => $user_info[7],										// friend_point
			"unit_max" => $user_info[8],											// max_unit
			"energy_max" => $user_info[9],											// max_lp
			"energy_full_time" => to_datetime($user_info[10]),						// full_lp_recharge
			"energy_full_need_time" => $lp_time_charge,								// full_lp_recharge - time()
			"over_max_energy" => $total_lp,											// overflow_lp
			"friend_max" => $user_info[12],											// max_friend
			"invite_code" => $user_info[13],										// invite_code
			"tutorial_state" => $user_info[14]										// tutorial_state
		]
	],
	200
];
?>
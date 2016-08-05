<?php
$target_idolize = intval($REQUEST_DATA['unit_owning_user_ids'][0] ?? 0);
$base_idolize = intval($REQUEST_DATA['base_owning_unit_user_id'] ?? 0);

if($target_idolize == $base_idolize)
{
	echo 'Invalid idolize member ID!';
	return false;
}

$unit_db = new SQLite3Database('data/unit.db_');
$user_tables = $DATABASE->execute_query("SELECT album_table, unit_table, deck_table FROM `users` WHERE user_id = $USER_ID")[0];
$album_table = $user_tables[0];
$unit_table = $user_tables[1];
$deck_table = $user_tables[2];

$base_info = NULL;
$target_unit_id = 0;
$base_unit_id = 0;
{
	$temp = $DATABASE->execute_query("SELECT card_id, current_exp, next_exp, level, max_level, skill_level, bond, max_bond, health_points, favorite FROM `$unit_table` WHERE unit_id = $base_idolize OR unit_id = $target_idolize ".$DATABASE->custom_ordering('unit_id', $base_idolize, $target_idolize));
	
	$target_unit_id = $temp[1][0];
	$base_unit_id = $temp[0][0];
	$base_info = [
		'unit_owning_user_id' => $base_idolize,
		'unit_id' => $temp[0][0],
		'exp' => $temp[0][1],
		'next_exp' => $temp[0][2],
		'level' => $temp[0][3],
		'max_level' => $temp[0][4],
		'rank' => 1,		// Should be.
		'max_rank' => 2,	// Should be.
		'love' => $temp[0][6],
		'max_love' => $temp[0][7],
		'unit_skill_level' => $temp[0][5],
		'max_hp' => $temp[0][8],
		'favorite_flag' => $temp[0][9],
		'is_rank_max' => false,
		'is_love_max' => false,
		'is_level_max' => false
	];
}

if($target_unit_id != $base_unit_id)
{
	echo 'Card type doesn\'t match!';
	return false;
}

$user_info = NULL;
{
	$temp = $DATABASE->execute_query("SELECT level, current_exp, next_exp, gold, paid_loveca + free_loveca, friend_point, max_unit, max_lp, max_friend FROM `users` WHERE user_id = $USER_ID")[0];
	
	$user_info = [
		'level' => $temp[0],
		'exp' => $temp[1],
		'next_exp' => $temp[2],
		'game_coin' => $temp[3],
		'sns_coin' => $temp[4],
		'social_point' => $temp[5],
		'unit_max' => $temp[6],
		'energy_max' => $temp[7],
		'friend_max' => $temp[8]
	];
}

$idolize_info = $unit_db->execute_query("SELECT rank_up_cost, after_love_max, after_level_max FROM `unit_m` WHERE unit_id = $base_unit_id")[0];

/* If it's already idolized, error */
if($base_info['max_level'] >=  $idolize_info[2])
{
	echo 'Already idolized!';
	return false;
}

/* Also, if player doesn't have enough money, error */
if($user_info['game_coin'] < $idolize_info[0])
{
	echo 'Not enough money!';
	return false;
}

/* Deduce player money */
$new_user_info = array_merge([], $user_info);
$new_user_info['game_coin'] -= $idolize_info[0];
$DATABASE->execute_query("UPDATE `users` SET gold = gold - {$idolize_info[0]} WHERE user_id = $USER_ID");

/* Set idolized flag */
$new_base_info = array_merge([], $base_info);
$new_base_info['rank'] = 2;
$new_base_info['is_rank_max'] = true;
$DATABASE->execute_query("UPDATE `$unit_table` SET max_bond = ?, max_level = ? WHERE unit_id = $base_idolize", 'ii', $idolize_info[1], $idolize_info[2]);

/* Update in album */
{
	$flags = $DATABASE->execute_query("SELECT flags FROM `$album_table` WHERE card_id = $base_unit_id")[0][0];
	
	if(($flags & 2) == 0)
		$DATABASE->execute_query("UPDATE `$album_table` SET flags = ? WHERE card_id = ?", 'ii', $flags | 2, $base_unit_id);
}

card_remove($USER_ID, $target_idolize);
user_set_last_active($USER_ID, $TOKEN);

return [
	[
		'before' => $base_info,
		'after' => $new_base_info,
		'before_user_info' => $user_info,
		'after_user_info' => $new_user_info,
		'use_game_coin' => $idolize_info[0],
		'open_subscenario_id' =>  NULL,
		'get_exchange_point_list' => []
	],
	200
];
?>
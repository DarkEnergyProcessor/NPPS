<?php
$unit_db = new SQLite3Database('data/unit.db_');
$unit_table = $DATABASE->execute_query("SELECT unit_table FROM `users` WHERE user_id = $USER_ID")[0][0];
$unit_list = [];

foreach($DATABASE->execute_query("SELECT * FROM `$unit_table`") as $unit)
{
	$rarity = $unit_db->execute_query("SELECT rarity FROM `unit_m` WHERE unit_id = {$unit[1]}")[0][0];
	$is_promo = count($unit_db->execute_query("SELECT unit_id FROM `unit_m` WHERE unit_id = {$unit[1]} AND normal_card_id = rank_max_card_id")) > 0;
	$is_idolized = $unit_db->execute_query("SELECT after_level_max FROM `unit_m` WHERE unit_id = {$unit[1]}")[0][0] == $unit[5];
	
	$unit_list[] = [
		'unit_owning_user_id' => $unit[0],
		'unit_id' => $unit[1],
		'exp' => $unit[2],
		'next_exp' => $unit[3],
		'level' => $unit[4],
		'max_level' => $unit[5],
		'rank' => $is_promo || $is_idolized ? 2 : 1,
		'max_rank' => 2,
		'love' => $unit[9],
		'max_love' => $unit[10],
		'unit_skill_level' => $unit[6],
		'max_hp' => $unit[8],
		'is_rank_max' => $is_idolized,
		'favorite_flag' => $unit[11] > 0,
		'is_love_max' => $is_idolized ? $unit[9] >= $unit[10] : false,
		'is_level_max' => $is_idolized ? $unit[4] >= $unit[5] : false,
		'is_skill_level_max' => $rarity > 1 ? $unit[6] >= 8 : true,
		'insert_date' => to_datetime($unit[12])
	];
}

return [
	$unit_list,
	200
];
?>
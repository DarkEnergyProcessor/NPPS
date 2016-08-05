<?php
$album_table = $DATABASE->execute_query("SELECT album_table FROM `users` WHERE user_id = $USER_ID")[0][0];
$album_out = [];

foreach($DATABASE->execute_query("SELECT * FROM `$album_table`") as $album)
{
	$flags = $album[1];
	$album_out[] = [
		'unit_id' => $album[0],
		'rank_max_flag' => ($flags & 2) > 0,
		'love_max_flag' => ($flags & 4) > 0,
		'rank_level_max_flag' => ($flags & 8) > 0,
		'all_max_flag' => $flags == 15,
		'highest_love_per_unit' => $album[2],
		'total_love' => $album[2]
	];
}

return [
	$album_out,
	200
];
?>
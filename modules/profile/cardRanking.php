<?php
$target_user_id = intval($REQUEST_DATA['user_id']);

if($target_user_id == 0)
{
	echo 'Invalid user ID';
	return false;
}

$out = [];
$album_table = $DATABASE->execute_query("SELECT album_table FROM `users` WHERE user_id = $target_user_id")[0][0];

foreach($DATABASE->execute_query("SELECT card_id, flags, total_bond FROM `$album_table` ORDER BY total_bond DESC LIMIT 10") as $cid)
{
	$idolized = ($cid[1] & 2) > 0;
	$out[] = [
		'unit_id' => $cid[0],
		'total_love' => $cid[2],
		'rank' => $idolized ? 2 : 1
	];
}

return [
	$out,
	200
];
?>
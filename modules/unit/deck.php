<?php
$modify_deck_list = $REQUEST_DATA['unit_deck_list'];
$deck_table = $DATABASE->execute_query("SELECT deck_table FROM `users` WHERE user_id = $USER_ID")[0][0];

foreach($modify_deck_list as $list)
{
	$pos = [0, 0, 0, 0, 0, 0, 0, 0, 0];
	
	foreach($list['unit_deck_detail'] as $units)
		$pos[$units['position'] - 1] = intval($units['unit_owning_user_id']);
	
	$pos_out = implode(':', $pos);
	
	if(strlen($list['deck_name']) > 0 && mb_strlen($list['deck_name']) <= 10)
		$DATABASE->execute_query("UPDATE `$deck_table` SET deck_name = ?, deck_members = ? WHERE deck_num = ?", 'ssi', $list['deck_name'], $pos_out, $list['unit_deck_id']);
	else
		$DATABASE->execute_query("UPDATE `$deck_table` SET deck_members = ? WHERE deck_num = ?", 'si', $pos_out, $list['unit_deck_id']);
	
	if($list['main_flag'] == 1)
		$DATABASE->execute_query("UPDATE `users` SET main_deck = ? WHERE user_id = $USER_ID", 'i', $list['unit_deck_id']);
}

return [
	[],
	200
];
?>
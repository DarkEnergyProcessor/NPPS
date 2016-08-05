<?php
$unit_initial_id = intval($REQUEST_DATA['unit_initial_set_id'] ?? 0);

if($unit_initial_id <= 0 || $unit_initial_id > 9)
{
	echo 'Invalid unit initial ID!';
	return false;
}

$unit_deck = [13, 9, 8, 23, $unit_initial_id + 48, 24, 21, 20, 19];
$unit_own_ids = [];

foreach($unit_deck as $i)
{
	$id = card_add_direct($USER_ID, $i);
	
	if($id > 0)
		$unit_own_ids[] = $id;
	else
		break;
}

if(count($unit_own_ids) != 9)
{
	echo 'Failed to add some cards!';
	http_response_code(500);
	return false;
}

$DATABASE->execute_query('UPDATE `users` SET first_choosen = ? WHERE user_id = ?', 'ii', $unit_initial_id + 48, $USER_ID);

if(!deck_alter($USER_ID, 1, $unit_own_ids))
{
	echo 'Failed to set deck';
	http_response_code(500);
	return false;
}

user_set_last_active($USER_ID, $TOKEN);

return [
	[
		'unit_id' => $unit_deck
	],
	200
];
?>
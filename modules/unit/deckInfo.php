<?php
$deck_info = $DATABASE->execute_query("SELECT deck_table, main_deck FROM `users` WHERE user_id = $USER_ID")[0];
$deck_data = [];

foreach($DATABASE->execute_query("SELECT * FROM `{$deck_info[0]}` WHERE deck_members <> \"0:0:0:0:0:0:0:0:0\"") as $deck)
{
	$deck_members = [];
	
	foreach(explode(':', $deck[2]) as $index => $units)
		if($units != 0)
			$deck_members[] = [
				'position' => $index + 1,
				'unit_owning_user_id' => intval($units)
			];
	
	$deck_data[] = [
		'unit_deck_id' => $deck[0],
		'main_flag' => $deck_info[1] == $deck[0],
		'deck_name' => $deck[1],
		'unit_owning_user_ids' => $deck_members
	];
}

return [
	$deck_data,
	200
];
?>
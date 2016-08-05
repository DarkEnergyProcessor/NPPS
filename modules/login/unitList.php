<?php
// Template N card list.
$template_deck = [13, 9, 8, 23, 0, 24, 21, 20, 19];
$unit_list = [];

for($i = 49; $i < 58; $i++)
{
	$new_deck = array_merge([], $template_deck);
	$new_deck[4] = $i;
	
	$unit_list[] = [
		'unit_initial_set_id' => $i - 48,
		'unit_list' => $new_deck,
		'center_unit_id' => $i
	];
}

return [
	[
		'unit_initial_set' => $unit_list
	],
	200
];
?>
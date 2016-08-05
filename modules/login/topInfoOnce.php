<?php
$goals_table = $DATABASE->execute_query("SELECT assignment_table FROM `users` WHERE user_id = $USER_ID")[0][0];
$count = $DATABASE->execute_query("SELECT SUM(new_flag = 1), SUM(complete_flag = 1) FROM `$goals_table`")[0];

return [
	[
		'new_achievement_cnt' => $count[0] ?? 0,
		'unaccomplished_achievement_cnt' => $count[1] ?? 0
	],
	200
];
?>
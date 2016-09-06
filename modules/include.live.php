<?php
/*
 * Null Pointer Private Server
 * Live shows!
 */

function live_unlock(int $user_id, int $live_id): bool
{
	global $DATABASE;
	
	$live_table = $DATABASE->execute_query("SELECT live_table FROM `users` WHERE user_id = $user_id")[0][0];
	return $DATABASE->execute_query("INSERT INTO `$live_table` (live_id) VALUES (?)", 'i', $live_id);
}

/* returns array: score, combo, clear. clear = 0 means never played (add "new" label) */
function live_get_info(int $user_id, int $live_id): array
{
	global $DATABASE;
	
	$out = [
		'score' => 0,
		'combo' => 0,
		'clear' => 0
	];
	
	$live_table = $DATABASE->execute_query("SELECT live_table FROM `users` WHERE user_id = $user_id")[0][0];
	$data = $DATABASE->execute_query("SELECT * FROM `$live_table` WHERE live_id = $live_id");
	
	if(count($data) > 0)
	{
		$data = $data[0];
		
		$out['score'] = $data['score'];
		$out['combo'] = $data['combo'];
		$out['clear'] = $data['times'];
	}
	
	return $out;
}

/* Returns list of current daily rotation in group. */
/* The ID returned is live difficulty id or simply called "live ID" */
function live_get_current_daily(): array
{
	global $DATABASE;
	global $UNIX_TIMESTAMP;
	
	$out = [];
	$group = $DATABASE->execute_query('SELECT MAX(daily_category), MIN(daily_category) FROM `daily_rotation`')[0];
	$max_group = $group[0];
	$min_group = $group[1];
	
	for($i = $min_group; $i <= $max_group; $i++)
	{
		$current_days = intdiv($UNIX_TIMESTAMP, 86400);
		$current_rot = $DATABASE->execute_query("SELECT live_id FROM `daily_rotation` WHERE daily_category = $i");
		$out[] = $current_rot[$current_days % count($current_rot)][0];
	}
	
	return $out;
}

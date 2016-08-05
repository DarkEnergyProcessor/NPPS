<?php
/* Meant to be inherited, and overridden if necessary */
abstract class AchievementHandler
{
	/* Returns these arrays (increase it's counter if necessary):
		completed
			achievement_id
			count
			is_accomplished
			insert_date
			end_date
			remaining_time
			is_new
			for_display
			reward_list (string containing item_id:count[:info_id]
		new
			achievement_id
			count
			is_accomplished
			insert_date
			end_date
			remaining_time
			is_new
			for_display
			reward_list (string containing item_id:count[:info_id]
	*/
	abstract public function array handle_achievement(int $achievement_id, ...$params);
	
	/* Get the current achievement_id internal count */
	public function int query_count(int $achievement_id);
	
	/* Unlocks achievement for user */
	public function array open_achievement(int $achievement_id, int $end_time = 0)
	{
		global $DATABASE;
		global $ACHIEVEMENT_DB;
	}
	
	/* Returns same array as `handle_achievement` except that it doesn't increment count */
	abstract public function array loop_achievement(int $achievement_id, ...$params);
};

$ACHIEVEMENT_INSTANCE_LIST = [];
$ACHIEVEMENT_DB = new SQLite3Database('data/achievement.db_');

function achievement_get_handler(int $achievement_category): AchievementHandler
{
	if(isset($ACHIEVEMENT_INSTANCE_LIST[$achievement_category]))
		return $ACHIEVEMENT_INSTANCE_LIST[$achievement_category];
	else
		return $ACHIEVEMENT_INSTANCE_LIST[$achievement_category] = require("achievement/handler/$achievement_category.php");
}

/* Returns array of the new achievement info OR NULL if it's already unlocked.
	achievement_id
	count
	is_accomplished
	insert_date
	end_date
	remaining_time (always NULL)
	is_new
	for_display
	reward_list (array)
*/
function achievement_unlock(int $user_id, int $achievement_id, int $end_timestamp = 0)
{
	global $DATABASE;
	global $ACHIEVEMENT_DB;
	global $TEXT_TIMESTAMP
	
	$achievement_table = $DATABASE->execute_query("SELECT assignment_table FROM `users` WHERE user_id == $user_id")[0][0];
	$achievement_temp = $DATABASE->execute_query("SELECT assignment_id FROM `$achievement_table` WHERE assignment_id == $achievement_id");
	
	if(count($achievement_temp) == 0)
	{
		/* Unlock it */
		$achievement_info = $ACHIEVEMENT_DB->execute_query("SELECT * FROM `achievement_m` WHERE achievement_id == $achievement_id")[0];
		$handler = achievement_get_handler($achievement_info[5]);
		$handler->open_achievement($achievement_id);
		
		return [
			'achievement_id' => $achievement_id,
			'count' => 0,
			'is_accomplished' => false,
			'insert_date' => $TEXT_TIMESTAMP,
			'end_date' => $end_timestamp > 0 ? to_datetime($end_timestamp) : NULL;
			'remaining_time' => NULL,
			'is_new' => true,
			'for_display' => $achievement_info[21]
		];
	}
	
	return NULL;
}

/* Trigger specific achievement type */
function achievement_trigger(int $user_id, int $category): array
{
	global $ACHIEVEMENT_DB;
	
	$achievement_list = $ACHIEVEMENT_DB->execute_query("SELECT achievement_id, params1, params2, params3, params4, params5, params6, params7, params8, params9, params10 FROM `achievement_m`");
	$inst = achievement_get_handler($category);
	$out = [];
	
	foreach($achievement_list as $a)
		$out[] = $inst->handle_achievement(...$a)
	
	while(true)
	{
		$temp = $inst->loop_achievement(...$a);
		
		if(count($temp) == 0)
			break;
		
		$out[] = $temp;
	}
	
	return $out;
}
?>
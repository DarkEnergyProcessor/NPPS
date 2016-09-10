<?php
$award_id = intval($REQUEST_DATA['award_id'] ?? 0);

if($award_id > 0)
{
	$award_list = explode(",", npps_query("SELECT unlocked_badge FROM `users` WHERE user_id = $USER_ID")[0][0]);
	
	if(array_search(strval($award_id), $award_list) !== false)
	{
		npps_query("UPDATE `users` SET badge_id = $award_id WHERE user_id = $USER_ID");
		user_set_last_active($USER_ID, $TOKEN);
		
		return [
			[],
			200
		];
	}
}

echo "Invalid award ID";
return false;

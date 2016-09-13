<?php
$back_id = intval($REQUEST_DATA['background_id'] ?? 0);

if($back_id > 0)
{
	$back_list = explode(",", npps_query("SELECT unlocked_background FROM `users` WHERE user_id = $USER_ID")[0][0]);
	
	if(array_search(strval($back_id), $back_list) !== false)
	{
		npps_query("UPDATE `users` SET background_id = $back_id WHERE user_id = $USER_ID");
		user_set_last_active($USER_ID, $TOKEN);
		
		return [
			[],
			200
		];
	}
}

echo "Invalid background ID";
return false;

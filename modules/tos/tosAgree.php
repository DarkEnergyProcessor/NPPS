<?php
if(isset($REQUEST_DATA['tos_id']) && is_int($REQUEST_DATA['tos_id']))
{
	if($DATABASE->execute_query("UPDATE `users` SET tos_agree = {$REQUEST_DATA['tos_id']} WHERE user_id = $USER_ID"))
	{
		user_set_last_active($USER_ID, $TOKEN);
		
		return [
			[],
			200
		];
	}
}

http_response_code(500);
return false;
?>
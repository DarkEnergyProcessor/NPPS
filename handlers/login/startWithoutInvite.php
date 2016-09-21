<?php
$new_invite_code = NULL;

if(!isset($REQUEST_DATA["login_key"]) || !isset($REQUEST_DATA["login_passwd"]))
{
	echo 'Missing "login_key" or "login_passwd"';
	return false;
}

if(token_exist($TOKEN) == false)
{
	echo 'Invalid token';
	return false;
}

if(isset($INVITE_CODE))
	$new_invite_code = $INVITE_CODE;


$user_id = $DATABASE->execute_query('SELECT user_id FROM `users` WHERE login_key = ? AND login_pwd = ?', 'ss', $REQUEST_DATA["login_key"], $REQUEST_DATA["login_passwd"]);

if($user_id && isset($user_id[0])) $user_id = $user_id[0][0];
else
{
	echo 'Serious internal error!!!';
	http_response_code(500);
	return false;
}

if($user_id)
{
	if(user_configure($user_id, $new_invite_code))
	{
		$DATABASE->execute_query('DELETE FROM `logged_in` WHERE token = ?', 's', $TOKEN);
		
		return [
			[],
			200
		];
	}
	
	echo 'Failed to configure!';
	http_response_code(500);
	return false;
}

echo 'Invalid data';
return false;
?>
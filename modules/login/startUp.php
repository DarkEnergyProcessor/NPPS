<?php
if(!isset($REQUEST_DATA["login_key"]) || !isset($REQUEST_DATA["login_passwd"]))
{
	echo 'Missing "login_key" or "login_passwd"';
	return false;
}

$user_id = user_create($REQUEST_DATA["login_key"], $REQUEST_DATA["login_passwd"]);

if($user_id == 0)
{
	echo 'Error while creating user';
	http_response_code(500);
	return false;
}

return [
	[
		"login_key" => $REQUEST_DATA["login_key"],
		"login_passwd" => $REQUEST_DATA["login_passwd"],
		"user_id" => $user_id
	],
	200
];
?>
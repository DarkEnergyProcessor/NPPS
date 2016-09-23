<?php
/* Check if there's login_key and login_passwd */
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

/* Ok, let's find links */
$connected_user_id = user_id_from_credentials($REQUEST_DATA["login_key"], $REQUEST_DATA["login_passwd"], $TOKEN);

if($connected_user_id == 0)
	/* No user ID found or login incorrect */
	return ERROR_CODE_LOGIN_INVALID;
else if($connected_user_id < 0)
{
	/* Account is banned! */
	http_response_code(423);
	header("HTTP/1.1 423 Locked");
	return false;
}

npps_begin_transaction();

/* Create new token again */
$newtoken = token_generate();
$USER_ID = $connected_user_id;

/* Delete previous token */
npps_query('DELETE FROM `logged_in` WHERE login_key = ? AND login_pwd = ?', 'ss', $REQUEST_DATA['login_key'], $REQUEST_DATA['login_passwd']);

/* Delete all WIP lives */
npps_query("DELETE FROM `wip_live` WHERE user_id = $connected_user_id");

/* Delete all WIP scenario */
npps_query("DELETE FROM `wip_scenario` WHERE user_id = $connected_user_id");

/* Update */
npps_query('UPDATE `logged_in` SET login_key = ?, login_pwd = ?, token = ? WHERE token = ?', 'ssss', 
	$REQUEST_DATA['login_key'], $REQUEST_DATA['login_passwd'], $newtoken, $TOKEN);

$TOKEN = $newtoken;

npps_end_transaction();

/* Out. The JSON string will be in-order atleast in PHP7 */
return [
	[
		'authorize_token' => $newtoken,
		'user_id' => $connected_user_id,
		'review_version' => '',
		'server_timestamp' => $UNIX_TIMESTAMP
	],
	200
];

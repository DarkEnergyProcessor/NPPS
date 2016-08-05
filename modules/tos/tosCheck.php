<?php
define('TOS_ID', 1);

$tos_check = $DATABASE->execute_query('SELECT tos_agree FROM `users` WHERE user_id = ?', 'i', $USER_ID);

if($tos_check == false)
{
	echo 'Internal server error!';
	http_response_code(500);
	return false;
}

user_set_last_active($USER_ID, $TOKEN);

return [
	[
		"tos_id" => TOS_ID,
		"is_agreed" => $tos_check[0][0] >= TOS_ID
	],
	200,
];
?>
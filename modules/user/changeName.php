<?php
if(!isset($REQUEST_DATA['name']))
{
	echo 'Name needed!';
	return false;
}

if(mb_strlen($REQUEST_DATA['name']) > 10 || strpos_array(str_replace(' ', '', strtolower($REQUEST_DATA['name'])), BADWORDS_LIST) !== false)
	/* Name too long or contain badwords */
	return [
		[
			"error_code" => 1105
		],
		600
	];

$old_name = $DATABASE->execute_query('SELECT name FROM `users` WHERE user_id = ?', 'i', $USER_ID)[0][0];

if($DATABASE->execute_query('UPDATE `users` SET name = ? WHERE user_id = ?', 'si', $REQUEST_DATA['name'], $USER_ID))
{
	user_set_last_active($USER_ID, $TOKEN);
	
	return [
		[
			'before_name' => $old_name,
			'after_name' => $REQUEST_DATA['name']
		],
		200
	];
}

http_response_code(500);
echo 'Internal server error!';
return false;
?>
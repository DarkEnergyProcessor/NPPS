<?php
$generate_passcode = function()
{
	$x = strtolower(base64_encode(random_bytes(12)));
	$x = preg_replace('/\+/', '1', $x);
	$x = preg_replace('!/!', '0', $x);
	
	return $x;
};

$generate_passcode_full = function() use($generate_passcode, $DATABASE, $USER_ID, $PLATFORM_ID, $UNIX_TIMESTAMP): array
{
	$new_passcode = $generate_passcode();
	
	while(count($DATABASE->execute_query('SELECT passcode FROM `users` WHERE passcode LIKE ?', 's', "$new_passcode:%")) > 0)
		$new_passcode = $generate_passcode();
	
	$DATABASE->execute_query('UPDATE `users` SET passcode = ?, passcode_issue = ? WHERE user_id = ?', 'sii', $new_passcode.':'.$PLATFORM_ID, $UNIX_TIMESTAMP, $USER_ID);
	
	return [
		$new_passcode,
		200
	];
};

if(defined('PASSCODE_REGENERATE'))
	return $generate_passcode_full();

$handover_info = $DATABASE->execute_query("SELECT passcode, passcode_issue FROM `users` WHERE user_id = $USER_ID")[0];

if($handover_info[0] == NULL || $UNIX_TIMESTAMP > intval($handover_info[1] ?? 0) + 31557600)
	return $generate_passcode_full();

$passcode_data = explode(':', $handover_info[0]);

if($passcode_data[1] != $PLATFORM_ID)
	return $generate_passcode_full();

return [
	$passcode_data[0],
	200
];

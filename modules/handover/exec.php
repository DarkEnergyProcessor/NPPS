<?php
$handover = $REQUEST_DATA['handover'] ?? '';

if(!is_string($handover))
{
	echo 'Invalid \'handover\'';
	return false;
}

$handover = substr(preg_replace('/[^A-Za-z0-9]/', '', $handover), 0, 16);
$passcode_search_results = $DATABASE->execute_query('SELECT user_id, passcode, passcode_issue, free_loveca + paid_loveca FROM `users` WHERE passcode LIKE ?', 's', "$handover:%");

if(count($passcode_search_results) == 0)
	return [
		[
			'error_code' => ERROR_HANDOVER_NONE
		],
		600
	];

$user_target = $passcode_search_results[0];

if($user_target[2] + 31557600 >= $UNIX_TIMESTAMP)
{
	// Not expired.
	
	if($user_target[0] == $USER_ID)
		// Self transfer
		return [
			[
				'error_code' => ERROR_HANDOVER_SELF
			],
			600
		];
	
	// Check if it has loveca
	if($user_target[3] > 0)
	{
		$temp = explode(':', $user_target[1]);
		
		if($temp[1] != $PLATFORM_ID)
			// Has loveca and different device
			return [
				[
					'error_code' => ERROR_HANDOVER_SELF
				],
				600
			];
	}
	
	// Set login key and password
	$DATABASE->execute_query('BEGIN');
	$DATABASE->execute_query("UPDATE `users` SET login_key = (SELECT login_key from `users` WHERE user_id = $USER_ID), login_pwd = (SELECT login_pwd from `users` WHERE user_id = $USER_ID), passcode = NULL, passcode_issue = NULL, platform_code = NULL WHERE user_id = {$user_target[0]}");
	$DATABASE->execute_query("UPDATE `users` SET login_key = NULL, login_pwd = NULL WHERE user_id = $USER_ID");
	
	token_destroy($TOKEN);
	$DATABASE->execute_query('COMMIT');
	
	return [true, 200];
}
else
	// Expired
	return [
		[
			'error_code' => ERROR_HANDOVER_EXPIRE
		],
		600
	];

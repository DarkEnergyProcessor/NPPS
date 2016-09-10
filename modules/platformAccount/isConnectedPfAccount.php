<?php
$platform_token = strval($REQUEST_DATA['platform_token'] ?? '');

// search
$with_platform_result = npps_query('SELECT name, level FROM `users` WHERE platform_code LIKE ?', 's', "$platform_token:%");

$target_user_name = NULL;
$target_user_level = NULL;

if(count($with_platform_result) > 0)
{
	$user_target = $with_platform_result[0];
	
	$target_user_name = $user_target['name'];
	$target_user_level = $user_target['level'];
}

return [
	[
		'is_connected' => $target_user_name !== $target_user_level,
		'user_level' => $target_user_level,
		'user_name' => $target_user_name
	],
	200
];

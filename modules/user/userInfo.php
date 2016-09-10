<?php
$userinfo = user_current_info($USER_ID);

user_set_last_active($USER_ID, $TOKEN);

return [
	[
		'user' => $userinfo
	],
	200
];

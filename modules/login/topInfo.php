<?php
$present_count = $DATABASE->execute_query('SELECT COUNT(item_pos) FROM `'.$DATABASE->execute_query("SELECT present_table FROM `users` WHERE user_id = $USER_ID")[0][0].'` WHERE `collected` IS NULL')[0][0];
$pm_count = $DATABASE->execute_query("SELECT COUNT(notice_id) FROM `notice_list` WHERE receiver_user_id = $USER_ID AND is_pm = 1")[0][0];
$activity_count = $DATABASE->execute_query("SELECT COUNT(notice_id) FROM `notice_list` WHERE receiver_user_id = $USER_ID AND is_pm = 0")[0][0];
$is_free_gacha = true;
$next_free_gacha = user_get_free_gacha_timestamp($USER_ID);

return [
	[
		'friend_action_cnt' => $activity_count + $pm_count,
		'friend_greet_cnt' => $pm_count,
		'friend_variety_cnt' => $activity_count,
		'present_cnt' => $present_count,
		'free_gacha_flag' => $is_free_gacha,
		'server_datetime' => $TEXT_TIMESTAMP,
		'server_timestamp' => $UNIX_TIMESTAMP,
		'next_free_gacha_timestamp' => $next_free_gacha,
		'notice_friend_datetime' => $TEXT_TIMESTAMP,
		'notice_mail_datetime' => $TEXT_TIMESTAMP
	],
	200
];
?>
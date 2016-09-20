<?php
$notice_title = '';
$notice_contents = '';
$notice = npps_query("SELECT title, contents FROM `user_notice` WHERE user_id = $USER_ID");
$has_notice = count($notice) > 0;

if($has_notice)
{
	$notice = $notice[0];
	$notice_title = $notice['title'];
	$notice_contents = $notice['contents'];
}

return [
	[
		'has_notice' => $has_notice,
		'notice_id' => $has_notice ? (-1) : 0,
		'type' => 0,
		'title' => $notice_title,
		'contents' => $notice_contents
	],
	200,
];

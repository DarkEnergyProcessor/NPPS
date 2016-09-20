<?php
npps_query("DELETE FROM `user_notice` WHERE user_id = $USER_ID");

return [
	[],
	200
];

<?php
$DATABASE->execute_query("DELETE FROM `wip_live` WHERE user_id = $USER_ID");

return [
	[],
	200
];
?>
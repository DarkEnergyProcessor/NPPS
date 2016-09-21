<?php
/* Generate token and insert it */
$authorize_token = token_generate();
$TOKEN = $authorize_token;

npps_query('INSERT INTO `logged_in` VALUES (NULL, NULL, ?, ?, 0)', 'si', $authorize_token, $UNIX_TIMESTAMP);

return [
	['authorize_token' => $authorize_token],
	200
];

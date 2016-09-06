<?php
/*
 * Null Pointer Private Server
 * Token-related functions, for login.
 */

/* Creates token */
function token_generate(): string
{
	token_invalidate();
	
	return hash("sha256", strval(rand(0, 32767)).strval(time()));
}

/* Check if token is there */
function token_exist($token): bool
{
	global $DATABASE;
	
	token_invalidate();
	
	if($token == NULL) return false;
	
	foreach($DATABASE->execute_query("SELECT token FROM `logged_in`") as $value)
		if(strcmp($value[0], $token) == 0)
			return true;
	
	return false;
}

/* Kick players not logged in for more than 3 days */
function token_invalidate()
{
	global $DATABASE;
	
	$unix_ts = time();
	
	foreach($DATABASE->execute_query('SELECT * FROM `logged_in`') as $value)
	{
		if(($unix_ts - $value[3]) > 259200)
			$DATABASE->execute_query("DELETE FROM `logged_in` WHERE time = ?", "i", $value[3]);
	}
}

/* Forcefully destroy the token */
function token_destroy(string $token)
{
	global $DATABASE;
	
	$DATABASE->execute_query('DELETE FROM `logged_in` WHERE token = ?', 's', $token);
}

function token_use_pseudo_unit_own_id(string $token): int
{
	global $DATABASE;
	
	$pseudo_curnum = $DATABASE->execute_query('SELECT pseudo_unit_own_id FROM `logged_in` WHERE token = ?', 's', $token)[0][0];
	$DATABASE->execute_query('UPDATE `logged_in` SET pseudo_unit_own_id = pseudo_unit_own_id - 1 WHERE token = ?', 's', $token);
	return $pseudo_curnum;
}

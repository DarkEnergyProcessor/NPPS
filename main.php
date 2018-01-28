<?php
/*
 * Null-Pointer Private Server
 * Do most preparation before handling SIF requests
 * Copyright © 2039 Dark Energy Processor Corporation
 */

/** @@ Configuration @@ **/
// Debug environment? Comment for production environment
define("DEBUG_ENVIRONMENT", true);

// Consumer key for this server
define("CONSUMER_KEY", "lovelive_test");

// Region ID for this server
define("REGION", "392");

// Application ID for SIF
define('APPLICATION_ID', '834030294');

// Expected client version before issuing "Starting Download". Comment to disable
// If the major version (the 7 or the 3) is modified, it will issue version update instead.
// Wildcard is allowed (and Server-Version default to *.*.0 if major-version is lower or higher
define('EXPECTED_CLIENT', "7.3.*");

// Enable request logging to database. Comment to disable
//define("REQUEST_LOGGING", true);

// Enable X-Message-Code checking. Comment to disable
//define("XMESSAGECODE_CHECK", true);

// Set default timezone for this server. Comment to rely on php.ini's timezone setting
//define('DEFAULT_TIMEZONE', 'UTC');

/* Game Related */ /* ************************** */

// Unlock all event stories? Comment to disable
define('UNLOCK_ALL_EVENTSCENARIO', true);

// List of badwords. Add if necessary
define('BADWORDS_LIST', [
]);

// Regenerate passcode or use existing when issuing passcode. Comment to "use existing passcode" behaviour.
define('PASSCODE_REGENERATE', true);

/** !! Configuration !! **/ /* End configuration */

/** @@ SIF Error Codes @@ **/
define('ERROR_CODE_NO_ERROR',									0);
define('ERROR_CODE_LIB_ERROR ',									1);
define('ERROR_CODE_NETWORK_ERROR',								501);
define('ERROR_CODE_LOGIN_FAILED',								502);
define('ERROR_CODE_UNAVAILABLE',								503);
define('ERROR_CODE_TIMEOUT',									504);
define('ERROR_CODE_LOCKED',										423);
define('ERROR_CODE_GAME_LOGIC_ERROR',							1001);
define('ERROR_CODE_DUPLICATE_USER_NAME',						1100);
define('ERROR_CODE_UNAVAILABLE_WORDS',							1101);
define('ERROR_CODE_ENERGY_FULL',								1102);
define('ERROR_CODE_NOT_ENOUGH_LOVECA',							1103);
define('ERROR_CODE_NOT_ENOUGH_GAME_COIN',						1104);
define('ERROR_CODE_NG_WORDS',									1105);
define('ERROR_CODE_ONLY_WHITESPACE_CHARACTERS',					1106);
define('ERROR_CODE_OVER_UNIT_MAX',								1108);
define('ERROR_CODE_OVER_FRIEND_MAX',							1109);
define('ERROR_CODE_CHARACTER_COUNT_IS_OVER',					1112);
define('ERROR_CODE_INCENTIVE_NONE',								1201);
define('ERROR_CODE_OPEN_OTHERS',								1202);
define('ERROR_CODE_LIMIT_OVER',									1203);
define('ERROR_CODE_DUPLICATE_UNIT',								1301);
define('ERROR_CODE_NO_CENTER_UNIT',								1302);
define('ERROR_CODE_POSITION_EMPTY',								1303);
define('ERROR_CODE_IGNORE_MAIN_DECK',							1304);
define('ERROR_CODE_IGNORE_MAIN_DECK_UNIT',						1305);
define('ERROR_CODE_NO_GAME_COIN',								1306);
define('ERROR_CODE_UNIT_NOT_EXIST',								1311);
define('ERROR_CODE_UNIT_NOT_DISPOSE',							1312);
define('ERROR_CODE_UNIT_LEVEL_AND_SKILL_LEVEL_MAX',				1313);
define('ERROR_CODE_IGNORE_FAVORITE_FLAG',						1321);
define('ERROR_CODE_BATTLE_NOT_EXIST',							1400);
define('ERROR_CODE_BATTLE_NOT_ENOUGH_MAX_ENERGY',				1401);
define('ERROR_CODE_BATTLE_NOT_ENOUGH_CURRENT_ENERGY',			1402);
define('ERROR_CODE_BATTLE_NOT_ENOUGH_LOVECA',					1403);
define('ERROR_CODE_BATTLE_INVALID_PARTY_USER',					1404);
define('ERROR_CODE_BATTLE_INVALID_UNIT_DECK',					1405);
define('ERROR_CODE_BATTLE_UNIT_LIMIT_OVER',						1406);
define('ERROR_CODE_BATTLE_NOTES_LEVEL_RATE_NOT_FOUND',			1407);
define('ERROR_CODE_BATTLE_NOTES_EFFECT_NOT_FOUND',				1408);
define('ERROR_CODE_BATTLE_UNIT_REWARD_NOT_FOUND',				1409);
define('ERROR_CODE_BATTLE_PARTY_MEMBER_INFO_NOT_FOUND',			1410);
define('ERROR_CODE_BATTLE_PLAY_DATA_NOT_FOUND',					1411);
define('ERROR_CODE_BATTLE_NOT_ENOUGH_EVENT_POINT',				1412);
define('ERROR_CODE_BATTLE_NOT_MATCH_BATTLE_RANK',				1413);
define('ERROR_CODE_BATTLE_UNKNOWN_ERROR',						1414);
define('ERROR_CODE_BATTLE_ROOM_IS_INVALID',						1415);
define('ERROR_CODE_SECRET_BOX_NOT_EXIST',						1500);
define('ERROR_CODE_SECRET_BOX_COST_TYPE_IS_NOT_SPECIFIED',		1501);
define('ERROR_CODE_SECRET_BOX_INVALID_COST_TYPE',				1502);
define('ERROR_CODE_SECRET_BOX_NOT_MULTI',						1503);
define('ERROR_CODE_SECRET_BOX_AUTHORITY_ERROR',					1504);
define('ERROR_CODE_SECRET_BOX_GAUGE_INFORMATION_NOT_EXIST',		1505);
define('ERROR_CODE_SECRET_BOX_GAUGE_IS_FULL',					1506);
define('ERROR_CODE_SECRET_BOX_REMAINING_COST_IS_NOT_ENOUGH',	1507);
define('ERROR_CODE_SECRET_BOX_OUT_OF_DATE',						1508);
define('ERROR_CODE_SECRET_BOX_UPPER_LIMIT',						1509);
define('ERROR_CODE_USER_NOT_EXIST',								1601);
define('ERROR_CODE_OUT_OF_RANG',								1602);
define('INITIAL_DECK_NOT_EXISTS',								1701);
define('INITIAL_MAIN_DECK_ALREADY_EXIST',						1702);
define('ERROR_ALLIANCE_DUMMY',									1900);
define('ERROR_ALLIANCE_DUMMY2',									1901);
define('ERROR_ALLIANCE_DUMMY3',									1902);
define('ERROR_CODE_SCENARIO_NOT_FOUND',							2300);
define('ERROR_CODE_SCENARIO_NOT_AVAILABLE',						2301);
define('ERROR_CODE_SCENARIO_LIVE_IS_PLAYING',					2302);
define('ERROR_CODE_SCENARIO_NO_REWARD',							2303);
define('ERROR_CODE_SCENARIO_IS_CLOSED',							2304);
define('ERROR_CODE_SCENARIO_HAS_GOAL',							2305);
define('ERROR_CODE_SUBSCENARIO_NOT_FOUND',						2350);
define('ERROR_CODE_SUBSCENARIO_NOT_AVAILABLE',					2351);
define('ERROR_CODE_SUBSCENARIO_LIVE_IS_PLAYING',				2352);
define('ERROR_CODE_SUBSCENARIO_NO_REWARD',						2353);
define('ERROR_CODE_SUBSCENARIO_IS_CLOSED',						2354);
define('ERROR_CODE_FRIEND_USER_NOT_EXISTS',						2400);
define('ERROR_CODE_FRIEND_SPECIFIED_USER_IS_NOT_A_FRIEND',		2401);
define('ERROR_CODE_FRIEND_SPECIFIED_USER_IS_ARLEADY_FRIEND',	2402);
define('ERROR_CODE_FRIEND_NOT_REQUESTED_FROM_SPECIFIED_USER',	2403);
define('ERROR_CODE_FRIEND_NOT_REQUESTING_TO_SPECIFIED_USER',	2404);
define('ERROR_CODE_FRIEND_COUNT_OVER_LIMIT',					2405);
define('ERROR_CODE_FRIEND_ALREADY_REQUESTING_TO_SPECIFIED_USER',2406);
define('ERROR_CODE_FRIEND_ALREADY_REJECTED_FROM_SPECIFIED_USER',2407);
define('ERROR_CODE_FRIEND_ALREADY_CANCELED_FROM_SPECIFIED_USER',2408);
define('ERROR_CODE_FRIEND_COUNT_OVER_LIMIT_OF_APPLICANT',		2409);
define('ERROR_CODE_FRIEND_OVER_REQUEST_COUNT',					2410);
define('ERROR_CODE_FRIEND_OVER_PENDING_APPROVAL_COUNT',			2411);
define('ERROR_CODE_GREET_INVALID_CHARACTOR',					2500);
define('ERROR_CODE_ALBUM_REWARD_NONE',							2700);
define('ERROR_CODE_ALBUM_REWARD_OPENED',						2701);
define('ERROR_CODE_TUTORIAL_MAKE_FIRST_DECK_ERROR',				3000);
define('ERROR_CODE_TUTORIAL_OPEN_FIRST_SCENARIO_ERROR',			3001);
define('ERROR_CODE_TUTORIAL_SET_USER_SETTING_ERROR',			3002);
define('ERROR_CODE_TUTORIAL_OPEN_FIRST_LIVE_ERROR',				3003);
define('ERROR_CODE_TUTORIAL_BAD_STATE_ERROR',					3004);
define('ERROR_CODE_TUTORIAL_IS_END',							3005);
define('ERROR_CODE_SNS_COIN_LIMIT_OVER',						3100);
define('ERROR_CODE_PAYMENT_INVALID_APPLE_PRODUCT_ID ',			3101);
define('ERROR_CODE_LIVE_NOT_FOUND',								3400);
define('ERROR_CODE_LIVE_NOT_ENOUGH_MAX_ENERGY',					3401);
define('ERROR_CODE_LIVE_NOT_ENOUGH_CURRENT_ENERGY',				3402);
define('ERROR_CODE_LIVE_NOT_ENOUGH_LOVECA',						3403);
define('ERROR_CODE_LIVE_INVALID_PARTY_USER',					3404);
define('ERROR_CODE_LIVE_INVALID_UNIT_DECK',						3405);
define('ERROR_CODE_LIVE_UNIT_LIMIT_OVER',						3406);
define('ERROR_CODE_LIVE_NOTES_LEVEL_RATE_NOT_FOUND',			3407);
define('ERROR_CODE_LIVE_NOTES_EFFECT_NOT_FOUND',				3408);
define('ERROR_CODE_LIVE_UNIT_REWARD_NOT_FOUND',					3409);
define('ERROR_CODE_LIVE_PARTY_MEMBER_INFO_NOT_FOUND',			3410);
define('ERROR_CODE_LIVE_PLAY_DATA_NOT_FOUND',					3411);
define('ERROR_CODE_LIVE_NOT_ENOUGH_EVENT_POINT',				3412);
define('ERROR_CODE_LIVE_NOT_MATCH_LIVE_RANK',					3413);
define('ERROR_CODE_LIVE_UNKNOWN_ERROR',							3414);
define('ERROR_CODE_LIVE_LEADER_SKILL_NOT_FOUND',				3415);
define('ERROR_CODE_LIVE_NOTES_LIST_NOT_FOUND',					3416);
define('ERROR_CODE_LIVE_NOT_OPENED',							3417);
define('ERROR_CODE_LIVE_EVENT_HAS_GONE',						3418);
define('ERROR_CODE_LIVE_UNEXPECTED_END',						3419);
define('ERROR_CODE_OVER_ADD_EXCHANGE_ITEM_COUNT_MAX_LIMIT',		4201);
define('ERROR_CODE_NOT_ENOUGH_EXCHANGE_POINT',					4202);
define('ERROR_CODE_EXCHANGE_ITEM_OUT_OF_DATE',					4203);
define('ERROR_CODE_EXCHANGE_INVALID',							4204);
define('ERROR_HANDOVER_EXPIRE',									4401);
define('ERROR_HANDOVER_NONE',									4402);
define('ERROR_HANDOVER_SELF',									4403);
define('ERROR_HANDOVER_DISABLE_MU',								4404);
define('ERROR_CODE_ONLINE_PLAY_COUNT_LIMIT_OVER',				4701);
define('ERROR_CODE_ONLINE_LIVE_HAS_GONE',						4702);
define('ERROR_DOWNLOAD_NO_ADDITIONAL_PACKAGE',					4501);
define('ERROR_DOWNLOAD_NO_UPDATE_PACKAGE',						4502);
define('ERROR_CODE_EVENT_REWARD_OPENED',						10000);
define('ERROR_CODE_EVENT_REWARD_NOT_GETTABLE',					10001);
define('ERROR_CODE_EVENT_NO_EVENT_POINT_COUNT_DATA',			10002);
define('ERROR_CODE_EVENT_NO_EVENT_POINT_USER_DATA',				10003);
define('ERROR_CODE_EVENT_NO_EVENT_DATA',						10004);
define('ERROR_CODE_LL_ACCOUNT_ALREADY_CONNECTED',				6000);
define('ERROR_CODE_PF_ACCOUNT_ALREADY_CONNECTED',				6001);
define('ERROR_CODE_CONNECTION_DOSE_NOT_EXISTS',					6002);
define('ERROR_CODE_INVALID_USERS_HANDOVER',						6003);
define('ERROR_CODE_INVALID_PLATFORM_HANDOVER',					6004);
/** !! SIF Error Codes !! **/

define('MAIN_INVOKED', true, true);
define('X_MESSAGE_CODE', 'liumangtuzi');

/* Hopefully nginx fix. Source: http://www.php.net/manual/en/function.getallheaders.php#84262 */
if(!function_exists('getallheaders'))
{
	function getallheaders(): array
	{
		$headers = '';
		foreach ($_SERVER as $name => $value)
		{
			if (substr($name, 0, 5) == 'HTTP_')
			{
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
	   }
	   return $headers;
	}
} 

$MAINTENANCE_MODE = false;
$REQUEST_HEADERS = array_change_key_case(getallheaders(), CASE_LOWER);
$REQUEST_SUCCESS = false;
$RESPONSE_ARRAY = [];
$DATABASE = NULL;
$UNIX_TIMESTAMP = time();
$TEXT_TIMESTAMP = date('Y-m-d H:i:s', $UNIX_TIMESTAMP);

set_error_handler(function($errNo, $errStr, $errFile, $errLine)
{
	http_response_code(500);
	throw new ErrorException("$errStr in $errFile on line $errLine", $errNo);
});

set_exception_handler(function($x)
{
	http_response_code(500);
	throw $x;
});

$HANDLER_SHUTDOWN = function()
{
	global $MAINTENANCE_MODE;
	global $REQUEST_HEADERS;
	global $REQUEST_SUCCESS;
	global $RESPONSE_ARRAY;
	
	if($MAINTENANCE_MODE) exit;	// Don't do anything on maintenance
	
	header('Content-Type: application/json; charset=utf-8');
	header(sprintf("Date: %s", gmdate('D, d M Y H:i:s T')));
	
	$contents = ob_get_contents();
	error_log($contents, 0);
	
	if(!defined('DEBUG_ENVIRONMENT'))
		$contents = "";
	
	if($REQUEST_SUCCESS == false)
	{
		$output = [
			"code" => 10000,
			"message" => $contents
		];
		
		if(http_response_code() == 200)
			http_response_code(error_get_last() == NULL ? 403 : 500);	// If it's not previously set
		
		ob_end_clean();
		exit(json_encode($output));
	}
		
	header("status_code: {$RESPONSE_ARRAY["status_code"]}");
	
	if(strlen($contents) > 0)
		$RESPONSE_ARRAY['message'] = $contents;
	
	ob_end_clean();
	ob_start('ob_gzhandler');
	
	$output = json_encode($RESPONSE_ARRAY);
	if(strlen($output) > 2)
	{
		header(sprintf("X-Message-Code: %s", hash_hmac('sha1', $output, X_MESSAGE_CODE)));
		header(sprintf("X-Message-Sign: %s", base64_encode(str_repeat("\x00", 128))));
		
		echo $output;
	}
	
	ob_end_flush();
	
	exit;
};

$MAIN_SCRIPT_HANDLER = function(string $BUNDLE, int& $USER_ID, $TOKEN, string $OS, int $PLATFORM_ID, string $OS_VERSION, string $TIMEZONE, string $module, $action = NULL): bool
{
	global $REQUEST_HEADERS;
	global $RESPONSE_ARRAY;
	global $DATABASE;
	global $UNIX_TIMESTAMP;
	global $TEXT_TIMESTAMP;
	
	$request_data = [];
	
	if(isset($_POST['request_data']))
	{
		if(defined("XMESSAGECODE_CHECK"))
		{
			
			if(!isset($REQUEST_HEADERS["x-message-code"]))
			{
				echo "X-Message-Code header required!";
				http_response_code(400);
				return false;
			}
			
			if(strcmp($REQUEST_HEADERS["x-message-code"], hash_hmac("sha1", $_POST['request_data'], X_MESSAGE_CODE)))
			{
				echo "Invalid X-Message-Code";
				http_response_code(400);
				return false;
			}
		}
		
		$request_data = json_decode(mb_convert_encoding($_POST['request_data'], 'UTF-8', 'UTF-8'), true);
		
		if($request_data == NULL)
		{
			echo "Invalid JSON data: {$_POST['request_data']}";
			return false;
		}
	}
	
	if(defined('REQUEST_LOGGING'))
	{
		// TODO
	}
	
	require_once('modules/include.php');
	
	/* Handler first. A handler doesn't need to have valid token */
	if(is_string($action) && strcmp($request_data['module'] ?? '', $module) && strcmp($request_data['action'] ?? '', $action))
	{
		$modname = "handlers/$module/$action.php";
		
		if(is_file($modname))
		{
			$REQUEST_DATA = $request_data;
			$val = NULL; {$val=include($modname);}
			
			if($val === false)
				return false;
			
			if(is_int($val))
			{
				$RESPONSE_ARRAY['response_data'] = ['error_code' => $val];
				$RESPONSE_ARRAY['status_code'] = 600;
			}
			else
			{
				$RESPONSE_ARRAY['response_data'] = $val[0];
				$RESPONSE_ARRAY['status_code'] = $val[1];
			}
			
			return true;
		}
	}
	
	/* Verify credentials existence */
	if($TOKEN === NULL || token_exist($TOKEN) == false || $USER_ID == 0)
	{
		invalid_credentials:
		echo 'Invalid login, password, user_id, and/or token!';
		return false;
	}
	else
	{
		$cred = npps_query('SELECT login_key, login_pwd FROM `logged_in` WHERE token = ?', 's', $TOKEN)[0];
		if(count(npps_query("SELECT user_id FROM `users` WHERE login_key = ? AND login_pwd = ? AND user_id = $USER_ID", 'ss', $cred[0], $cred[1])) != 1)
			goto invalid_credentials;
	}
	
	/* ok now modules */
	if(strcmp($module, 'api') == 0)
	{
		/* Multiple module/action calls */
		$RESPONSE_ARRAY["response_data"] = [];
		$RESPONSE_ARRAY["status_code"] = 200;
		
		/* Call all handler in order */
		foreach($request_data as $rd)
		{
			$modname = "modules/{$rd["module"]}/{$rd["action"]}.php";
			
			if(is_file($modname))
			{
				$REQUEST_DATA = $rd;
				$val = NULL; {$val=include($modname);}
				
				if($val === false)
					return false;
				
				if(is_integer($val))
					$RESPONSE_ARRAY["response_data"][] = [
						"result" => ['error_code' => $val],
						"status" => 600,
						"commandNum" => false,
						"timeStamp" => $UNIX_TIMESTAMP
					];
				else
					$RESPONSE_ARRAY["response_data"][] = [
						"result" => $val[0],
						"status" => $val[1],
						"commandNum" => false,
						"timeStamp" => $UNIX_TIMESTAMP
					];
			}
			else
			{
				echo "One of the module not found: {$rd['module']}/{$rd['action']}";
				return false;
			}
		}
		
		return true;
	}
	else if($action !== NULL && isset($request_data['module']) && isset($request_data['action']) &&
			strcmp($request_data['module'], $module) == 0 && strcmp($request_data['action'], $action) == 0)
	{
		/* Single module call in form /main.php/module/action */
		$modname = "modules/$module/$action.php";
			
		if(is_file($modname))
		{
			$REQUEST_DATA = $request_data;
			$val = NULL; {$val=include($modname);}
			
			if($val === false)
				return false;
			
			if(is_integer($val))
			{
				$RESPONSE_ARRAY["response_data"] = ['error_code' => $val];
				$RESPONSE_ARRAY["status_code"] = 600;
			}
			else
			{
				$RESPONSE_ARRAY["response_data"] = $val[0];
				$RESPONSE_ARRAY["status_code"] = $val[1];
			}
			
			return true;
		}
		
		echo "Module not found! $module/$action", PHP_EOL;
		return false;
	}
	else
	{
		echo 'Invalid module/action';
		return false;
	}
};

/* Returns string if array is supplied; Returns array if string is supplied */
/* Returns false if the authorize parameter is invalid */
function authorize_function($authorize)
{
	if(is_array($authorize))
	{
		/* Assemble authorize string */
		return http_build_query($authorize);
	}
	elseif(is_string($authorize))
	{
		/* Disassemble authorize string */
		parse_str($authorize, $new_assemble);
		
		/* Check the authorize string */
		/*
		if(
			(isset($new_assemble["consumerKey"]) && strcmp($new_assemble["consumerKey"], CONSUMER_KEY) == 0) &&
			(isset($new_assemble["version"]) && strcmp($new_assemble["version"], "1.1") == 0) &&
			isset($new_assemble["nonce"]) &&
			isset($new_assemble["timeStamp"])
		)
		*/
		if(
			(isset($new_assemble["consumerKey"]) && strcmp($new_assemble["consumerKey"], CONSUMER_KEY) == 0)
		)
			return $new_assemble;
		
		return false;
	}
}

/* Returns value or null if variable is not set */
function retval_null(&$var)
{
	return isset($var) ? $var : NULL;
}

if(!defined("WEBVIEW"))
{
	function main()
	{
		global $MAINTENANCE_MODE;
		global $REQUEST_HEADERS;
		global $REQUEST_SUCCESS;
		global $RESPONSE_ARRAY;
		global $DATABASE;
		global $MAIN_SCRIPT_HANDLER;
		
		// Will be modified later by the server_api handler
		$USER_ID = 0;
		$TOKEN = NULL;
		$AUTHORIZE_DATA = NULL;
		
		$MODULE_TARGET = NULL;
		$ACTION_TARGET = NULL;
		
		/* Set timezone */
		if(defined('DEFAULT_TIMEZONE'))
			date_default_timezone_set(DEFAULT_TIMEZONE);
		
		/* Check if it's maintenance */
		if(file_exists("Maintenance") || file_exists("Maintenance.txt") ||
		   file_exists("maintenance") || file_exists("maintenance.txt")
		)
		{
			header("Maintenance: 1");
			$MAINTENANCE_MODE = true;
			exit;
		}
		
		/* Check the authorize */
		if(isset($REQUEST_HEADERS["authorize"]))
			$AUTHORIZE_DATA = authorize_function($REQUEST_HEADERS["authorize"]);
		if(!$AUTHORIZE_DATA)
		{
			echo "Authorize header needed!";
			exit;
		}
		$TOKEN = retval_null($AUTHORIZE_DATA["token"]);
		
		/* Check the bundle version */
		/*
		if(!isset($REQUEST_HEADERS["bundle-version"]))
		{
			echo "Bundle-Version header needed!";
			exit;
		}
		*/
		
		/* Check if client-version is OK */
		if(isset($REQUEST_HEADERS["client-version"]))
		{
			if(defined("EXPECTED_CLIENT"))
			{
				//header("Server-Version: ".EXPECTED_CLIENT);
				$ver1 = explode('.', EXPECTED_CLIENT);
				$ver2 = explode('.', $REQUEST_HEADERS["client-version"]);
				$trigger_version_up = NULL;
				
				for($i = 0; $i < 3; $i++)
				{
					if(strcmp($ver1[$i], '*') != 0 && $ver1[$i] != $ver2[$i])
					{
						$trigger_version_up = str_replace('*', '0', EXPECTED_CLIENT);
						break;
					}
				}
				
				$trigger_version_up = $trigger_version_up ?? $REQUEST_HEADERS["client-version"] ?? EXPECTED_CLIENT;
				header("Server-Version: $trigger_version_up");
			}
			else
				header("Server-Version: {$REQUEST_HEADERS["client-version"]}");
		}
		else
		{
			echo "Client-Version header needed!";
			exit;
		}
		
		/* get the module and the action. Use different scope */
		{
			preg_match('!main.php/(\w+)/?(\w*)!', $_SERVER["REQUEST_URI"], $x);
			
			if(isset($x[1]))
				$MODULE_TARGET = $x[1];
			else
			{
				echo "Module needed!";
				exit;
			}
			
			if(isset($x[2]) && strlen($x[2]) > 0)
				$ACTION_TARGET = $x[2];
		}
		
		if(isset($REQUEST_HEADERS['user-id']) || isset($AUTHORIZE_DATA['user_id']))
		{
			if(isset($REQUEST_HEADERS['user-id']))
				if(preg_match('/\d+/', $REQUEST_HEADERS['user-id']) == 1)
					$USER_ID = intval($REQUEST_HEADERS['user-id']);
				else
				{
					echo 'Invalid user ID';
					exit;
				}
		}
		
		
		/* Load database wrapper and initialize it */
		$DATABASE = require('database_wrapper.php');
		$DATABASE->initialize_environment();
		
		/* Call handler. Parameters: bundle-version, user_id, token, os, platform-id, os-version, time-zone = "unknown", module = "api", action = NULL */
		$REQUEST_SUCCESS = $MAIN_SCRIPT_HANDLER(
			'',
			$USER_ID,
			$TOKEN,
			$REQUEST_HEADERS["os"] ?? "unknown",
			$REQUEST_HEADERS["platform-type"] ?? -1,
			$REQUEST_HEADERS["os-version"] ?? "unknown",
			$REQUEST_HEADERS["time-zone"] ?? "unknown",
			$MODULE_TARGET ?? "api",
			$ACTION_TARGET
		);
		
		/* Check if user id changed */
		if($USER_ID > 0)
			header("user_id: $USER_ID");
		
		/* Reassemble authorize function */
		{
			$new_authorize = [];
			
			foreach($AUTHORIZE_DATA as $k => $v)
				$new_authorize[$k] = $v;
			
			$new_authorize["requestTimeStamp"] = $new_authorize["timeStamp"] ?? time();
			$new_authorize["timeStamp"] = time();
			$new_authorize["user_id"] = $USER_ID > 0 ? $USER_ID : "";
			
			if(is_string($TOKEN))
				$new_authorize["token"] = $TOKEN;
			
			header(sprintf("authorize: %s", authorize_function($new_authorize)));
		}
		
		/* Exit. Let the shutdown function do the rest */
		exit;
	}

	register_shutdown_function($HANDLER_SHUTDOWN);
	ob_start();

	main();
}

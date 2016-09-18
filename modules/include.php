<?php
/*
 * Null Pointer Private server
 * Contains common functions
 */

function strpos_array(string $haystack, array $needles) {
	if(is_array($needles)) {
		if(count($needles) == 0)
			return false;
		foreach ($needles as $str) {
			if ( is_array($str) ) {
				$pos = strpos_array($haystack, $str);
			} else {
				$pos = strpos($haystack, $str);
			}
			if ($pos !== FALSE) {
				return $pos;
			}
		}
	} else {
		return strpos($haystack, $needles);
	}
}

function to_datetime(int $timestamp): string
{
	if($timestamp < 86400) $timestamp += 86400;
	
	return date('Y-m-d H:i:s', $timestamp);
}

function to_utcdatetime(int $timestamp): string
{
	if($timestamp < 86400) $timestamp += 86400;
	
	return gmdate('Y-m-d H:i:s', $timestamp);
}

function time_elapsed_string(int $datetime, bool $full = false): string {
	$now = new DateTime;
	$ago = new DateTime("@$datetime");
	$diff = $now->diff($ago);

	$diff->w = floor($diff->d / 7);
	$diff->d -= $diff->w * 7;

	$string = [
		'y' => 'year',
		'm' => 'month',
		'w' => 'week',
		'd' => 'day',
		'h' => 'hour',
		'i' => 'minute',
		's' => 'second',
	];
	foreach ($string as $k => &$v) {
		if ($diff->$k) {
			$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
		} else {
			unset($string[$k]);
		}
	}

	if (!$full) $string = array_slice($string, 0, 1);
	return $string ? implode(', ', $string) . ' ago' : 'just now';
}

/* Get SQLite3 database handle */
function npps_get_database(string $db_name = ''): DatabaseWrapper
{
	static $db_list = [];
	
	if(isset($db_list[$db_name]))
		return $db_list[$db_name];
	else
	{
		if(strlen($db_name) == 0)
			return $db_list[''] = $GLOBALS['DATABASE'];
		
		$xdb = new SQLite3Database("data/$db_name.db_");
		return $db_list[$db_name] = $xdb;
	}
}

function npps_query(string $query, string $list = NULL, ...$arglist)
{
	$DATABASE = $GLOBALS['DATABASE'];
	
	if($list !== NULL)
		return $DATABASE->execute_query($query, $list, ...$arglist);
	else
		return $DATABASE->execute_query($query);
}

function npps_separate(string $delimiter, string $str): array
{
	if(strlen($str) > 0)
	{
		$datalist = explode($delimiter, $str);
		
		array_walk($datalist, function(&$v, $k)
		{
			if(is_numeric($v))
				$v = $v + 0;
		});
		
		return $datalist;
	}
	
	return [];
}

final class npps_nested_transaction
{
	private $nested_count = 0;
	
	private function __construct()
	{
		$this->nested_count = 0;
	}
	
	public function __destruct()
	{
		if($this->nested_count > 0)
		{
			npps_query('COMMIT');
			throw Exception('Unbalanced nested transaction');
		}
	}
	
	public function begin()
	{
		if($this->nested_count == 0)
			npps_query('BEGIN');
		
		$this->nested_count++;
	}
	
	public function commit()
	{
		if($this->nested_count == 0)
			throw Exception('Unbalanced nested transaction');
		if($this->nested_count == 1)
			npps_query('COMMIT');
		
		$this->nested_count--;
	}
	
	public function commit_force()
	{
		if($nested_count > 0)
		{
			npps_query('COMMIT');
			$nested_count = 0;
		}
	}
	
	public static function instance()
	{
		static $x = NULL;
		
		if($x === NULL)
			$x = new npps_nested_transaction();
		
		return $x;
	}
};

function npps_begin_transaction()
{
	npps_nested_transaction::instance()->begin();
}

function npps_end_transaction()
{
	npps_nested_transaction::instance()->commit();
}

function npps_commit_transaction()
{
	npps_nested_transaction::instance()->commit_force();
}


require('modules/include.card.php');
require('modules/include.deck.php');
require('modules/include.item.php');
require('modules/include.live.php');
require('modules/include.token.php');
require('modules/include.user.php');

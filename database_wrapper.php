<?php
/*
 * SIF Private server
 * Provides database support. MySQL or SQLite3 can be used as the database backend.
 */

if(!defined("MAIN_INVOKED")) exit;

/** @@ Configuration @@ **/
// Use SQLite3 instead of MySQL? comment to use SQLite
//define("DBWRAPPER_USE_MYSQL", true);

// MySQL host
define("DBWRAPPER_MYSQL_HOSTNAME", "localhost");

// MySQL username
define("DBWRAPPER_MYSQL_USERNAME", "school_idol");

// MySQL password
define("DBWRAPPER_MYSQL_PASSWORD", "school_idol");

// MySQL port
define("DBWRAPPER_MYSQL_PORT", 3306);

// MySQL db. This is also serves as the SQLite3 database name (with db extension)
define("DBWRAPPER_MYSQL_DB", "school_idol");

// Automatically translate some MySQL-specific keyword to SQLite3 (and vice versa)? comment to disable
define("DBWRAPPER_MYSQL_SQLITE3_COMPAT", true);
/** !! Configuration !! **/ /* End configuration */

function common_initialize_environment(DatabaseWrapper $db, $direct_db = NULL)
{
	$a = file_get_contents('data/initialize_environment.sql');
	$b = file_get_contents('data/login_bonus_list.sql');
	$query = <<<QUERY
BEGIN;
$a
$b
COMMIT
QUERY
	;
	
	if($db->execute_query($query) == false)
		throw new Exception('Unable to initialize environment!');
}

$GLOBALS['common_sqlite3_concat_function'] = function(string ...$arg): string
{
	return implode($arg);
};

/* This class must be inherited */
abstract class DatabaseWrapper
{
	/* The db handle. Can be handle to MySQL connection or SQLite database file */
	protected $db_handle;
	
	abstract function __construct();
	
	/* Initialize the database for the first time */
	abstract public function initialize_environment();
	
	/* Returns array if it returns table or "true" if it's not (but the query success) */
	/* Returns false if the query is failed */
	/* The additional argument is there to supply values. You must use ?, ?, ... in query */
	/* to pass values to INSERT, UPDATE, ... */
	/* values can be single value of array that contain everything or can be passed */
	/* as function argument. If there's multiple SELECT, only the first result are returned */
	abstract public function execute_query(string $query, string $types = NULL, ...$values);
	
	/* Create custom ordering SQL string */
	public function custom_ordering(string $field_name, ...$order): string
	{
		if(is_array($order[0]))
			$order = $order[0];
		
		if(count($order) == 0)
			return "";
		
		$out = ["ORDER BY CASE `$field_name`"];
		$max_len = count($order);
		
		foreach($order as $key => $val)
		{
			if(is_string($val))
				$out[] = "WHEN `$val` THEN $key";
			else
				$out[] = "WHEN $val THEN $key";
		}
		
		$out[] = "ELSE $max_len END";
		return implode(' ', $out);
	}
	
	/* Closes the database handle */
	function __destruct() {}
};

/*****************************************
** Database Wrapper: MySQL Wrapper      **
*****************************************/
class MySQLDatabase extends DatabaseWrapper
{
	function __construct()
	{
		$this->db_handle = new mysqli(DBWRAPPER_MYSQL_HOSTNAME, DBWRAPPER_MYSQL_USERNAME, DBWRAPPER_MYSQL_PASSWORD, DBWRAPPER_MYSQL_DB, DBWRAPPER_MYSQL_PORT);
		
		if($this->db_handle->connect_error)
			throw new Exception('Error ('.$mysqli->connect_errno.') '.$mysqli->connect_error);
		
		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	}
	
	public function initialize_environment()
	{
		if(count($this->execute_query('SHOW TABLES LIKE "school_idol_initialized"')) > 0) return;
		
		common_initialize_environment($this, $this->db_handle);
		$this->db_handle->execute_query('ALTER TABLE `secretbox_list` AUTO_INCREMENT = 1');	// MySQL fix
		
		// Add initialized flag
		$this->execute_query('CREATE TABLE `school_idol_initialized` (unused INTEGER)');
	}
	
	public function execute_query(string $query, string $types = NULL, ...$values)
	{
		if(defined("DBWRAPPER_MYSQL_SQLITE3_COMPAT"))
			$query = str_ireplace('INSERT OR IGNORE', 'INSERT IGNORE', preg_replace('/\?\d*/', "?", str_ireplace("RANDOM", "RAND", $query)));
		
		if(isset($values[0]) && is_array($values[0]))
			$values = $values[0];
		
		if($types != NULL)
		{
			if($stmt = $this->db_handle->prepare($query))
			{
				$stmt->bind_param($types, ...$values);
				
				if($stmt->execute())
				{
					$result = $stmt->get_result();
					
					if($result)
					{
						/* Has result */
						$out = $result->fetch_all(MYSQLI_BOTH);
						$result->free();
						
						return $out;
					}
					
					return true;
				}
				
				if($this->db_handle->error)
					echo 'Error '.$this->db_handle->error, PHP_EOL;
				
				return false;
			}
			
			if($this->db_handle->error)
				echo 'Error '.$this->db_handle->error, PHP_EOL;
			
			return false;
		}
		else
		{
			$result = $this->db_handle->multi_query($query);
			
			if($result == false)
			{
				echo 'Error '.$this->db_handle->error, PHP_EOL;
				return false;
			}
			
			if($result = $this->db_handle->use_result())
			{
				$fields = $result->fetch_fields();
				$result_array = $result->fetch_all(MYSQLI_BOTH);
				$result->free();
				
				/* Convert the datatypes if possible */
				if($fields)
				{
					/* Because associative array, also make fields as assoc */
					foreach($fields as $x)
						$fields[$x->name] = $x;
					
					/* Enum */
					foreach($result_array as &$values)
					{
						foreach($fields as $i => $types)
						{
							$target = &$values[$i];
							
							switch($types->type)
							{
								case MYSQLI_TYPE_TINY:
								case MYSQLI_TYPE_SHORT:
								case MYSQLI_TYPE_LONG:
								case MYSQLI_TYPE_INT24:
								{
									$target = intval($target);
									break;
								}
								case MYSQLI_TYPE_LONGLONG:
								{
									if(PHP_INT_MAX > 2147483647)
										// It's 64-bit. Convert it.
										$target = intval($target);
									
									break;
								}
								case MYSQLI_TYPE_DECIMAL:
								case MYSQLI_TYPE_NEWDECIMAL:
								case MYSQLI_TYPE_DOUBLE:
								case MYSQLI_TYPE_FLOAT:
								{
									$target = floatval($target);
									break;
								}
								default:
								{
									// Do mothing
									break;
								}
							}
						}
					}
				}
				
				return $result_array;
			}
			
			if($this->db_handle->error)
			{
				echo 'Error '.$this->db_handle->error, PHP_EOL;
				return false;
			}
			
			while($this->db_handle->more_results() && $this->db_handle->next_result()) {}
			
			if($this->db_handle->error)
			{
				echo 'Error '.$this->db_handle->error, PHP_EOL;
				return false;
			}
			
			/* No result. Return true */
			return true;
		}
	}
	
	public function __destruct()
	{
		$this->db_handle->close();
	}
};

/*****************************************
** Database Wrapper: SQLite3 Wrapper    **
*****************************************/
class SQLite3Database extends DatabaseWrapper
{
	protected $custom_filename;
	
	public function __construct(string $filename = NULL)
	{
		$custom_filename = false;
		$dbname = DBWRAPPER_MYSQL_DB.'.db';
		
		if($filename)
		{
			$dbname = $filename;
			$this->custom_filename = true;
		}
		
		$this->db_handle = new SQLite3($dbname, $custom_filename ? SQLITE3_OPEN_READONLY : (SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE));
		$this->db_handle->busyTimeout(5000);				// timeout: 5 seconds
		
		if(defined("DBWRAPPER_MYSQL_SQLITE3_COMPAT"))
			$this->db_handle->createFunction('CONCAT', $GLOBALS['common_sqlite3_concat_function']);
	}
	
	public function initialize_environment()
	{
		if($this->custom_filename)
			throw new Exception('Cannot initialize environment when opening another DB file');
		
		if(file_exists(".sqlite3_initialized"))
			return;
		
		fclose(fopen(DBWRAPPER_MYSQL_DB.'.db', 'w'));
		
		if(!defined('DEBUG_ENVIRONMENT'))
			if($this->db_handle->version()['versionNumber'] < 3007000)
				throw new Exception('SQLite3 database wrapper requires SQLite v3.7.0 or later!');
			else
				$this->db_handle->exec("PRAGMA journal_mode=WAL");	// journal mode: WAL; production environment only
		
		common_initialize_environment($this);
		
		touch(".sqlite3_initialized");
	}
	
	public function execute_query(string $query, string $types = NULL, ...$values)
	{
		/* Try to convert the MySQL-specific keyword to SQLite */
		if(defined("DBWRAPPER_MYSQL_SQLITE3_COMPAT"))
			$query = str_ireplace("AUTO_INCREMENT", "AUTOINCREMENT", str_ireplace("LAST_INSERT_ID", "last_insert_rowid", $query));
		
		if(isset($values[0]) && is_array($values[0]))
			$values = $values[0];
		
		var_dump($query);
		
		if($types != NULL)
		{
			if($stmt = $this->db_handle->prepare($query))
			{
				foreach($values as $k => $v)
				{
					$datatype = SQLITE3_NULL;
					
					switch($types[$k])
					{
						case "b":
						{
							$datatype = SQLITE3_BLOB;
							break;
						}
						case "d":
						{
							$datatype = SQLITE3_FLOAT;
							break;
						}
						case "i":
						{
							$datatype = SQLITE3_INTEGER;
							break;
						}
						case "s":
						{
							$datatype = SQLITE3_TEXT;
							break;
						}
						default:
						{
							unset($datatype);
							break;
						}
					}
					
					$stmt->bindValue($k + 1, $v, $datatype);
				}
				
				if($result = $stmt->execute())
				{
					if($result->numColumns())
					{
						/* There's result */
						$out = [];
						
						while($row = $result->fetchArray(SQLITE3_BOTH))
							$out[] = $row;
						
						return $out;
					}
					
					return true;
				}
				
				return false;
			}
		}
		else
		{
			if(stripos($query, 'SELECT') === false)
				return $this->db_handle->exec($query);
			
			$result = $this->db_handle->query($query);
			
			if($result == false)
				return false;
			
			$out = [];
			
			while($row = $result->fetchArray(SQLITE3_BOTH))
				$out[] = $row;
			
			return $out;
		}
	}
	
	function __destruct()
	{
		$this->db_handle->close();
	}
};

if(defined("DBWRAPPER_USE_MYSQL"))
	return new MySQLDatabase();
else
	return new SQLite3Database();
?>
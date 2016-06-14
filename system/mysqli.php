<?php
/**
 * Wrapper class for php's MySQLi extention
 *
 * @example $this->prepare("SELECT * FROM `table` WHERE `column` = ?;")->execute($value)->fetch($method);
 * @author Shane van den Bogaard
 */
class Mysqli_db {

	/**
	 * @var bool Enables debugging
	 */
	private $_debug = false;

	/**
	 * @var mysqli The connected mysqli object
	 */
	private $_connection = null;

	/**
	 * @var mysqli_stmt The mysqli statement object
	 */
	private $_stmt = null;

	/**
	 * @var int The number of rows in the result set
	 */
	private $_num_rows = 0;

	/**
	 * @var int The number of rows affected by INSERT, UPDATE or DELETE
	 */
	private $_affected_rows = 0;

	/**
	 * @var bool Checks if the SQL statement is prepared to be executed
	 */
	private $_prepared = false;

	/**
	 * @var Mysqli_db Singleton of this class
	 */
	private static $_singleton = null;

	public function __construct() {
		global $config;
		$this->_connection = $this->_connect($config['db']);
	}

	/**
	 * Connect to the database
	 *
	 * @param assoc array The database configuration
	 * @param bool Creates a persistant connection
	 * @return mysqli Returns the mysqli instance
	 */
	private function _connect($cfg, $persistant = true) {
		if (!$cfg['enabled']) return;

		$host = $persistant ? 'p:'.$cfg['host'] : $cfg['host'];

		$mysqli = new mysqli($host, $cfg['user'], $cfg['pswd']);

		if ($this->_debug && $mysqli->connect_error) throw new Exception('thrown mysqli error: '. $mysql->connect_error, 1);
		
		return $mysqli->connect_error ? null : $this->_select_db($mysqli, $cfg['name']);
	}

	/**
	 * Create database if not exists and selects that database
	 *
	 * @param mysqli The mysqli connection
	 * @param string The database name
	 * @return mysqli Returns the connection to the database
	 */
	private function _select_db($mysqli, $name) {
		$mysqli->query("CREATE DATABASE IF NOT EXISTS `$name`;");
		$mysqli->select_db($name);

		return $mysqli;
	}

	/**
	 * Disconnects the connection to the database
	 */
	private function _disconnect() {
		if ($this->_connection instanceof mysqli) $this->_connection->close();
	}

	/**
	 * Checks if the MySQL connection was made
	 *
	 * @return bool Returns true if the MySQL successfully pinged the server and selected db
	 */
	public function connected() {
		return $this->_connection instanceof mysqli ? $this->_connection->ping() : false;
	}

	/**
	 * Executes the SQL statement
	 *
	 * @param string The SQL query
	 * @return this
	 */
	public function query($sql) {
		$this->_num_rows = 0;
		$this->_affected_rows = 0;

		if ($this->_connection instanceof mysqli) {
			$stmt = $this->_connection->query($sql);

			$this->_affected_rows = $this->_connection->affected_rows;
			$this->_stmt = $stmt;
		}

		if ($this->_debug && $this->_connection->error) throw new Exception("MySQLI error: " . $this->_connection->error, 1);
		
		return $this;
	}

	/**
	 * Prepare the SQL statement
	 *
	 * @param string The SQL query
	 * @return this
	 */
	public function prepare($sql) {
		$this->_num_rows = 0;
		$this->_affected_rows = 0;

		$this->_prepared = true;
		if ($this->_debug) echobr("preparing...".$sql);

		if ($this->_connection instanceof mysqli) {
			if ($this->_debug) echobr("\$this->_connection is instance of mysqli");
			$this->_stmt = $this->_connection->prepare($sql);
		}

		if ($this->_debug && $this->_connection->error) throw new Exception("MySQLI error: " . $this->_connection->error, 1);

		return $this;
	}

	/**
	 * Executes the prepared SQL statement
	 *
	 * @param mixed The function arguments can be used to bind to the SQL statement
	 * @return this
	 */
	public function execute() {
		if ($this->_connection instanceof mysqli &&  $this->_stmt instanceof mysqli_stmt && $this->_prepared) {
			if ($this->_debug) echobr("instanceof mysqli && instanceof mysqli_stmt");
			if (count($args = func_get_args()) > 0) {
				$types = array();
				$params = array();
				foreach ($args as $arg) {
					$types[] = is_int($arg) ? 'i' : (is_float($arg) ? 'd' : 's');
					$params[] = $this->_connection->real_escape_string($arg);
				}
				array_unshift($params, implode($types));

				call_user_func_array(array($this->_stmt, 'bind_param'), $this->_pass_by_reference($params));
			} else if ($this->_debug) echobr("count is -1"); 

			if ($this->_stmt->execute()) $this->_affected_rows = $this->_stmt->affected_rows;
		}
		$this->_prepared = false;

		return $this;
	}

	/**
	 * Get the results as an array, the type of array depends on the method passed through
	 *
	 * @param string The array method, default assoc (associative)
	 * @param bool Whether or not the statement should be destroyed after execution, default false
	 * @return array Returns an array of database results
	 */
	/*public function fetch($method = 'assoc', $destroy = false) {
		$results = array();
		if ($this->_debug) echobr("fetching");
		if ($this->_stmt instanceof mysqli_stmt || is_object($this->_stmt)) {
			if ($this->_debug) echobr("instanceof mysqli_stmt || is_object");
			$stmt_type = get_class($this->_stmt);

			if ($this->_debug) debug($this->_stmt);

			switch ($stmt_type) {
				case 'mysqli_stmt':
					$this->_stmt->bind_result($name, $code);

					$result = $this->_stmt->fetch();
					// $result = $this->_stmt->get_result();
					$close = 'close';
					break;
				case 'mysqli_result':
					$result = $this->_stmt;
					$close = 'free';
					break;
			}

			if ($result != null) {
				$this->_num_rows = $result->num_rows;

				$method = "fetch_$method";
				while ($row = $result->$method()) $results[] = $row;

				$result->$close();
				if ($destroy) $this->_stmt = null;
			}
		}
		return $results;
	}*/

	/**
	 *  MYSQL Native Drive fix
	 * Get the results as an array, the type of array depends on the method passed through
	 *
	 * @param string The array method, default assoc (associative)
	 * @param bool Whether or not the statement should be destroyed after execution, default false
	 * @return array Returns an array of database results
	 */
	public function fetch(){    
	    $array = array();
	    
	    if($this->_stmt instanceof mysqli_stmt)
	    {
	        $this->_stmt->store_result();
	        
	        $variables = array();
	        $data = array();
	        $meta = $this->_stmt->result_metadata();
	        
	        while($field = $meta->fetch_field())
	            $variables[] = &$data[$field->name]; // pass by reference
	        
	        call_user_func_array(array($this->_stmt, 'bind_result'), $variables);
	        
	        $i=0;
	        while($this->_stmt->fetch())
	        {
	            $array[$i] = array();
	            foreach($data as $k=>$v)
	                $array[$i][$k] = $v;
	            $i++;
	            
	            // for some reason when using $array[] = $data, it stores the same result in all rows
	        }
	    }
	    elseif($this->_stmt instanceof mysqli_result) {
	        while($row = $this->_stmt->fetch_assoc())
	            $array[] = $row;
	    }
	    
	    return $array;
	}

	/**
	 * Checks if the table exists
	 *
	 * @param string The table name
	 * @return bool Returns true if the table exists  
	 */
	public function table_exists($name) {
		return $this->_connection->query("SELECT 1 FROM $name") != null;
	}

	/**
	 * Get the number or rows in the statement's result set
	 *
	 * @return int The number of rows in the result set
	 */
	public function get_num_rows() {
		return $this->_num_rows;
	}

	/**
	 * Get the number of affected rows in a previous MySQL operation
	 *
	 * @return int The number of rows affected by INSERT, UPDATE or DELETE
	 */
	public function get_affected_rows() {
		return $this->_affected_rows;
	}

	/**
	 * Get the last auto-incrementated ID associated with an insertion
	 *
	 * @return int Returns the last auto-incremented ID
	 */
	public function get_incremented_id() {
		return $this->_connection->insert_id;
	}

	/**
	 * Fix for the call_user_func_array and bind_param pass by reference bs
	 *
	 * @param The array to be referenced
	 * @return array Returns a referenced array
	 */
	private function _pass_by_reference(&$arr) {
		$refs = array();
		foreach ($arr as $key => $value) $refs[$key] = &$arr[$key];

		return $refs;
	}

	public function debug($flag) {
		$this->_debug = $flag;

		return $this;
	}

	/**
	 * Static reference to this class' object
	 *
	 * @return self
	 */
	public static function instance() {
		self::$_singleton = self::$_singleton instanceof self ? self::$_singleton : new self();
		
		return self::$_singleton;
	}
}

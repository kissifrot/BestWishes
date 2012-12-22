<?php
class BwDatabase
{
	protected $db = null;

	private $dbType;
	private $dbServer;
	private $dbUser;
	private $dbPassword;
	private $dbPort;
	private $dbName;
	private $dbPrefix;

	protected static $instance;

	protected $currentStatement = null;
	protected $currentQueryType = null;
	protected $queryAutoField = null;

	protected $debugMode;

	public static function getInstance()
	{
		if (!isset (self::$instance))
			self::$instance = new self;
		
		return self::$instance;
	}

	protected function __construct()
	{
		// Import params from config file
		global $confDBType, $confDBServer, $confDBName, $confDBPrefix, $confDBUser, $confDBPasswd, $confDBPort;

		$this->dbType     = (!empty($confDBType)) ? strtolower($confDBType) : '';
		$this->dbServer   = $confDBServer;
		$this->dbUser     = $confDBUser;
		$this->dbPassword = $confDBPasswd;
		$this->dbPort     = (!empty($confDBPort)) ? intval($confDBPort) : 0;
		$this->dbName     = $confDBName;
		$this->dbPrefix   = $confDBPrefix;

		$this->debugMode = BwDebug::getDebugMode();

		$this->connect();
	}

	private function connect()
	{
		$pdoDefaultOptions = array(
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		);
		try
		{
			switch($this->dbType) {
				case 'mysql':
					if (version_compare(PHP_VERSION, '5.3.6', '<')) {
						$pdoOptions = $pdoDefaultOptions + array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
						$this->db = new PDO('mysql:host=' .$this->dbServer. ';port=' . $this->dbPort . ';dbname=' .$this->dbName, $this->dbUser, $this->dbPassword, $pdoOptions);
					} else {
						$this->db = new PDO('mysql:host=' .$this->dbServer. ';port=' . $this->dbPort . ';dbname=' .$this->dbName. ';charset=utf8', $this->dbUser, $this->dbPassword, $pdoDefaultOptions);
					}
				break;
				case 'postgresql':
					$this->db = new PDO('pgsql:host=' .$this->dbServer. ';port=' . $this->dbPort . ';dbname=' .$this->dbName, $this->dbUser, $this->dbPassword, $pdoDefaultOptions);
				break;
				default:
					throw new Exception('Unsupported DB type');
			}
		}
		catch(PDOException $e)
		{
			exit('<span style="color: #ff0000">Connection error!<br /><strong>' . $e->getMessage() . '</strong></span>');
		}
		catch(Exception $e)
		{
			exit('<span style="color: #ff0000">Error!<br /><strong>' . $e->getMessage() . '</strong></span>');
		}
	}

	/**
	 *
	 */
	public function prepareQuery($queryParams = array())
	{
		if(empty($queryParams))
			return false;
		
		$defaults = array(
			'queryType' => 'SELECT',
			'queryFields' => '',
			'queryCondition' => '',
			'queryValues' => '',
			'queryOrderBy' => '',
			'queryLimit' => '',
			'queryAutoField' => ''
		);
		$params = array_merge($defaults, $queryParams);
		if(!isset($params['tableName']))
			return false;

		$this->queryAutoField = null;
		switch($params['queryType']) {
			case 'SELECT':
				$this->currentQueryType = $params['queryType'];
				$query = 'SELECT ';
				if(is_array($params['queryFields'])) {
					$query .= implode(', ', $params['queryFields']);
				} else {
					if(!empty($params['queryFields'])) {
						$query .= $params['queryFields'];
					} else {
						$query .= '*';
					}
				}
				$query .= ' FROM ' . $this->dbPrefix . $params['tableName'];
				if(!empty($params['queryCondition'])) {
					if(!is_array($params['queryCondition'])) {
						$query .= ' WHERE ' . $params['queryCondition'];
					} else {
						$query .= ' WHERE (';
						$query .= implode(' AND ', $params['queryCondition']);
						$query .= ')';
					}
				}
				if(!empty($params['queryOrderBy'])) {
					$query .= ' ORDER BY ' . $params['queryOrderBy'];
				}
				if(!empty($params['queryLimit'])) {
					$query .= ' LIMIT ' . $params['queryLimit'];
				}
			break;
			case 'UPDATE':
				$this->currentQueryType = $params['queryType'];
				if(empty($params['queryValues']) || empty($params['queryFields'])) {
					return false;
				}
				$query = 'UPDATE ' . $this->dbPrefix . $params['tableName'] . ' SET ';
				if(is_array($params['queryFields'])) {
					foreach($params['queryFields'] as $queryField => $queryVariable) {
						$query .= $queryField . ' = ' . $queryVariable . ', ';
					}
					// Remove the last " ," from the query string
					$query = substr($query, 0, - 2);
				} else {
					$query .= '*';
				}
				$query .= ' WHERE ';
				if(is_array($params['queryCondition'])) {
					$query .= '(';
					$query .= implode(' AND ', $params['queryCondition']);
					$query .= ')';
				} else {
					$query .= $params['queryCondition'];
				}
			break;
			case 'INSERT':
				$this->currentQueryType = $params['queryType'];
				if(empty($params['queryValues']) || empty($params['queryFields'])) {
					return false;
				}
				$query = 'INSERT INTO ' . $this->dbPrefix . $params['tableName'] . ' (';
				if(is_array($params['queryFields'])) {
					$query .= implode(', ', array_keys($params['queryFields']));
				}
				$query .= ') VALUES ( ';
					$query .= implode(', ', array_values($params['queryFields']));
				$query .= ')';
				// Special case for PostgreSQL to get the inserted id
				if($this->dbType === 'postgresql' && !empty($params['queryAutoField'])) {
					$query .= ' RETURNING ' . $params['queryAutoField'];
					$this->queryAutoField = $params['queryAutoField'];
				}
			break;
			case 'DELETE':
				$this->currentQueryType = $params['queryType'];
				if(empty($params['queryValues'])) {
					return false;
				}
				$query = 'DELETE FROM ' . $this->dbPrefix . $params['tableName'];
				$query .= ' WHERE ';
				if(is_array($params['queryCondition'])) {
					$query .= '(';
					$query .= implode(' AND ', $params['queryCondition']);
					$query .= ')';
				} else {
					$query .= $params['queryCondition'];
				}
			break;
			default:
			return false;
		}
		// Prepare the PDO statement
		try
		{
			$this->currentStatement = $this->db->prepare($query);
			// Bind the params given
			if(is_array($params['queryValues'])) {
				if(!empty($params['queryValues'])) {
					foreach($params['queryValues'] as $queryValue) {
						$this->currentStatement->bindParam(
							$queryValue['parameter'],
							$queryValue['variable'],
							$queryValue['data_type']
						);
					}
				}
			}
			return true;
		}
		catch(PDOException $e)
		{
			$this->currentStatement = null;
			if($this->debugMode > 0) {
				BwDebug::storeError($e->getMessage());
			}
			return false;
			// exit('<span style="color: #ff0000">Query error!<br /><strong>' . $e->getMessage() . '</strong></span>');
		}
	}

	/**
	 * Prepare a manual query
	 */
	public function prepare($query)
	{
		$this->currentStatement = $this->db->prepare($query);
	}

	/**
	 * 
	 */
	public function bindParams($params = array())
	{
		// Bind the params given
		if(!empty($params)) {
			foreach($params as $queryValue) {
				$this->currentStatement->bindParam(
					$queryValue['parameter'],
					$queryValue['variable'],
					$queryValue['data_type']
				);
			}
		}
	}

	/**
	 *
	 */
	private function executeStatement()
	{
		if(empty($this->currentStatement))
			return false;

		try {
			$execResult = $this->currentStatement->execute();
			if($this->debugMode == BwDebug::LOG_ALL) {
				BwDebug::storeQuery($this->currentStatement->queryString);
			} else {
				if($this->debugMode > 0) {
					BwDebug::incrementQueriesCount();
				}
			}
			return $execResult;
		}
		catch(PDOException $e)
		{
			if($this->debugMode == BwDebug::LOG_ALL) {
				BwDebug::storeQuery($this->currentStatement->queryString);
			}
			if($this->debugMode > 0) {
				BwDebug::storeError($e->getMessage());
			}
			return false;
		}
	}

	/**
	 *
	 */
	public function fetch($fetchMode = PDO::FETCH_ASSOC)
	{
		if(!$this->executeStatement())
			return false;
		
		try {
			return $this->currentStatement->fetch($fetchMode);
		} 
		catch(PDOException $e)
		{
			if($this->debugMode > 0) {
				BwDebug::storeError($e->getMessage());
			}
			return false;
		}
	}

	/**
	 *
	 */
	public function fetchAll($fetchMode = PDO::FETCH_ASSOC)
	{
		if(!$this->executeStatement())
			return false;
		
		try {
			return $this->currentStatement->fetchAll($fetchMode);
		} 
		catch(PDOException $e)
		{
			if($this->debugMode > 0) {
				BwDebug::storeError($e->getMessage());
			}
			return false;
		}
	}

	/**
	 *
	 */
	public function closeQuery()
	{
		if(empty($this->currentStatement))
			return false;

		$closeResult = $this->currentStatement->closeCursor();
		$this->currentStatement = null;
		
		return $closeResult;
	}

	/**
	 *
	 */
	public function exec()
	{
		return $this->executeStatement();
	}

	/**
	 *
	 */
	public function lastInsertId()
	{
		if($this->dbType != 'postgresql') {
			return $this->db->lastInsertId();
		} else {
			// Try to retrieve the auto incremented field for PostgreSQL
			if(!empty($this->queryAutoField)) {
				$result = $this->fetch();
				return $result[$this->queryAutoField];
			} else {
				return $this->db->lastInsertId();
			}
		}
	}

	/**
	 *
	 */
	public function disconnect()
	{
		if( $this->db != null )
			$this->db = null; 
	}
}
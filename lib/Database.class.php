<?php
class Database
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

		$this->dbType     = $confDBType;
		$this->dbServer   = $confDBServer;
		$this->dbUser     = $confDBUser;
		$this->dbPassword = $confDBPasswd;
		$this->dbPort     = $confDBPort;
		$this->dbName     = $confDBName;
		$this->dbPrefix   = $confDBPrefix;

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
						$pdoOptions = array_merge($pdoDefaultOptions, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
						$this->db = new PDO('mysql:host=' .$this->dbServer. ';port=' . $this->dbPort . ';dbname=' .$this->dbName, $this->dbUser, $this->dbPassword, $pdoOptions);
					} else {
						$this->db = new PDO('mysql:host=' .$this->dbServer. ';port=' . $this->dbPort . ';dbname=' .$this->dbName. ';charset=UTF-8', $this->dbUser, $this->dbPassword, $pdoDefaultOptions);
					}
				break;
				case 'postgresql':
					$this->db = new PDO('pgsql:host=' .$this->dbServer. ';port=' . $this->dbPort . ';dbname=' .$this->dbName. ';charset=UTF-8', $this->dbUser, $this->dbPassword, $pdoDefaultOptions);
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
	
	public function disconnect()
	{
		if( $this->db != null )
			$this->db = null; 
	}
}
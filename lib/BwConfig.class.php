<?php
/**
 * Configuration managment class
 */
class BwConfig
{
	protected static $instance;
	
	protected $configDirectives;

	public function __construct()
	{
		$this->load();
	}

	private function load()
	{
		$db = BwDatabase::getInstance();
		$queryParams = array(
			'tableName' => 'config',
			'queryType' => 'SELECT',
			'queryFields' => '*',
			'queryCondition' => '',
			'queryValues' => '',
		);
		if($db->prepareQuery($queryParams)) {
			$results = $db->fetchAll();
			if($results === false)
				return $results;
			
			if(!empty($results)) {
				foreach($results as $result) {
					$this->configDirectives[$result['config_key']] = $result['config_value'];
				}
			}
			return true;
		}
	}

	private function save($key, $value, $create = false) {
		if(empty($key)) {
			return false;
		}

		$db = BwDatabase::getInstance();
		if(!$create) {
			// Just update the value
			$queryParams = array(
				'tableName' => 'config',
				'queryType' => 'UPDATE',
				'queryFields' => array(
					'config_value' => ':conf_value'
				),
				'queryCondition' => 'config_key = :conf_key',
				'queryValues' => array(
					array(
						'parameter' => ':conf_value',
						'variable' => $value,
						'data_type' => PDO::PARAM_STR
					),
					array(
						'parameter' => ':conf_key',
						'variable' => $key,
						'data_type' => PDO::PARAM_STR
					)
				)
			);
		} else {
			// We're creating a new key/value pair
			$queryParams = array(
				'tableName' => 'config',
				'queryType' => 'INSERT',
				'queryFields' => array(
					'config_value' => ':conf_value',
					'config_key' => ':conf_key'
				),
				'queryValues' => array(
					array(
						'parameter' => ':conf_value',
						'variable' => $value,
						'data_type' => PDO::PARAM_STR
					),
					array(
						'parameter' => ':conf_key',
						'variable' => $key,
						'data_type' => PDO::PARAM_STR
					)
				)
			);
		}
		if($db->prepareQuery($queryParams)) {
			$resultExec = $db->exec();
			if($resultExec === false)
				return $resultExec;
			
			return true;
		}
		return false;
	}

	public static function getInstance()
	{
		if (!isset(self::$instance))
		{
			self::$instance = new self;
		}
		return self::$instance;
	}

	public static function get($key, $default = '') {
		$config = self::getInstance();
		if(!isset($config->configDirectives[$key])) {
			return $default;
		}

		return $config->configDirectives[$key];
	}

	public static function set($key, $value = '', $create = false) {
		$config = self::getInstance();
		if(!isset($config->configDirectives[$key]) && !$create) {
			return false;
		}

		$config->configDirectives[$key] = $value;
		return $config->save($key, $value, $create);
	}

}
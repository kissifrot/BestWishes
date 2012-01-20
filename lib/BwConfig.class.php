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
		if(!defined('INSTALL_MODE')) {
			$this->load();
		}
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
					$this->configDirectives[$result['config_key']] = array();
					$this->configDirectives[$result['config_key']]['value']  = $result['config_value'];
					$this->configDirectives[$result['config_key']]['type']   = $result['value_type'];
					$this->configDirectives[$result['config_key']]['regex'] = $result['value_regex'];
				}
			}
			return true;
		}
	}

	private function save($key, $value, $create = false, $type = 'string', $regex = '') {
		if(empty($key)) {
			return false;
		}

		$db = BwDatabase::getInstance();
		if(!$create) {
			// Just update the value
			if(!isset($config->configDirectives[$key])) {
				return false;
			}
			if($this->configDirectives[$key]['type'] === 'email') {
				if(filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
					return false;
				}
			} elseif($this->configDirectives[$key]['type'] === 'numeric') {
				if(!is_numeric($value)) {
					return false;
				}
			}
			if(!empty($this->configDirectives[$key]['regex'])) {
				// Check for a regex
				if(preg_match('#' . $this->configDirectives[$key]['regex'] . '#i', $value) < 1) {
					return false;
				}
			}
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
			$queryFields = array(
				'config_value' => ':conf_value',
				'config_key' => ':conf_key',
				'value_type' => ':value_type'
			);
			$queryValues = array(
				array(
					'parameter' => ':conf_value',
					'variable' => $value,
					'data_type' => PDO::PARAM_STR
				),
				array(
					'parameter' => ':conf_key',
					'variable' => $key,
					'data_type' => PDO::PARAM_STR
				),
				array(
					'parameter' => ':value_type',
					'variable' => $type,
					'data_type' => PDO::PARAM_STR
				)
			);
			if(!empty($regex)) {
				$queryValues[] = array(
					'parameter' => ':value_regex',
					'variable' => $regex,
					'data_type' => PDO::PARAM_STR
				);
				$queryFields['value_regex'] = ':value_regex';
			}
			$queryParams = array(
				'tableName' => 'config',
				'queryType' => 'INSERT',
				'queryFields' => $queryFields,
				'queryValues' => $queryValues
			);
		}
		if($db->prepareQuery($queryParams)) {
			$resultExec = $db->exec();
			if($resultExec === false)
				return $resultExec;
			
			if(!$create) {
				// Update the current variables
				$this->configDirectives[$key]['value'] = $value;
			}
			return true;
		}
		return false;
	}
	
	private function create($key, $value = '', $type = 'string', $regex = '')
	{
		if(!isset($config->configDirectives[$key])) {
			return false;
		}
		return $this->save($key, $value, true, $type, $regex);
	}

	public static function getInstance()
	{
		if (!isset(self::$instance))
		{
			self::$instance = new self;
		}
		return self::$instance;
	}

	public static function createDirective($key, $value = '', $type = 'string', $regex = ''){
		$config = self::getInstance();
		return $config->create($key, $value, $type, $regex);
	}

	public static function get($key, $default = '') {
		$config = self::getInstance();
		if(!isset($config->configDirectives[$key])) {
			return $default;
		}

		return $config->configDirectives[$key]['value'];
	}

	public static function set($key, $value = '') {
		$config = self::getInstance();
		if(!isset($config->configDirectives[$key])) {
			return false;
		}

		$config->configDirectives[$key]['value'] = $value;
		return $config->save($key, $value);
	}

}
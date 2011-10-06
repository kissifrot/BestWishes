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

}
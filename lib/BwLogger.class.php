<?php
/**
 * Logs management class
 */
class BwLogger
{
	public static $logFilePath;

	/**
	 *
	 */
	public static function log($message)
	{
		self::$logFilePath = BwConfig::get('log_filepath', false);
		if(!empty($message) && !empty(self::$logFilePath)) {
			$message = date('Y-m-d H:i:s'). ':' . "\t" . $message . "\n";
			@file_put_contents(self::$logFilePath, $message, FILE_APPEND);
		}
		return false;
	}

	/**
	 *
	 */
	public static function dump($data)
	{
		self::$logFilePath = BwConfig::get('log_filepath', false);
		if(!empty(self::$logFilePath)) {
			$message = date('Y-m-d H:i:s'). ':' . "\t" . var_export($data, true) . "\n";
			@file_put_contents(self::$logFilePath, $message, FILE_APPEND);
		}
		return false;
	}
}
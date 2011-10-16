<?php
class BwClassAutoloader
{
	protected static $instance;

	public function __construct()
	{
		spl_autoload_register(array($this, 'loader'));
		if(!defined('DS')) {
			define('DS', DIRECTORY_SEPARATOR);
		}
	}

	private function loader($className) {
		global $bwLibDir;

		// Do not autoload other classes
		if(preg_match('#^Bw#', $className) < 1) {
			return;
		}
		$fileToLoad = $bwLibDir . DS . $className . '.class.php';
		if(!is_file($fileToLoad)) {
			exit('<span style="color: #ff0000">Could not load file ' . $fileToLoad . '</span>');
		}
		// echo "including $fileToLoad <br />\n";
		require_once($fileToLoad);
	}

	public static function getInstance()
	{
		if (!isset(self::$instance))
		{
			self::$instance = new self();
		}
		return self::$instance;
  }
}
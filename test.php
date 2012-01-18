<?php
/**
 * Misc tests
 */
if (version_compare(PHP_VERSION, '5.2.0', '<')) exit("Sorry, BestWishes will only run on PHP version 5.2.0 or greater!\n");

define('BESTWISHES', true);

// Load config
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.inc.php');
// Load other needed files
require_once($bwLibDir . DS . 'BwCommon.inc.php');
require_once($bwLibDir . DS . 'BwClassAutoloader.class.php');

$autoloader = BwClassAutoloader::getInstance();

$httpPath = 'http' . (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 's' : '') . '://' . (empty($_SERVER['HTTP_HOST']) ? $_SERVER['SERVER_NAME'] . (empty($_SERVER['SERVER_PORT']) || $_SERVER['SERVER_PORT'] == '80' ? '' : ':' . $_SERVER['SERVER_PORT']) : $_SERVER['HTTP_HOST']) . (strtr(dirname($_SERVER['PHP_SELF']), '\\', '/') == '/' ? '' : strtr(dirname($_SERVER['PHP_SELF']), '\\', '/')) . '/install.php';
echo $httpPath;

var_dump(BwDebug::getDebugMode());

$db = BwDatabase::getInstance();
var_dump($db);
$lang = new BwLang();
var_dump($lang);
var_dump($lang->getLangList());

var_dump(BwDebug::getInstance());

ob_start();
phpinfo(INFO_MODULES);
$phpinfo = ob_get_contents();
ob_end_clean();

var_dump(preg_match('#PDO#', $phpinfo));
if(preg_match('#PDO#', $phpinfo) > 0) {
	echo "Seems PDO is available<br />\n";
}

var_dump(BwEvent::getAllActiveEvents());


$user = new BwUser();
$password = 'v8aLTIZd';
$user->login('Kissifrot', $password);

var_dump(BwInflector::slug('Ceci est un .2...'));

var_dump(BwDebug::getInstance());
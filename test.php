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

var_dump(BwDebug::getDebugMode());
var_dump(BwDebug::setDebugMode(BwDebug::LOG_ALL));

$db = BwDatabase::getInstance();
var_dump($db);
$lang = new BwLang();
var_dump($lang);
var_dump($lang->getLangList());

$config = BwConfig::get('foo');

var_dump($config);

var_dump(BwConfig::set('foo', 'bar', true));
var_dump(BwConfig::set('foo', 'bar'));
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

$slug = 'foobar';
$list = new BwList($slug);
$list->load();
var_dump($list->getNearestEventData());

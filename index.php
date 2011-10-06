<?php
if (version_compare(PHP_VERSION, '5.2.0', '<')) exit("Sorry, BestWishes will only run on PHP version 5.2.x or greater!\n");

define('BESTWISHES', true);

// Load config
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.inc.php');

require_once($bwLibDir . DS . 'BwClassAutoloader.class.php');

$autoloader = BwClassAutoloader::getInstance();

$db = BwDatabase::getInstance();
var_dump($db);
$lang = new BwLang();
var_dump($lang);
var_dump($lang->getLangList());

$config = BwConfig::get('foo');

var_dump($config);

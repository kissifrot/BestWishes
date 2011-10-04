<?php
if (version_compare(PHP_VERSION, '5.2.0', '<')) exit("Sorry, BewtWishes will only run on PHP version 5.2.x or greater!\n");

define('BESTWISHES', true);

// Load config
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.inc.php');

require_once($bwLibDir . DS . 'BwClassAutoloader.class.php');

$autoloader = BwClassAutoloader::getInstance();

$db = Database::getInstance();
var_dump($db);
$lang = new Lang();
var_dump($lang);
var_dump($lang->getLangList());


<?php
/**
 * Show a list
 */
if (version_compare(PHP_VERSION, '5.2.0', '<')) exit("Sorry, BestWishes will only run on PHP version 5.2.0 or greater!\n");

define('BESTWISHES', true);

// Load config
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.inc.php');
// Load other needed files
require_once($bwLibDir . DS . 'BwCommon.inc.php');
require_once($bwLibDir . DS . 'BwClassAutoloader.class.php');

$autoloader = BwClassAutoloader::getInstance();

if(isset($_GET['slug']) && !empty($_GET['slug'])) {
	$slug = $_GET['slug'];
} else {
	exit('List not specified');
}
BwDebug::setDebugMode(2);
$db = BwDatabase::getInstance();


if(BwUser::checkSession()) {
	
} else {
	$display = new BwDisplay(BwConfig::get('theme', 'default'));
}
$subTitle = '';
$list = new BwList($slug);
if($list->load()) {
	$display->header('Affichage d\'une liste : Liste de '.$list->name, $subTitle);
	$display->footer();
}




<?php
/**
 * Show a list
 */
define('BESTWISHES', true);

// Load config
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.inc.php');
// Load other needed files
require_once($bwLibDir . DS . 'BwCommon.inc.php');
require_once($bwLibDir . DS . 'BwClassAutoloader.class.php');

$autoloader = BwClassAutoloader::getInstance();

if(isset($_GET['id']) && !empty($_GET['id'])) {

	$id = intval($_GET['id']);
} else {
	exit('List not specified');
}
$db = BwDatabase::getInstance();

if(!BwUser::checkSession()) {
	exit;
}
$user = BwUser::getInstance();
$disp = new BwSessionDisplay($user->getTheme()->shortName, $user);
$list = new BwList();
if($list->loadById($id)) {
	$allCats = BwCategory::getAllByListId($id);
	foreach($allCats as $cat) {
		unset($cat->gifts);
	}
	$disp->showJSONData($allCats);
}
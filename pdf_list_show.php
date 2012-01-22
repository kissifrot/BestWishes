<?php
/**
 * Show a PDF list
 */
define('BESTWISHES', true);

// Load common
require_once(dirname(__FILE__). DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'BwCommon.inc.php');

$autoloader = BwClassAutoloader::getInstance();

if(isset($_GET['slug']) && !empty($_GET['slug'])) {
//éè
	$slug = BwInflector::slug($_GET['slug']);
} else {
	exit('List not specified');
}
$db = BwDatabase::getInstance();

if(BwUser::checkSession()) {
	$sessionOk = true;
	$user = BwUser::getInstance();
	$user->loadParams();
} else {
	// Nobody logged
	$sessionOk = false;
	$user = null;
	$publicLists = BwConfig::get('public_lists', 'true');
	// Stop if not public
	// TODO: Tweak this
	if(!boolVal($publicLists)) {
		echo _('You must be logged in to view this list');
		exit;
	}
}

// Load and display the list
$subTitle = '';
$list = new BwList($slug);
if($list->load()) {
	// Remove some categories/gifts depending on the situation
	$list->filterContent($sessionOk, $user);
	$listTitle = sprintf(_('%s\'s list'), $list->name);
	if(!empty($list->lastUpdate)) {
		$subTitle = sprintf(_('(last update on: %s)'), date(_('m/d/Y'), strtotime($list->lastUpdate)));
	} else {
		$subTitle = ('(last update on: n/a)');
	}
	// Outpuf the PDF
	$list->PdfOutput($listTitle, $subTitle, $sessionOk, $user);
}
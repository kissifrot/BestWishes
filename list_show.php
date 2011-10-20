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
	$publicLists = BwConfig::get('public_lists', 'true');
	$disp = new BwDisplay(BwConfig::get('theme', 'default'));
	// Stop if not public
	// TODO: Tweak this
	if(!boolVal($publicLists)) {
		$disp->header(_('This list is not public'), _('You must be logged in to view this list'));
		echo _('You must be logged in to view this list');
		$disp->footer();
		exit;
	}
}
// Translation strings
$disp->assign('lngPossibleActions', _('Possible actions:'));
$disp->assign('lngInfoEmptyList', _('(This list is still empty)'));
$disp->assign('lngDetails', _('Details'));

// Load and display the list
$subTitle = '';
$list = new BwList($slug);
if($list->load()) {
	$listTitle = sprintf(_('List display: %s\'s list'), $list->name);
	$nextEventData = $list->getNearestEventData();
	$daysLeft = intval($nextEventData['daysLeft']);
	$eventText = sprintf(_('Next event (%s): '), $nextEventData['name']);
	if($daysLeft > 0) {
		$eventText .= sprintf(ngettext('%d day', '%d days', $daysLeft), $daysLeft);
	} else {
		$eventText .= _('today');
	}
	$subTitle = $listTitle;
	if(!empty($list->lastUpdate)) {
		$subTitle .= '<br /><span class="copyright">' . sprintf(_('(last update on: %s)'), date(_('m/d/Y'), strtotime($list->lastUpdate))) . '</span>';
	}
	$disp->header($listTitle, $subTitle);
	$disp->assign('list', $list);
	$disp->assign('daysLeft', $daysLeft);
	$disp->assign('timeLeftText', $eventText);
	$disp->display('list_page.tpl');
	$disp->footer();
}

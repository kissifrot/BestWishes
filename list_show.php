<?php
/**
 * Show a list
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
	$disp = new BwSessionDisplay($user->getTheme()->shortName, $user);
	$user->loadParams();
	$tipsText = BwConfig::get('list_tips_text', false);
	$disp->assign('tipsText', $tipsText);
} else {
	// Nobody logged
	$sessionOk = false;
	$user = null;
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
$disp->assign('cfgMaxEdits', BwConfig::get('max_gift_name_edits', false));
// Translation strings
$disp->assignListStrings();

// Load and display the list
$subTitle = '';
$list = new BwList($slug);
if($list->load()) {
	// Remove some categories/gifts depending on the situation
	$list->filterContent($sessionOk, $user);
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
		$subTitle .= '<br /><span id="list_last_update" class="copyright">' . sprintf(_('(last update on: %s)'), date(_('m/d/Y'), strtotime($list->lastUpdate))) . '</span>';
	}
	$disp->header($listTitle, $subTitle);
	$disp->assign('list', $list);
	$disp->assign('daysLeft', $daysLeft);
	$disp->assign('timeLeftText', $eventText);
	$disp->display('list_page.tpl');
	$disp->footer();
}
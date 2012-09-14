<?php
/**
 * Show a gift
 */
define('BESTWISHES', true);

// Load common
require_once(dirname(__FILE__). DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'BwCommon.inc.php');

$autoloader = BwClassAutoloader::getInstance();

if(!isset($_GET['type']) || empty($_GET['type'])) {
	exit;
}
if(!isset($_GET['listId']) || empty($_GET['listId'])) {
	exit;
}

$type = trim($_GET['type']);
$listId = intval($_GET['listId']);

$list = new BwList();
if(!$list->loadById($listId)) {
	exit;
}

if(BwUser::checkSession()) {
	$sessionOk = true;
	$user = BwUser::getInstance();
	$disp = new BwSessionDisplay($user->getTheme()->shortName, $user);
} else {
	// Nobody logged
	$sessionOk = false;
	$disp = new BwDisplay(BwConfig::get('theme', 'default'));
}
switch($type) {
	case 'gift':
		if(!isset($_GET['id']) || empty($_GET['id'])) {
			exit;
		}
		$giftId = intval($_GET['id']);
		$gift = new BwGift($giftId);
		if(!$gift->load()) {
			$disp->showJSONData(null);
			exit;
		}
		// Cleanup some data
		if(isset($user)) {
			if($user->isListOwner($list)) {
				$gift->filterContent();
			}
		} else {
			$gift->filterContent();
		}
		$disp->showJSONData($gift);
	break;
	case 'cats':
		// Get the cats list
		if(!$sessionOk) {
			exit;
		}
		$allCats = BwCategory::getAllByListId($listId);
		foreach($allCats as $cat) {
			unset($cat->gifts);
		}
		$disp->showJSONData($allCats);
	break;
	case 'list':
		if($sessionOk) {
			$user->loadParams();
			$tipsText = BwConfig::get('list_tips_text', false);
			$disp->assign('tipsText', $tipsText);
		} else {
			$user = null;
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
		$disp->assign('list', $list);
		$disp->assign('daysLeft', $daysLeft);
		$disp->assign('timeLeftText', $eventText);
		$disp->display('list_display.tpl');
	break;
}
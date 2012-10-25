<?php
/**
 * Gifts managment
 */
define('BESTWISHES', true);

// Load common
require_once(dirname(__FILE__). DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'BwCommon.inc.php');

$autoloader = BwClassAutoloader::getInstance();

if(!isset($_GET['action']) || empty($_GET['action'])) {
	exit;
}
$action = trim($_GET['action']);

if(isset($_GET['slug']) && !empty($_GET['slug'])) {
	$slug = BwInflector::slug($_GET['slug']);
	if(isset($_GET['id']) && !empty($_GET['id'])) {
		$giftId = intval($_GET['id']);
	} else {
		exit('Gift not specified');
	}
} else {
	exit('List not specified');
}
$db = BwDatabase::getInstance();

if(BwUser::checkSession()) {
	$sessionOk = true;
	$user = BwUser::getInstance();
	$disp = new BwSessionDisplay($user->getTheme()->shortName, $user);
	$user->loadParams();
} else {
	// Nobody logged
	$sessionOk = false;
	$user = null;
	$publicLists = BwConfig::get('public_lists', 'true');
	$disp = new BwDisplay(BwConfig::get('theme', 'default'));
}

switch($action) {
	case 'show':
		// Show a gift's details
		if(!$sessionOk) {
			// Nobody logged
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
		$disp->assign('pageViewed', 'gift_display');
		// Translation strings
		$disp->assignListStrings();

		// Load and display the gift
		$gift = new BwGift($giftId);
		$list = new BwList($slug);
		if($list->lightLoad()) {
			if($gift->load()) {
				// Filter gift data if needed
				if($sessionOk) {
					if($user->isListOwner($list)) {
						$gift->filterContent();
					}
				} else {
					$gift->filterContent();
				}
				$title = _('Gift details');
				$disp->header($title);
				$disp->assign('list', $list);
				$disp->assign('gift', $gift);
				$disp->display('gift_page.tpl');
				$disp->footer();
			}
		}
	break;
	case 'markbought':
		$gift = new BwGift($giftId);
		$list = new BwList($slug);
		if(!$sessionOk) {
			$disp->header(_('Error'), _('You must be logged in to mark this gift as bought'));
			echo _('You must be logged in to mark this gift as bought');
			$disp->footer();
			exit;
		}
		if($list->lightLoad()) {
			if($gift->load()) {
				$disp->assign('pageViewed', 'gift_display');
				$title = _('Gift details');
				$disp->header($title);
				$disp->assignListStrings();
				$disp->assign('list', $list);
				$disp->assign('gift', $gift);
				$disp->display('form_purchase_gift.tpl');
				$disp->footer();
			}
		}
	break;
}
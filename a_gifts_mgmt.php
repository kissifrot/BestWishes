<?php
/**
 * Gifts managment (Ajax)
 */
define('BESTWISHES', true);

// Load config
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.inc.php');
// Load other needed files
require_once($bwLibDir . DS . 'BwCommon.inc.php');
require_once($bwLibDir . DS . 'BwClassAutoloader.class.php');

$autoloader = BwClassAutoloader::getInstance();

if(!BwUser::checkSession()) {
	exit;
}

if(!isset($_GET['action']) || empty($_GET['action'])) {
	exit;
}
if(!isset($_GET['listId']) || empty($_GET['listId'])) {
	exit;
}

$action = trim($_GET['action']);
$listId = intval($_GET['listId']);

$list = new BwList();
if(!$list->loadById($listId)) {
	exit;
}

$user = BwUser::getInstance();
$disp = new BwSessionDisplay($user->getTheme()->shortName, $user);

switch($action) {
	case 'add':
		// Adding an element
		if(!isset($_POST['type']) || empty($_POST['type'])) {
			exit;
		}
		$elementType = $_POST['type'];
		switch($elementType) {
			case 'cat':
				// Adding a category
				if(!isset($_POST['name']) || empty($_POST['name'])) {
					exit;
				}
				$catName = trim($_POST['name']);
				$result = BwCategory::add($listId, $catName);
				$disp->showJSONStatus($result, getStatusMessage($result, sprintf(_('Category %s added'), $catName), sprintf(_('Could not add category %s'), $catName)));
			break;
			case 'gift':
				// Adding a gift
				if(!isset($_POST['name']) || empty($_POST['name'])) {
					exit;
				}
				$giftName = trim($_POST['name']);
				$catId = intval($_POST['catId']);
				$force = (bool)($_POST['force']);
				$category = new BwCategory($catId);
				$statusMessages = array(
					0 => sprintf(_('Gift %s added'), $giftName),
					1 => sprintf(_('A gift named %s already exists and is indicated as bought, do you want to add it anyway?'), $giftName),
					99 => _('Internal error'),
				);
				$statusCode = 99;
				$status = 'error';
				if(!$category->load()) {
					$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
					exit;
				}
				if($category->giftListId != $listId) {
					$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
					exit;
				}
				$statusCode = BwGift::add($listId, $catId, $giftName, $force);
				if($statusCode == 0) {
					$status = 'success';
				}
				if($statusCode == 1) {
					$status = 'confirm';
				}
				$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
			break;
		}
	break;
	case 'del':
		// Deleting an element
		if($user->canDoActionForList($listId, 'edit') || $user->isListOwner($list)) {
			if(!isset($_POST['type']) || empty($_POST['type'])) {
				exit;
			}
			$elementType = $_POST['type'];
			switch($elementType) {
				case 'cat':
					// Deleting a category
					if(!isset($_POST['id']) || empty($_POST['id'])) {
						exit;
					}
					$catId = intval($_POST['id']);
					$result = BwCategory::delete($listId, $catId);
					$disp->showJSONStatus($result, getStatusMessage($result, _('Category deleted'), _('Could not delete category')));
				break;
			}
		}
	break;
}


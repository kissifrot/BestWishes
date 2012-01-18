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
				$statusMessages = array(
					0 => sprintf(_('Category %s added'), $catName),
					1 => sprintf(_('Could not add category %s'), $catName),
					99 => _('Internal error'),
				);
				$statusCode = 99;
				$status = 'error';
				$statusCode = BwCategory::add($listId, $catName);
				if($statusCode == 0) {
					$status = 'success';
				}
				$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
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
				if($category->giftListId !== $listId) {
					$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
					exit;
				}
				$statusCode = BwGift::add($listId, $catId, $giftName, $force);
				if($statusCode == 0) {
					$status = 'success';
					// Send an e-mail if configured
					$transport = BwConfig::get('mail_transport_type', 'none');
					if($transport != 'none') {
						BwMailer::sendAddAlert($user, $list, $category->name, $giftName);
					}
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
					$category = new BwCategory($catId);
					
					$statusMessages = array(
						0 => _('Category deleted'),
						1 => _('Could not delete category'),
						99 => _('Internal error'),
					);
					$statusCode = 99;
					$status = 'error';
					if(!$category->load()) {
						$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
						exit;
					}
					$statusCode = $category->delete($listId);
					if($statusCode == 0) {
						$status = 'success';
					}
					$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
				break;
				case 'gift':
					// Deleting a gift
					if(!isset($_POST['id']) || empty($_POST['id'])) {
						exit;
					}
					if(!isset($_POST['catId']) || empty($_POST['catId'])) {
						exit;
					}
					$catId = intval($_POST['catId']);
					$giftId = intval($_POST['id']);
					$gift = new BwGift($giftId);
					$category = new BwCategory($catId);
					
					$statusMessages = array(
						0 => _('Gift deleted'),
						1 => _('Could not delete gift'),
						99 => _('Internal error'),
					);
					$statusCode = 99;
					$status = 'error';
					if(!$category->load() || !$gift->load()) {
						$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
						exit;
					}
					if($category->giftListId !== $listId) {
						$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
						exit;
					}
					$statusCode = $gift->delete($listId);
					if($statusCode == 0) {
						$status = 'success';
					}
					$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
				break;
			}
		}
	break;
	case 'mark_bought':
		// Mark a gift as bought
		if(!isset($_POST['id']) || empty($_POST['id'])) {
			exit;
		}if(!isset($_POST['catId']) || empty($_POST['catId'])) {
			exit;
		}
		$giftId = intval($_POST['id']);
		$catId = intval($_POST['catId']);
		$purchaseComment = trim($_POST['purchaseComment']);
		$gift = new BwGift($giftId);
		$category = new BwCategory($catId);
		$statusMessages = array(
			0 => _('Gift marked as bought'),
			1 => _('Could not mark the gift as bought'),
			2 => _('Gift is already marked as bought'),
			99 => _('Internal error'),
		);
		$statusCode = 99;
		$status = 'error';
		if(!$user->canMarkGiftsForList($listId)) {
			$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
			exit;
		}
		if(!$category->load() || !$gift->load()) {
			$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
			exit;
		}
		if($category->giftListId !== $listId) {
			$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
			exit;
		}
		$statusCode = $gift->markAsBought($listId, $user->getId(), $purchaseComment);
		if($statusCode == 0) {
			$status = 'success';
			// Send an e-mail if configured
			$transport = BwConfig::get('mail_transport_type', 'none');
			if($transport != 'none') {
				BwMailer::sendPurchaseAlert($user, $list, $gift->name, $purchaseComment);
			}
		}
		$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
	break;

	case 'mark_received':
		// Mark a gift as received
		if(!isset($_POST['id']) || empty($_POST['id'])) {
			exit;
		}if(!isset($_POST['catId']) || empty($_POST['catId'])) {
			exit;
		}
		$giftId = intval($_POST['id']);
		$catId = intval($_POST['catId']);
		$gift = new BwGift($giftId);
		$category = new BwCategory($catId);
		$statusMessages = array(
			0 => _('Gift marked as received'),
			1 => _('Could not mark the gift as received'),
			2 => _('Gift is already marked as received'),
			99 => _('Internal error'),
		);
		$statusCode = 99;
		$status = 'error';
		if(!$user->isListOwner($list)) {
			$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
			exit;
		}
		if(!$category->load() || !$gift->load()) {
			$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
			exit;
		}
		if($category->giftListId !== $listId) {
			$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
			exit;
		}
		$statusCode = $gift->markAsReceived($listId);
		if($statusCode == 0) {
			$status = 'success';
		}
		$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
	break;
}


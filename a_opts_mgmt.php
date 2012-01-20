<?php
/**
 * Gifts managment (Ajax)
 */
define('BESTWISHES', true);

// Load common
require_once(dirname(__FILE__). DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'BwCommon.inc.php');

$autoloader = BwClassAutoloader::getInstance();

if(!BwUser::checkSession()) {
	exit;
}

if(!isset($_GET['action']) || empty($_GET['action'])) {
	exit;
}
$action = trim($_GET['action']);


$user = BwUser::getInstance();
$disp = new BwSessionDisplay($user->getTheme()->shortName, $user);

switch($action) {
	case 'editpwd':
		// Editing the password
		$statusMessages = array(
			0 => _('Password updated'),
			1 => sprintf(_('Password given is too short (should be %d characters long)'), BwConfig::get('min_password_size', 6)),
			2 => _('Password given does not match with the one in the database'),
			3 => _('Missing mandatory field'),
			4 => _('You must repeat the same password'),
			99 => _('Internal error'),
		);
		$statusCode = 99;
		$status = 'error';
		if(!isset($_POST['currentPasswd']) || empty($_POST['currentPasswd']) || !isset($_POST['newPasswd']) || empty($_POST['newPasswd']) || !isset($_POST['newPasswdRepeat']) || empty($_POST['newPasswdRepeat'])) {
			$statusCode = 3;
			$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
			exit;
		}
		$currentPasswd = trim($_POST['currentPasswd']);
		$newPasswd = trim($_POST['newPasswd']);
		$newPasswdRepeat = trim($_POST['newPasswdRepeat']);
		if($newPasswd != $newPasswdRepeat) {
			$statusCode = 4;
			$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
			exit;
		}
		$statusCode = $user->updatePassword($currentPasswd, $newPasswd);
		if($statusCode == 0) {
			$status = 'success';
		}
		$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
		//var_dump(BwDebug::getInstance());
	break;
	case 'editrights':
		// Editing a list's rights
		if(!isset($_GET['listId']) || empty($_GET['listId'])) {
			exit;
		}
		if(!isset($_POST['rtype']) || empty($_POST['rtype'])) {
			exit;
		}
		$listId = intval($_GET['listId']);
		$rightType = trim($_POST['rtype']);
		$enabled = (bool)($_POST['enabled']);

		$list = new BwList();
		if(!$list->loadById($listId)) {
			exit;
		}
		$statusMessages = array(
			0 => _('Alert updated successfully'),
			1 => _('Could not update this alert'),
			99 => _('Internal error'),
		);
		$statusCode = 99;
		$status = 'error';
		switch($rightType) {
			case 'alert_addition':
			case 'alert_purchase':
				$statusCode = $user->updateRight($listId, $rightType, $enabled);
			break;
			default:
			exit;
		}
		if($statusCode == 0) {
			$status = 'success';
		}
		$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
	break;
}


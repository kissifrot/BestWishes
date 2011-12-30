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
		$listId = intval($_GET['listId']);

		$list = new BwList();
		if(!$list->loadById($listId)) {
			exit;
		}
		// TODO
	break;
}


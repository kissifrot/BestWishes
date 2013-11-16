<?php
/**
 * Users managment (Ajax)
 */
define('BESTWISHES', true);

// Load common
require_once(dirname(__FILE__). DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'BwCommon.inc.php');

$autoloader = BwClassAutoloader::getInstance();

if(!isset($_GET['action']) || empty($_GET['action'])) {
	exit;
}

$action = trim($_GET['action']);

if(BwUser::checkSession()) {
	$sessionOk = true;
	$user = BwUser::getInstance();
	$disp = new BwSessionDisplay($user->getTheme()->shortName, $user);
	$user->loadParams();
} else {
	// Nobody logged
	$sessionOk = false;
	$user = null;
	$disp = new BwDisplay(BwConfig::get('theme', 'default'));
}

switch($action) {
	case 'resetpwd':
		// Password reset
		if($sessionOk) {
			// The user shouldn't be logged in
			exit;
		}
		if(!isset($_POST['uname']) || empty($_POST['uname'])) {
			exit;
		}
		$username = preg_replace('#[^a-z0-9_]#i', '', $_POST['uname']);
		$userToWorkOn = new BwUser();
		$statusMessages = array(
			0 => _('An e-mail containing instructions was sent'),
			1 => sprintf(_('The user "%s" doesn\'t exist'), htmlentities($username)),
			2 => sprintf(_('The user "%s" doesn\'t have an e-mail address'), htmlentities($username)),
			3 => sprintf(_('Could not send an e-mail to "%s"'), htmlentities($username)),
			99 => _('Internal error')
		);
		$statusCode = 99;
		$status = 'error';
		if(!$userToWorkOn->loadByUsername($username)) {
			$statusCode = 1;
			$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
			exit;
		}
		if(empty($userToWorkOn->email)) {
			$statusCode = 2;
			$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
			exit;
		}
		$statusCode = $userToWorkOn->sendPasswordReset();
		if($statusCode == 0) {
			$status = 'success';
		}
		$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
	break;
}


<?php
/**
 * Users management page
 */

define('BESTWISHES', true);

// Load common
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'BwCommon.inc.php');

if(!BwAdminUser::checkSession()) {
	exit;
}

$disp = new BwAdminDisplay(BwConfig::get('default_theme', 'default'));

if(!isset($_GET['action']) || empty($_GET['action'])) {
	exit;
}
$action = $_GET['action'];

switch($action) {
	case 'del':
		/*
		$statusMessages = array(
			0 => _('User deleted succesfully'),
			1 => _('Could not delete user'),
			99 => _('Internal error')
		);
		$statusCode = 99;
		$status = 'error';
		if(!isset($_POST['userId']) || empty($_POST['userId'])) {
			$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
			exit;
		}
		$userId = intval($_POST['userId']);
		$userToDelete = new BwUser();
		if(!$userToDelete->load($userId)) {
			$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
			exit;
		}
		$statusCode = $userToDelete->delete();
		if($statusCode == 0) {
			$status = 'success';
		}
		$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
		*/
	break;
	case 'add':
		$statusMessages = array(
			0 => _('User created succesfully'),
			1 => _('The given e-mail address is invalid'),
			2 => _('An user of the same name already exist'),
			99 => _('Internal error')
		);
		$statusCode = 99;
		$status = 'error';
		if(!isset($_POST['username']) || empty($_POST['username']) || !isset($_POST['name']) || empty($_POST['name']) || !isset($_POST['pwd']) || empty($_POST['pwd'])) {
			$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
			exit;
		}
		$username = trim($_POST['username']);
		$name = trim($_POST['name']);
		$pwd = trim($_POST['pwd']);
		$email = (isset($_POST['email']) && !empty($_POST['email'])) ? trim($_POST['email']) : '';
		
		$statusCode = BwUser::add($username, $pwd, $name, $email);
		if($statusCode == 0) {
			$status = 'success';
		}
		$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
	break;
}

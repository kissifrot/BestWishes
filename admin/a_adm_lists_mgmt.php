<?php
/**
 * Lists management page
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
	case 'list':
		$disp->display('lists_list.tpl');
	break;
	case 'edit':
		$statusMessages = array(
			0 => _('List updated succesfully'),
			1 => _('Could not update list'),
			2 => _('A list of the same name already exist'),
			99 => _('Internal error')
		);
		$statusCode = 99;
		$status = 'error';
		if(!isset($_POST['listId']) || empty($_POST['listId'])) {
			$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
			exit;
		}
		$listId = intval($_POST['listId']);
		$listToEdit = new BwList();
		if(!$listToEdit->loadById($listId)) {
			$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
			exit;
		}
		$newName = (isset($_POST['newName']) && !empty($_POST['newName'])) ? $_POST['newName'] : '';
		$statusCode = $listToEdit->save(array('name' => $newName));
		if($statusCode == 0) {
			$status = 'success';
		}
		$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
	break;
	case 'del':
		$statusMessages = array(
			0 => _('List deleted succesfully'),
			1 => _('Could not delete list'),
			99 => _('Internal error')
		);
		$statusCode = 99;
		$status = 'error';
		if(!isset($_POST['listId']) || empty($_POST['listId'])) {
			$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
			exit;
		}
		$listId = intval($_POST['listId']);
		$listToDelete = new BwList();
		if(!$listToDelete->loadById($listId)) {
			$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
			exit;
		}
		$statusCode = $listToDelete->delete();
		if($statusCode == 0) {
			$status = 'success';
		}
		$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
	break;
	case 'editrights':
		// Editing a list's rights
		if(!isset($_GET['listId']) || empty($_GET['listId'])) {
			exit;
		}
		if(!isset($_GET['userId']) || empty($_GET['userId'])) {
			exit;
		}
		if(!isset($_POST['rtype']) || empty($_POST['rtype'])) {
			exit;
		}
		$listId = intval($_GET['listId']);
		$userId = intval($_GET['userId']);
		$rightType = trim($_POST['rtype']);
		$enabled = (bool)($_POST['enabled']);

		$list = new BwList();
		if(!$list->loadById($listId)) {
			exit;
		}
		$user = new BwUser();
		if(!$user->load($userId)) {
			exit;
		}
		$statusMessages = array(
			0 => _('Right updated successfully'),
			1 => _('Could not update this right'),
			99 => _('Internal error'),
		);
		$statusCode = 99;
		$status = 'error';
		switch($rightType) {
			case 'can_view':
			case 'can_edit':
			case 'can_mark':
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
	case 'add':
		$statusMessages = array(
			0 => _('List created succesfully'),
			1 => _('The list birthdate is incorrect'),
			2 => _('A list of the same name already exist'),
			99 => _('Internal error')
		);
		$statusCode = 99;
		$status = 'error';
		if(!isset($_POST['listBirthdate']) || empty($_POST['listBirthdate']) || !isset($_POST['listName']) || empty($_POST['listName']) || !isset($_POST['listUser']) || empty($_POST['listUser'])) {
			$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
			exit;
		}
		$listUser = intval($_POST['listUser']);
		$listBirthdate = trim($_POST['listBirthdate']);
		$listName = trim($_POST['listName']);
		$user = new BwUser($listUser);
		if(!$user->load()) {
			$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
			exit;
		}
		$statusCode = BwList::add($listName, $listUser, $listBirthdate);
		if($statusCode == 0) {
			$status = 'success';
		}
		$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
	break;
}

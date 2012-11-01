<?php
/**
 * Events management page
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
		$allEvents = BwEvent::getAllEvents();
		$disp->assign('allEvents', $allEvents);
		$disp->display('events_list.tpl');
	break;
	case 'edit':
		$statusMessages = array(
			0 => _('Event updated succesfully'),
			1 => _('Could not update event'),
			2 => _('An event of the same name already exist'),
			99 => _('Internal error')
		);
		$statusCode = 99;
		$status = 'error';
		if(!isset($_POST['eventId']) || empty($_POST['eventId'])) {
			$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
			exit;
		}
		$eventId = intval($_POST['eventId']);
		$eventToEdit = new BwEvent();
		if(!$eventToEdit->load($eventId)) {
			$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
			exit;
		}
		$newName = (isset($_POST['newName']) && !empty($_POST['newName'])) ? $_POST['newName'] : '';
		$statusCode = $eventToEdit->save(array('name' => $newName));
		if($statusCode == 0) {
			$status = 'success';
		}
		$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
	break;
	/*
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
	*/
	case 'add':
		$statusMessages = array(
			0 => _('Event created succesfully'),
			1 => _('The event date is incorrect'),
			2 => _('An event of the same name already exist'),
			99 => _('Internal error')
		);
		$statusCode = 99;
		$status = 'error';
		if(!isset($_POST['eventName']) || empty($_POST['eventName'])) {
			$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
			exit;
		}
		$eventName = trim($_POST['eventName']);
		$eventType = 'standard';
		$eventPerm = $_POST['eventPerm'];
		$eventDay = $_POST['eventDay'];
		$eventMonth = $_POST['eventMonth'];
		$eventYear = $_POST['eventYear'];
		if(empty($eventDay) && empty($eventMonth) && empty($eventYear)) {
			$statusCode = 1;
			$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
			exit;
		}
		$statusCode = BwEvent::add($eventName, $eventType, $eventPerm, $eventDay, $eventMonth, $eventYear);
		if($statusCode == 0) {
			$status = 'success';
		}
		$disp->showJSONStatus($status, getStatusMessage($statusCode, $statusMessages));
	break;
}

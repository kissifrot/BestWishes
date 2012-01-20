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
		$disp->assign('status', 'error');
		$disp->assign('statusMessage', _('Undefined listId'));
		if(isset($_POST['listId']) && !empty($_POST['listId'])) {
			$listId = intval($_POST['listId']);
			$listToEdit = new BwList();
			if(!$listToEdit->loadById($listId)) {
				$disp->assign('statusMessage', _('The list doesn\'t exist'));
			} else {
				$newName = (isset($_POST['newName']) && !empty($_POST['newName'])) ? $_POST['newName'] : '';
				if(empty($newName)) {
					$disp->assign('statusMessage', _('No name specified'));
				} else {
					$newSlug = BwInflector::slug($newName);
					// Check for already existing list
					if($listToEdit->checkExisting('name', $newName)) {
						$disp->assign('statusMessage', _('A list of the same name already exist'));
					} else {
						if($listToEdit->save(array('name' => $newName, 'slug' => $newSlug))) {
							// Clear cache
							$listToEdit->deleteFromCache();
							// Success
							$disp->assign('status', 'success');
							$disp->assign('statusMessage', _('List updated'));
						} else {
							$disp->assign('statusMessage', _('Unable to update list'));
						}
					}
				}
			}
		}
		$disp->display('json_message.tpl');
	break;
	case 'delete':
		$disp->assign('status', 'error');
		$disp->assign('statusMessage', _('Undefined listId'));
		if(isset($_GET['listId']) && !empty($_GET['listId'])) {
			$listId = intval($_GET['listId']);
			$listToDelete = new BwList();
			if(!$listTodelete->loadById($listId)) {
				$disp->assign('statusMessage', _('The list doesn\'t exist'));
			} else {
				$disp->assign('statusMessage', _('The list doesn\'t exist'));
				// Clear cache
				$listToEdit->deleteFromCache();
			}
		}
		$disp->display('json_message.tpl');
	break;
	case 'add':
		$disp->assign('status', 'error');
		$disp->assign('statusMessage', _('Could not create the list'));
		$disp->display('json_message.tpl');
	break;
}

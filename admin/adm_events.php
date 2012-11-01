<?php
/**
 * Events management page
 */

define('BESTWISHES', true);

// Load common
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'BwCommon.inc.php');


// $admUser = new BwAdminUser();
// $password = 'v8aLTIZd';
// var_dump($admUser->login('Kissifrot', $password));

if(!BwAdminUser::checkSession()) {
	header('Location: login.php');
	exit;
}

$disp = new BwAdminDisplay(BwConfig::get('default_theme', 'default'));
$disp->header(_('Events management'));
$disp->assignListStrings();
$allEvents = BwEvent::getAllEvents();
$disp->assign('allEvents', $allEvents);
$disp->display('events_mgmt.tpl');
$disp->footer();

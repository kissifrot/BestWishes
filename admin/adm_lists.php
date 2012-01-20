<?php
/**
 * Lists management page
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
$disp->header(_('Lists magnagement'));
$allUsers = BwUser::getAll();
$disp->assign('users', $allUsers);
$disp->display('lists_mgmt.tpl');
$disp->footer();

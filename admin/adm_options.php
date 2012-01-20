<?php
/**
 * Options management page
 */

define('BESTWISHES', true);

// Load common
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'BwCommon.inc.php');

if(!BwAdminUser::checkSession()) {
	header('Location: login.php');
	exit;
}

$disp = new BwAdminDisplay(BwConfig::get('default_theme', 'default'));
$disp->header(_('Options magnagement'));
$disp->display('options_mgmt.tpl');
$disp->footer();

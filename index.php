<?php
/**
 * Front page
 */

define('BESTWISHES', true);

// Load common
require_once(dirname(__FILE__). DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'BwCommon.inc.php');

if(BwUser::checkSession()) {
	$user = BwUser::getInstance();
	$disp = new BwSessionDisplay($user->getTheme()->shortName, $user);
} else {
	$disp = new BwDisplay(BwConfig::get('theme', 'default'));
}
// Translation strings
$disp->assign('lngPasswordForgot', _('Forgot your password?'));
$disp->assign('lngLoginLabel', _('Login:'));
$disp->assign('lngLoginAction', _('Login'));
$disp->assign('lngPasswordLabel', _('Password:'));

$disp->header(_('Home'));
$disp->display('home.tpl');
$disp->footer();

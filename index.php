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
	// Adjust the welcome message
	$welcomeMsg = sprintf(_('Welcome <b>%s</b>'), $user->name);
	if(!empty($_SESSION['last_login'])) {
		$welcomeMsg .= sprintf(_(', your last visit was on %s'), date(_('m/d/Y'), strtotime($_SESSION['last_login'])));
	}
	$disp->assign('lngUserWelcomeMessage', $welcomeMsg);
} else {
	$disp = new BwDisplay(BwConfig::get('theme', 'default'));
}
$disp->assign('pageViewed', 'home');
// Translation strings
$disp->assign('lngHomeInstructions', _('Choose a list to show in the left menu'));
$disp->assign('lngPasswordForgot', _('Forgot your password?'));
$disp->assign('lngToLogin', _('To log in:'));
$disp->assign('lngLoginLabel', _('Login:'));
$disp->assign('lngLoginAction', _('Login'));
$disp->assign('lngPasswordLabel', _('Password:'));

$disp->header(_('Home'));
$disp->display('home.tpl');
$disp->footer();

<?php
/**
 * Password reset page
 */

define('BESTWISHES', true);

// Load common
require_once(dirname(__FILE__). DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'BwCommon.inc.php');

if(BwUser::checkSession()) {
	$user = BwUser::getInstance();
	$disp = new BwSessionDisplay($user->getTheme()->shortName, $user);
} else {
	$user = null;
	$disp = new BwDisplay(BwConfig::get('theme', 'default'));
}

$disp->header(_('Password reset'));
// Translation strings
$disp->assign('lngPasswordReset', _('Password reset'));

$disp->assign('error', true);
if(isset($_GET['token']) && !empty($_GET['token'])) {
	$passwordToken = $_GET['token'];
	$userToWorkOn = new BwUser();
	if(!$userToWorkOn->loadByResetToken($passwordToken)) {
		$disp->assign('message', _('Either the token doesn\'t exist or you already reset your password'));
	} else {
		if(!$userToWorkOn->sendNewPassword()) {
			$disp->assign('message', _('Couldn\'t send a new password'));
		} else {
			$disp->assign('error', false);
			$disp->assign('message', _('An e-mail was sent with a new password, please check your e-mail inbox'));
		}
	}
} else {
	$disp->assign('message', _('No token specified'));
}
$disp->display('pwd_reset.tpl');
$disp->footer();
// $disp->assign('pageViewed', 'home');
// Translation strings

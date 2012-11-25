<?php
/**
 * Login page
 */

define('BESTWISHES', true);

// Load common
require_once(dirname(__FILE__). DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'BwCommon.inc.php');

$errorMessage = false;
if(isset($_POST['username']) && !empty($_POST['username']) && isset($_POST['pass']) && !empty($_POST['pass'])) {
	$username = $_POST['username'];
	$password = $_POST['pass'];
	$remember = isset($_POST['rememberme']) ? true : false;
	$user = new BwUser();
	// var_dump($user->login($username, $password));
	if($user->login($username, $password, $remember)) {
		$user->updateLastLogin();
		header('Location: index.php');
		exit;
	} else {
		BwUser::clearAutoLogin();
		$errorMessage = _('Username or password incorrect');
	}
}

$disp = new BwDisplay(BwConfig::get('theme', 'default'));
$disp->assign('message', $errorMessage);
// Translation strings
$disp->assign('lngPasswordForgot', _('Forgot your password?'));
$disp->assign('lngToLogin', _('To log in:'));
$disp->assign('lngLoginLabel', _('Login:'));
$disp->assign('lngLoginAction', _('Login'));
$disp->assign('lngPasswordLabel', _('Password:'));
$disp->assign('lngUsernameIncorrect', _('You must enter a valid username'));
$disp->assign('lngRememberMe', _('Remember me'));

$disp->header(_('Login'));
$disp->display('login.tpl');
$disp->footer();

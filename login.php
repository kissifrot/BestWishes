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
	$user = new BwUser();
	// var_dump($user->login($username, $password));
	if($user->login($username, $password)) {
		$user->updateLastLogin();
		header('Location: index.php');
		exit;
	} else {
		$errorMessage = _('Username or password incorrect');
	}
}

$disp = new BwDisplay(BwConfig::get('theme', 'default'));
$disp->assign('message', $errorMessage);
// Translation strings
$disp->assign('lngPasswordForgot', _('Forgot your password?'));
$disp->assign('lngLoginLabel', _('Login:'));
$disp->assign('lngLoginAction', _('Login'));
$disp->assign('lngPasswordLabel', _('Password:'));

$disp->header(_('Login'));
$disp->display('login.tpl');
$disp->footer();

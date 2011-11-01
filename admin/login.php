<?php
/**
 * Admin login page
 */

define('BESTWISHES', true);

// Load config
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config.inc.php');
// Load other needed files
require_once($bwLibDir . DS . 'BwCommon.inc.php');

$errorMessage = false;
var_dump($_POST);
if(isset($_POST['adm_username']) && !empty($_POST['adm_username']) && isset($_POST['adm_pass']) && !empty($_POST['adm_pass'])) {
	$username = $_POST['adm_username'];
	$password = $_POST['adm_pass'];
	$admUser = new BwAdminUser();
	var_dump($admUser->login($username, $password));
	if($admUser->login($username, $password)) {
		header('Location: index.php');
		exit;
	} else {
		$errorMessage = _('Username or password incorrect');
	}
}

$disp = new BwAdminDisplay(BwConfig::get('default_theme', 'default'));
$disp->assign('sessionOk', false);
$disp->assign('message', $errorMessage);
// Translation strings
$disp->assign('lngPasswordForgot', _('Forgot your password?'));
$disp->assign('lngLoginLabel', _('Login:'));
$disp->assign('lngLoginAction', _('Login'));
$disp->assign('lngPasswordLabel', _('Password:'));

$disp->header(_('Login'));
$disp->display('login.tpl');
$disp->footer();

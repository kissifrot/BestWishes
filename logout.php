<?php
/**
 * Logout page
 */

define('BESTWISHES', true);

// Load common
require_once(dirname(__FILE__). DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'BwCommon.inc.php');

if(!BwUser::checkSession()) {
	header('Location: index.php');
	exit;
} else {
	$user = BwUser::getInstance();
	$user->logout();
	unset($user);
	header('Location: index.php');
	exit;
}

<?php
/**
 * Logout page
 */

define('BESTWISHES', true);

// Load config
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.inc.php');
// Load other needed files
require_once($bwLibDir . DS . 'BwCommon.inc.php');

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

<?php
/**
 * Options page
 */
define('BESTWISHES', true);

// Load common
require_once(dirname(__FILE__). DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'BwCommon.inc.php');

$autoloader = BwClassAutoloader::getInstance();

if(!BwUser::checkSession()) {
	header('Location: index.php');
	exit;
}

$db = BwDatabase::getInstance();

$user = BwUser::getInstance();
$disp = new BwSessionDisplay($user->getTheme()->shortName, $user);
$user->loadParams();
// Translation strings
$disp->assignOptionsStrings();

$disp->header(_('Options management'));
$disp->assign('lists', BwList::getAll());
$disp->assign('user', $user);
$disp->assign('themes', BwTheme::getAll());
$disp->display('options_page.tpl');
$disp->footer();

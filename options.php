<?php
/**
 * options page
 */
define('BESTWISHES', true);

// Load config
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.inc.php');
// Load other needed files
require_once($bwLibDir . DS . 'BwCommon.inc.php');
require_once($bwLibDir . DS . 'BwClassAutoloader.class.php');

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
$disp->display('options_page.tpl');
$disp->footer();

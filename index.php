<?php
/**
 * Front page
 */
if (version_compare(PHP_VERSION, '5.2.0', '<')) exit("Sorry, BestWishes will only run on PHP version 5.2.0 or greater!\n");

define('BESTWISHES', true);

// Load config
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.inc.php');
// Load other needed files
require_once($bwLibDir . DS . 'BwCommon.inc.php');

if(BwUser::checkSession()) {
	
} else {
	$display = new BwDisplay(BwConfig::get('theme', 'default'));
}
$display->header(__('Home'));
$display->footer();

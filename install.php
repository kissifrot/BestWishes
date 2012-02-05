<?php
if (version_compare(PHP_VERSION, '5.2.0', '<')) exit("Sorry, BestWishes will only run on PHP version 5.2.0 or greater!\n");

define('BESTWISHES', true);
define('INSTALL_MODE', true);

$availableDatabasesInfo = array(
	'mysql' => array(
		'name' => 'MySQL',
		'default_user' => 'root',
		'default_password' => '',
		'default_host' => 'localhost',
		'default_port' => '3306',
	),
	'postgresql' => array(
		'name' => 'PostgreSQL',
		'default_user' => 'root',
		'default_password' => '',
		'default_host' => 'localhost',
		'default_port' => '5432',
	)
);

// This is what we are.
$installurl = $_SERVER['PHP_SELF'];

require_once(dirname(__FILE__). DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'BwCommon.inc.php');

$autoloader = BwClassAutoloader::getInstance();

$lang = new BwLang('fr');
var_dump($lang);
var_dump($lang->getLangList());
var_dump($lang->setupLocale());

if (! function_exists('__')) {
    exit('Bad invocation!');
}

// Steps
$installSteps = array(
	0 => array(1, _('Welcome'), 'Welcome', 0),
	1 => array(2, _('Writable Check'), 'CheckFilesWritable', 10),
	2 => array(3, _('Database Settings'), 'DatabaseSettings', 15),
	4 => array(5, _('Database Population'), 'DatabasePopulation', 15),
	5 => array(6, _('Admin Account'), 'AdminAccount', 20),
	6 => array(7, _('Finalize Install'), 'DeleteInstall', 0),
);

var_dump($installSteps);

$currentStep = isset($_GET['step']) ? (int) $_GET['step'] : 0;

var_dump($currentStep);



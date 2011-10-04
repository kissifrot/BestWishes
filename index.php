<?php
if (version_compare(PHP_VERSION, '5.2.0', '<')) exit("Sorry, BewtWishes will only run on PHP version 5.2.x or greater!\n");

define('BESTWISHES', true);

// Load config
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.inc.php');

require_once($bwLibDir . DIRECTORY_SEPARATOR . 'BwClassAutoloader.class.php');

$autoloader = BwClassAutoloader::getInstance();

$db = Database::getInstance();
var_dump($db);
$lang = new Lang();
var_dump($lang);
var_dump($lang->getLangList());

$locale = 'fr';
// gettext setup
T_setlocale(LC_MESSAGES, $locale);
// Set the text domain as 'messages'
$domain = 'bestwishes';
T_bindtextdomain($domain, $bwLocaleDir);
T_bind_textdomain_codeset($domain, 'UTF-8');
T_textdomain($domain);

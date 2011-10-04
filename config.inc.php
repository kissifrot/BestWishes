<?php
if (! defined('BESTWISHES')) {
	exit;
}

/* BESTWISHES INFO */
$bwName = 'BestWishes';
$bwLang = 'en';
$bwURL  = 'http://bestwishes.localhost';

/* DATABASE INFO */
$confDBType    = 'mysql';
$confDBServer  = 'localhost';
$confDBName    = 'bestwishes';
$confDBPrefix  = 'bw_';
$confDBUser    = 'root';
$confDBPasswd  = '';
$confDBPort    = '3306';

/* DIRECTORIES INFO */
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
$bwMainDir   = dirname(__FILE__);
$bwLibDir    = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lib';
$bwVendorDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lib'  . DIRECTORY_SEPARATOR . 'vendor';
$bwCacheDir  = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cache';
$bwLocaleDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'locale';

/* INSTALL CHECK */
// if (file_exists(dirname(__FILE__) . '/install.php'))
// {
	// header('Location: http' . (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 's' : '') . '://' . (empty($_SERVER['HTTP_HOST']) ? $_SERVER['SERVER_NAME'] . (empty($_SERVER['SERVER_PORT']) || $_SERVER['SERVER_PORT'] == '80' ? '' : ':' . $_SERVER['SERVER_PORT']) : $_SERVER['HTTP_HOST']) . (strtr(dirname($_SERVER['PHP_SELF']), '\\', '/') == '/' ? '' : strtr(dirname($_SERVER['PHP_SELF']), '\\', '/')) . '/install.php'); exit;
// }

?>
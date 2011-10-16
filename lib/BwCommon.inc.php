<?php
/**
* Common
*/
if (! defined('BESTWISHES')) {
	exit;
}

session_start();

require_once($bwLibDir . DS . 'BwClassAutoloader.class.php');

$autoloader = BwClassAutoloader::getInstance();
BwLang::load();

function cleanupVariable($type = 'theme', $variable = '') {
	$variableCleaned = '';
	switch($type) {
		case 'theme':
			// Theme name
			$variableCleaned = preg_replace('#[^a-z0-9_-]#i', '', $variable);
		break;
		case 'default':
			$variableCleaned = preg_replace('#[^a-z0-9_-]#i', '_', $variable);
			$variableCleaned = strtolower($variableCleaned);
			$variableCleaned = preg_replace('#(_)+#i', '_', $variableCleaned);
		break;
	}

	return $variableCleaned;
}
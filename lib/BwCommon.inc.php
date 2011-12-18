<?php
/**
* Common
*/
if (! defined('BESTWISHES')) {
	exit;
}

session_start();

// Guess the current URL if not set
if(empty($bwURL)) {
	$bwURL = 'http' . (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 's' : '') . '://' . (empty($_SERVER['HTTP_HOST']) ? $_SERVER['SERVER_NAME'] . (empty($_SERVER['SERVER_PORT']) || $_SERVER['SERVER_PORT'] == '80' ? '' : ':' . $_SERVER['SERVER_PORT']) : $_SERVER['HTTP_HOST']) . (strtr(dirname($_SERVER['PHP_SELF']), '\\', '/') == '/' ? '' : strtr(dirname($_SERVER['PHP_SELF']), '\\', '/'));
	$bwURL = preg_replace('#/admin$#i', '', $bwURL);
}

require_once($bwLibDir . DS . 'BwClassAutoloader.class.php');

$autoloader = BwClassAutoloader::getInstance();
BwDebug::setDebugMode(BwDebug::LOG_ALL); // TODO: remove this
BwLang::load();

BwCache::configure('default', array(
	'engine' => 'File',
	'duration'=> 3600
));

/**
 * Cleans up a variable depending on its type
 */
function cleanupVariable($type = 'theme', $variable = '') {
	$variableCleaned = '';
	switch($type) {
		case 'theme':
			// Theme name
			$variableCleaned = preg_replace('#[^a-z0-9_-]#i', '', $variable);
		break;
		case 'classname':
			$variableCleaned = preg_replace('#[^A-za-z0-9]#i', '', $variable);
		break;
		case 'default':
			$variableCleaned = preg_replace('#[^a-z0-9_-]#i', '_', $variable);
			$variableCleaned = strtolower($variableCleaned);
			$variableCleaned = preg_replace('#(_)+#i', '_', $variableCleaned);
		break;
	}

	return $variableCleaned;
}

/**
 * Little utility fonction to use in usort()
 */
function datesCompare($a, $b)
{
	if ($a['time'] == $b['time']) {
		return 0;
	}
	return ($a['time'] > $b['time']) ? -1 : 1;
}

function boolString($bValue = false) {
	return ($bValue ? 'true' : 'false');
}

/** 
 * Borrowed from http://php.net/manual/en/function.is-bool.php#88635
 * Checks a variable to see if it should be considered a boolean true or false.
 *     Also takes into account some text-based representations of true of false,
 *     such as 'false','N','yes','on','off', etc.
 * @author Samuel Levy <sam+nospam@samuellevy.com>
 * @param mixed $in The variable to check
 * @param bool $strict If set to false, consider everything that is not false to
 *                     be true.
 * @return bool The boolean equivalent or null
 */
function boolVal($in, $strict = false) {
	$out = null;
	// if not strict, we only have to check if something is false
	if (in_array($in,array('false', 'False', 'FALSE', 'no', 'No', 'n', 'N', '0', 'off',
						'Off', 'OFF', false, 0, null), true)) {
		$out = false;
	} else if ($strict) {
		// if strict, check the equivalent true values
		if (in_array($in,array('true', 'True', 'TRUE', 'yes', 'Yes', 'y', 'Y', '1',
							'on', 'On', 'ON', true, 1), true)) {
			$out = true;
		}
	} else {
		// not strict? let the regular php bool check figure it out (will
		//     largely default to true)
		$out = ($in?true:false);
	}
    return $out;
}

/**
 * Returns a message depending on the status
 */
function getStatusMessage($success = false, $successMessage = '', $errorMessage = '') {
	if($success) {
		return $successMessage;
	} else {
		return $errorMessage;
	}
}

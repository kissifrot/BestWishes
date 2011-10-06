<?php

if (file_exists(dirname(dirname(__FILE__))  . DIRECTORY_SEPARATOR . 'config.inc.php'))
{
	require(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'config.inc.php');
	header('Location: ' . $bwURL);
	exit;
}
else
	exit;

?>

<?php
if (version_compare(PHP_VERSION, '5.2.0', '<')) exit("Sorry, BestWishes will only run on PHP version 5.2.x or greater!\n");

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

#!/usr/bin/php

<?php
// Autoload function
function autoload($class_name)
{
	// Get path
	$path = 'src/'.str_replace('\\', '/', $class_name).'.php';

	// Check file exists
	if (! is_readable($path))
	{
		throw new Exception('The class file for "'.$path.'" does not exist.');
	}

	// Include class
	require_once $path;
}

// Register autoload function
spl_autoload_register('autoload');

// Run application class
$application = new Backgammon\Core\Application;
$application->main();
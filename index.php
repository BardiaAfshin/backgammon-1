#!/usr/bin/php

<?php
// Register autoload function
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

spl_autoload_register('autoload');

// Build and run application
(new Backgammon\Core\Factory)->buildApplication()->execute();
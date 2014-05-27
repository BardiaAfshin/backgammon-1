#!/usr/bin/php

<?php
// Autoload function
function autoload($class_name)
{
	// Get path
    $path = 'classes/'.str_replace('\\', '/', $class_name).'.php';

	// Check file exists
	if (! is_readable($path))
	{
		throw new Exception('The class file for "'.$path.'" does not exist.');
	}

	// Include class
	require_once $path;
}
spl_autoload_register('autoload');

// Load factory
$factory = new Backgammon\Factory;

// Build IO
$io = $factory->buildIO();

// Build checkers
$black_checker = $factory->buildBlackChecker();
$white_checker = $factory->buildWhiteChecker();

// Build computer player
$computer = $factory->buildComputerPlayer($white_checker, true);

// Build human player
//$name = ucwords($io->input('What is your name?'));
$name = 'Lenton';
$human = $factory->buildHumanPlayer($black_checker, false, $name);

// Get first turn
/*while (true)
{
	$first_turn = (int) $io->input('Which player is to go first (1 or 2)?');
	if ($first_turn === 1 || $first_turn === 2) { break; }
	$io->error('Invalid input; you must enter 1 or 2.');
}*/
$first_turn = 2;

// Build backgammon game
$game = $factory->buildGame($computer, $human, $first_turn);

// Game loop
while (true)
{
	// Display board
	$this->io->output($this->board->asText());

	// Take turn
	$this->takeTurn();
	sleep(3);

	// If a player has won
	if (($winner = $this->checkWinner()) !== false)
	{
		$this->io->output('Game over.');
		break;
	}
}
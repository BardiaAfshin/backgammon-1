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
$players[1] = $factory->buildComputerPlayer($white_checker, true);

// Build human player
//$name = ucwords($io->input('What is your name?'));
$name = 'Lenton';
$players[2] = $factory->buildHumanPlayer($black_checker, false, $name);

// Get first turn
/*while (true)
{
	// Get input
	$input = (int) $io->input('Which player is to go first (1 or 2)?');
	
	// If input is valid
	if (array_key_exists($input, $players))
	{
		// Set first turn
		$first_turn = $players[$input];
		break;
	}
	
	// Invalid input
	$io->error('Invalid input; you must enter 1 or 2.');
}*/
$first_turn = $players[1];

// Build backgammon game
$game = $factory->buildGame($players[1], $players[2], $first_turn);

// Turn loop
while (true)
{
	// Display board
	$io->output($game->board->asText());

	// Get the active player
	$player = $game->active_player;
	$io->output($player->checker->symbol.' '.$player->name.'\'s Turn');
	
	// Take turn
	$game->takeTurn();
	
	// Check if player has won
	if (($winner = $game->checkWinner()) !== false)
	{
		// Game over
		$this->io->output('Game over.');
		break;
	}
}
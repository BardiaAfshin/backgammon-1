<?php
namespace Backgammon\Core;

use Backgammon\Business\Game;
use Backgammon\Business\Player;
use Backgammon\Business\Board;
use Backgammon\Business\Players\Computer;

/**
 * Main application class
 */
class Application
{
	protected $factory;
	protected $io;
	
	protected $namespace = 'Backgammon\\Business\\Players\\';
	
	public function __construct(Factory $factory, IO $io)
	{
		$this->factory = $factory;
		$this->io = $io;
	}
	
	/**
	 * Game initialisation
	 */
	public function execute()
	{
		// Build checkers
		$black_checker = $this->factory->buildBlackChecker();
		$white_checker = $this->factory->buildWhiteChecker();

		// Build human player
		//$name = $this->getName();
		$name = 'Lenton';
		$players[1] = $this->factory->buildHumanPlayer($name, $black_checker, false);
		
		// Build computer player
		$players[2] = $this->factory->buildComputerPlayer('Botster', $white_checker, true);

		// Get first turn
		//$first_turn = $this->getFirstTurn($players);
		$first_turn = $players[1];

		// Build backgammon game
		$game = $this->factory->buildGame($players[1], $players[2], $first_turn);

		// Run game loop
		$this->gameLoop($game);
	}
	
	protected function getName()
	{
		return ucwords($this->io->input('What is your name?'));
	}
	
	/**
	 * Ask which player is to go first
	 */
	protected function getFirstTurn(array $players)
	{
		while (true)
		{
			// Get input
			$input = (int) $this->io->input('Which player is to go first (1 or 2)?');

			// If input is valid
			if (array_key_exists($input, $players))
			{
				// Return the player to go first
				return $players[$input];
			}

			// Invalid input
			$this->io->error('Invalid input; you must enter 1 or 2.');
		}
	}
	
	/**
	 * The game loop
	 * 
	 * @param $game Game of backgammon
	 */
	protected function gameLoop(Game $game)
	{
		// Turn loop
		while (true)
		{
			// Display board
			$this->io->output("\n".$game->board->asText());

			// Get the active player
			$player = $game->active_player;
			$this->io->output($player->checker->symbol.' '.$player->name.'\'s Turn');
			
			// Take turn
			$this->takeTurn($game->active_player, $game->board);

			// Check if player has won
			if (($winner = $game->checkWinner()) !== false)
			{
				// End turn loop
				break;
			}
			
			// Next player
			$game->nextTurn();
		}

		// Game over
		$this->io->output('Game over.');
	}
	
	protected function takeTurn(Player $player, Board $board)
	{
		while (true)
		{
			// Get player type
			switch (get_class($player))
			{
				// If player is human
				case $this->namespace.'Human':
					$moves = $this->humanGetMoves();
					break;
				// If player is computer
				case $this->namespace.'Computer':
					$moves = $this->computergetMoves($player);
					break;
				default:
					throw new \Exception('Unsupported player type.');
			}

			try
			{
				// Make moves
				$board->makeMoves($moves, $player->checker, $player->clockwise);
			}
			catch (\Exception $e)
			{
				$this->io->error($e->getMessage());
				continue;
			}
			
			return true;
		}
	}
	
	/**
	 * Get moves of a human player
	 */
	protected function humanGetMoves()
	{
		while (true)
		{
			// Get input
			$input = $this->io->input('Moves (from-to from-to):');

			// Expload moves
			$exploded_moves = preg_split("/\s/", $input, null, PREG_SPLIT_NO_EMPTY);

			// If no moves were inputted
			if (empty($exploded_moves))
			{
				$this->io->error('You must input at least 1 move.');
				continue;
			}

			// For each inputted moves
			$moves = [];
			foreach ($exploded_moves as $move)
			{
				// Exploded move
				$exploded_move = preg_split("/-/", $move, null, PREG_SPLIT_NO_EMPTY);

				// Check that move has two values
				if (count($exploded_move) !== 2)
				{
					$this->io->error('Each move must have 2 values.');
					continue 2;
				}

				// Add move to array
				$moves[] = [
					0 => (int) $exploded_move[0],
					1 => (int) $exploded_move[1],
				];
			}
			
			return $moves;
		}
	}
	
	protected function computerGetMoves(Computer $player)
	{
		$this->io->output('SKIP');
		
		return [];
	}
}
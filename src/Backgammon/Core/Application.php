<?php
namespace Backgammon\Core;

use Backgammon\Business\Player;
use Backgammon\Business\Board;

/**
 * Main application class
 */
class Application
{
	protected $factory;
	protected $io;
	protected $game;
	
	public function main()
	{
		// Load factory
		$this->factory = new Factory;

		// Build IO
		$this->io = $this->factory->buildIO();

		// Build checkers
		$black_checker = $this->factory->buildBlackChecker();
		$white_checker = $this->factory->buildWhiteChecker();

		// Build computer player
		$players[1] = $this->factory->buildComputerPlayer('Botster', $white_checker, true);

		// Build human player
		//$name = ucwords($io->input('What is your name?'));
		$name = 'Lenton';
		$players[2] = $this->factory->buildHumanPlayer($name, $black_checker, false);

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
		$this->game = $this->factory->buildGame($players[1], $players[2], $first_turn);

		// Turn loop
		while (true)
		{
			// Display board
			$this->io->output("\n".$this->game->board->asText());

			// Get the active player
			$player = $this->game->active_player;
			$this->io->output($player->checker->symbol.' '.$player->name.'\'s Turn');
			
			// Take turn
			$this->takeTurn($this->game->active_player, $this->game->board);

			// Check if player has won
			if (($winner = $this->game->checkWinner()) !== false)
			{
				// End turn loop
				break;
			}
			
			// Next player
			$this->game->nextTurn();
		}

		// Game over
		$this->io->output('Game over.');
	}
	
	public function takeTurn(Player $player, Board $board)
	{
		$namespace = 'Backgammon\\Business\\Players\\';
		
		// Get player type
		switch (get_class($player))
		{
			// If player is human
			case $namespace.'Human':
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

					// Success
					break;
				}
				break;
			// If player is computer
			case $namespace.'Computer':
				$this->io->output('SKIP');
				break;
			default:
				throw new \Exception('Unsupported player type.');
		}
	}
}
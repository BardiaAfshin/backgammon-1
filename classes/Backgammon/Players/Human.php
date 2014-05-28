<?php
namespace Backgammon\Players;

use Backgammon\Player;
use Backgammon\IO;
use Backgammon\Checker;
use Backgammon\Board;

class Human extends Player
{
	public function __construct(IO $io, Checker $checker, $clockwise, $name)
	{
		parent::__construct($io, $checker, $clockwise);
		$this->name = $name;
	}
	
	public function takeTurn(Board $board)
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
			
			try
			{
				// Make moves
				$board->makeMoves($moves, $this->checker, $this->clockwise);
			}
			catch (\Exception $e)
			{
				$this->io->error($e->getMessage());
				continue;
			}
			
			// Success
			break;
		}
	}
}
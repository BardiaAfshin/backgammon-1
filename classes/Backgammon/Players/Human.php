<?php
namespace Backgammon\Players;

use Backgammon\Player;
use Backgammon\IO;
use Backgammon\Checker;

class Human extends Player
{
	public function __construct(IO $io, Checker $checker, $clockwise, $name)
	{
		parent::__construct($io, $checker, $clockwise);
		$this->name = $name;
	}
	
	public function takeTurn($board)
	{
		while (true)
		{
			// Get moves
			$moves = [];
			
			// Get input
			$input = trim(preg_replace("/ +/", ' ', $this->io->input('Moves (from-to from-to):')));

			// Expload moves
			$exploaded_moves = explode(' ', $input);
			
			// For each moves
			foreach ($exploaded_moves as $move)
			{
				// Exploded move
				$exploded_move = explode('-', $move);

				// Add move to array
				$moves[] = [
					0 => (int) $exploded_move[0],
					1 => (int) $exploded_move[1],
				];
			}
			
			// Clone board
			$new_board = clone $board;
			
			try
			{
				// Make moves
				$new_board->makeMoves($moves, $this->checker, $this->clockwise);
			}
			catch (\Exception $e)
			{
				$this->io->error($e->getMessage());
				continue;
			}
			
			// Return new board
			return $new_board;
		}
	}
}
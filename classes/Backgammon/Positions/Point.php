<?php
namespace Backgammon\Positions;

use Backgammon\Position;
use Backgammon\Checker;

/**
 * Point on the board
 */
class Point extends Position
{	
	public function placeChecker(Checker $checker, Bar $bar = NULL)
	{
		// Get checker type
		$last_checker = $this->getChecker();
		
		// If point is empty, has same type of checker or an existing checker can be taken
		if (! $last_checker || $last_checker->colour === $checker->colour || $this->numCheckers() === 1)
		{
			// If checker is to be taken
			if ($this->numCheckers() === 1 && $last_checker->colour !== $checker->colour)
			{
				// Move checker to the bar
				$this->removeChecker($last_checker);
				$bar->placeChecker($last_checker);
			}
			
			return parent::placeChecker($checker);
		}
		
		throw new \Exception('Can not move to that point.');
	}
	
	public function numCheckers()
	{
		return parent::numCheckers();
	}
	
	/**
	 * Get last checker in stack
	 */
	public function getChecker()
	{
		return end($this->checkers);
	}
}
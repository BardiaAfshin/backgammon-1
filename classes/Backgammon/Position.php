<?php
namespace Backgammon;

/**
 * Position on the board
 */
abstract class Position
{
	protected $checkers;
	
	public function __construct(array $checkers = [])
	{
		$this->checkers = $checkers;
	}
	
	public function __clone()
	{
		foreach ($this->checkers as $key => $checker)
		{
			$this->checkers[$key] = clone $checker;
		}
	}
	
	/**
	 * Place down a checker on position
	 * 
	 * @param $checker Checker to place down
	 * @return bool
	 */
	public function placeChecker(Checker $checker)
	{
		array_push($this->checkers, $checker);
		
		return true;
	}
	
	/**
	 * Removes a checker from position
	 * 
	 * @param $checker Type of checker to remove
	 * @return mixed
	 */
	public function removeChecker(Checker $checker)
	{
		// If checker exists
		if (($key = $this->getCheckerKey($checker)))
		{
			// Remove checker from position
			unset($this->checkers[$key]);

			return true;
		}
		
		throw new \Exception('Can not move from that point.');
	}
	
	/**
	 * Returns the number of checkers on point
	 * 
	 * @param $checker_reference Checker to count
	 * @return int
	 */
	public function numCheckers(Checker $checker_reference = NULL)
	{
		// If a checker wasn't specified
		if ($checker_reference === NULL)
		{
			// Count all checkers
			$count = count($this->checkers);
		}
		else
		{
			// Count specific checker
			$count = 0;

			// For each checkers
			foreach ($this->checkers as $checker)
			{
				// If checker matches reference checker
				if ($checker->colour === $checker_reference->colour)
				{
					// Increment count
					$count++;
				}
			}
		}
		
		return $count;
	}
	
	/**
	 * Checks for checker on position
	 * 
	 * @param $checker Checker to look for
	 * @return bool
	 */
	public function checkerExists(Checker $checker)
	{
		if ($this->getCheckerKey($checker) !== false)
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * Gets the array key for a checker
	 * 
	 * @param $checker_reference Checker to look for
	 * @return mixed
	 */
	protected function getCheckerKey(Checker $checker_reference)
	{
		// For each checkers
		foreach ($this->checkers as $key => $checker)
		{
			// If checkers match
			if ($checker->colour === $checker_reference->colour)
			{
				// Return the checker's key
				return $key;
			}
		}
		
		return false;
	}
}

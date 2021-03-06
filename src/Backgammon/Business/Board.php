<?php
namespace Backgammon\Business;

use Backgammon\Business\Positions\Bar;
use Backgammon\Business\Positions\Point;

class Board
{
	public $bar;
	public $points;
	protected $checkers;
	protected $save = null;
    
	public function __construct(Bar $bar, array $points, array $checkers)
	{
		$this->bar = $bar;
		$this->points = $points;
		$this->checkers = $checkers;
	}
    
	public function __clone()
	{
		foreach ($this->points as $key => $point)
		{
			$this->points[$key] = clone $point;
		}
		
		$this->bar = clone $this->bar;
	}
	
	/**
	 * Returns an array of point keys which have the checker on
	 * 
	 * @param $checker Checker to look for
	 * @return array Point keys
	 */
	protected function getPointKeysWithChecker(Checker $checker)
	{
		$point_keys = [];
		
		// For each point
		foreach ($this->points as $key => $point)
		{
			// If checker exists on point
			if ($point->checkerExists($checker))
			{
				// Add point key to array
				$point_keys[] = $key;
			}
		}
		
		return $point_keys;
	}
	
	/**
	 * Check if a player is bearing off
	 * 
	 * @return bool
	 */
	public function bearingOff(Checker $checker)
	{
		// Get point keys with the checker on
		$point_keys = $this->getPointKeysWithChecker($checker);
		
		// If all checkers are in home quarter
		if (max($point_keys) <= 6)
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * Carries out multiple moves on the board
	 * 
	 * @param $moves Array of move objects
	 * @param $checker The players checker
	 * @param bool $clockwise If the player is going clockwise around the board
	 */
	public function makeMoves(array $moves, Checker $checker, $clockwise, array $dice)
	{
		// Check there's no more than 4 moves
		if (count($moves) > 4)
		{
			throw new \Exception('Can not have more than 4 moves.');
		}
		
		// Save current state of board
		$this->saveState();
		
		// Check if rolled a double
		/*if($dice[0] == $dice[1])
		{
			// Duplicate dice
			$dice[2] = $dice[0];
			$dice[3] = $dice[0];
		}*/
		
		try
		{
			// For each move
			foreach ($moves as $move)
			{
				// Make move
				$this->makeMove($move, $checker, $clockwise);
			}
		}
		catch (\Exception $e)
		{
			// Revert board to original state
			$this->revert();
			
			throw $e;
		}
	}
	
	/**
	 * Makes a single move on the board
	 * 
	 * @param int $from From position
	 * @param int $to To position
	 * @param $checker Checker type to move
	 * @param bool $clockwise Whether playing is going clockwise
	 */
	protected function makeMove(Move $move, Checker $checker, $clockwise)
	{
		// Validate move
		$this->validateMove($move, $checker, $clockwise);

		// Pick up checker
		if ($move->from === 25)
		{
			// From bar
			$this->bar->removeChecker($checker);
		}
		else
		{
			// From point
			$this->points[$move->from]->removeChecker($checker);
		}

		// If move isn't off the board
		if ($move->to !== 0)
		{
			// Place down checker
			$this->points[$move->to]->placeChecker($checker, $this->bar);
		}
	}
	
	/**
	 * Validates a single move - throws an exception when invalid
	 * 
	 * @param int $from From position
	 * @param int $to To position
	 * @param $checker Checker type to move
	 * @param bool $clockwise Whether playing is going clockwise
	 * @throws \Exception
	 */
	protected function validateMove(Move $move, Checker $checker, $clockwise)
	{
		// If they have a checker on the bar
		$on_bar = $this->bar->checkerExists($checker);

		// Check if player is bearing off
		$bearing_off = $this->bearingOff($checker);

		// Check that from position is valid
		if (! $this->validFromPosition($move->from, $on_bar, $bearing_off))
		{
			throw new \Exception('From position is invalid.');
		}

		// Check that to position is valid number
		if (! $this->validToPosition($move->to, $bearing_off))
		{
			throw new \Exception('To position is invalid.');
		}

		// Check player is going in the right direction
		if (! $this->validDirection($move->from, $move->to, $clockwise))
		{
			throw new \Exception('Going in the wrong direction.');
		}
	}
	
	/**
	 * Checks from position is valid
	 * 
	 * @param int $from From position
	 * @param bool $on_bar Whether player has checker(s) on bar
	 * @param bool $bearing_off Whether player is bearing off
	 * @return bool
	 */
	protected function validFromPosition($from, $on_bar, $bearing_off)
	{
		// If from point is invalid
		if (! array_key_exists($from, $this->points) && ($from !== 25 && $on_bar))
		{
			return false;
		}
		
		// If bearing off and move isn't from home quarter
		if ($bearing_off && $from > 6)
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * Checks if to position is valid
	 * 
	 * @param int $to To position
	 * @param bool $bearing_off Whether player is bearing off
	 * @return bool
	 */
	protected function validToPosition($to, $bearing_off)
	{
		if ((! $bearing_off && (array_key_exists($to, $this->points) || $to === 0)
			|| ($bearing_off && $to < 5)))
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * Checks move is in the right direction
	 * 
	 * @param int $from From position
	 * @param int $to To position
	 * @param bool $clockwise Going clockwise around the board
	 * @return bool
	 */
	protected function validDirection($from, $to, $clockwise)
	{
		if (($clockwise && $from < $to) || (! $clockwise && $from > $to))
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * Saves the current board state
	 */
	protected function saveState()
	{
		$this->save = clone $this;
	}
	
	/**
	 * Reverts the board to the last save
	 */
	protected function revert()
	{
		// If board hasn't been saved
		if ($this->save === null)
		{
			throw new Exception('Board state must be saved first before reverting.');
		}
		
		// Update properties
		$this->bar = $this->save->bar;
		$this->points = $this->save->points;
	}
	
	/**
	 * Returns a textual representation of the current board
	 */
	public function asText()
	{
		/* Example:
		╔══╦══╦══╦══╦══╦══╦══╦══╦══╦══╦══╦══╦══╗
		║5●║  ║  ║  ║3○║  ║  ║5○║  ║  ║  ║  ║2●║
		║  ║  ║  ║  ║  ║  ║  ║  ║  ║  ║  ║  ║  ║
		║13 14 15 16 17 18║  ║19 20 21 22 23 24║
		║                 ║2●║                 ║
		║                 ║1○║                 ║
		║12 11 10  9  8  7║  ║ 6  5  4  3  2  1║
		║  ║  ║  ║  ║  ║  ║  ║  ║  ║  ║  ║  ║  ║
		║5○║  ║  ║  ║3●║  ║  ║5●║  ║  ║  ║  ║2○║
		╚══╩══╩══╩══╩══╩══╩══╩══╩══╩══╩══╩══╩══╝
		*/
		
		// Board ascii
		$newline = "\n";
		$border = '║';
		$top = '╔══╦══╦══╦══╦══╦══╦══╦══╦══╦══╦══╦══╦══╗'.$newline;
		$bar = $border.'  '.$border;
		$point_tops = '║  ║  ║  ║  ║  ║  ║  ║  ║  ║  ║  ║  ║  ║'.$newline;
		$middle = $border.'                 '.$border;
		$bottom = '╚══╩══╩══╩══╩══╩══╩══╩══╩══╩══╩══╩══╩══╝';
		
		$board = $top;
		
		for ($i = 13; $i <= 18; $i++)
		{
			$board .= $border.$this->getPointNum($this->points[$i]).$this->getPointChar($this->points[$i]);
		}
		$board .= $bar;
		for ($i = 19; $i <= 24; $i++)
		{
			$board .= $this->getPointNum($this->points[$i]).$this->getPointChar($this->points[$i]).$border;
		}
		$board .= $newline;
		
		$board .= $point_tops;
		
		$board .= $border.'13 14 15 16 17 18'.$bar.'19 20 21 22 23 24'.$border.$newline;
		
		$board .= $middle.$this->getBarNum($this->checkers[1]).$this->getBarChar($this->checkers[1]).$middle.$newline;
		
		$board .= $middle.$this->getBarNum($this->checkers[2]).$this->getBarChar($this->checkers[2]).$middle.$newline;
		
		$board .= $border.'12 11 10  9  8  7'.$bar.' 6  5  4  3  2  1'.$border.$newline;
		
		$board .= $point_tops;
		
		for ($i = 12; $i >= 7; $i--)
		{
			$board .= $border.$this->getPointNum($this->points[$i]).$this->getPointChar($this->points[$i]);
		}
		$board .= $bar;
		for ($i = 6; $i >= 1; $i--)
		{
			$board .= $this->getPointNum($this->points[$i]).$this->getPointChar($this->points[$i]).$border;
		}
		$board .= $newline;
		
		$board .= $bottom.$newline;
		
		return $board;
	}
	
	/**
	 * Gets a character representation of a checker
	 * 
	 * @return string
	 */
	protected function getPointChar(Point $point)
	{
		// Get checker type on point
		$checker = $point->getChecker();
		
		// If there is a checker on point
		if ($checker)
		{
			return $checker->symbol;
		}
		
		return ' ';
	}
	
	/**
	 * Gets a character representation of a checker
	 * 
	 * @return string
	 */
	protected function getPointNum(Point $point)
	{
		// Count checkers
		$count = $point->numCheckers();
		
		// If count is not 0
		if ($count !== 0)
		{
			return $count;
		}
		
		return ' ';
	}
	
	protected function getBarChar(Checker $checker)
	{
		// Count checkers on bar
		$count = $this->bar->numCheckers($checker);
		
		// If isn't 0
		if ($count != 0)
		{
			// return checker symbol
			return $checker->symbol;
		}
		
		return ' ';
	}
	
	protected function getBarNum(Checker $checker)
	{
		// Count checkers on bar
		$count = $this->bar->numCheckers($checker);
		
		// If isn't 0
		if ($count != 0)
		{
			// return count
			return $count;
		}
		
		return ' ';
	}
}
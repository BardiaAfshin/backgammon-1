<?php
namespace Backgammon;

use Backgammon\Positions\Bar;
use Backgammon\Positions\Point;

class Board
{
	protected $bar;
    protected $points;
	protected $checkers;
    
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
	 * Carries out moves on the board
	 * 
	 * @param $moves [[#, #], [#, #]]
	 * @param $checker The players checker
	 * @param bool $clockwise If the player is going clockwise around the board
	 */
	public function makeMoves(array $moves, Checker $checker, $clockwise)
	{
		// Check there's no more than 4 moves
		if (count($moves) > 4)
		{
			throw new \Exception('Can not have more than 4 moves.');
		}

		// For each move
		foreach ($moves as $move)
		{
			// Check that move has two values
			if (count($move) !== 2)
			{
				throw new \Exception('Each move must have 2 values.');
			}
			
			// If they have a checker on the bar
			$on_bar = $this->bar->checkerExists($checker);
			
			// Check if player is bearing off
			$bearing_off = $this->bearingOff($checker);
			
			// Check that 'from position' is valid
			if ((! array_key_exists($move[0], $this->points) && ! ($move[0] === 25 && $on_bar)) || ($bearing_off && $move[0] > 6))
			{
				throw new \Exception('From point is invalid.');
			}

			// Check that 'to position' is valid number
			if ((! array_key_exists($move[1], $this->points) && $move[1] !== 0) || ($bearing_off && $move[1] > 5))
			{
				throw new \Exception('To point is invalid.');
			}
			
			// Check player is going the right way
			if (($clockwise && $move[0] > $move[1]) || (! $clockwise && $move[0] < $move[1]))
			{
				throw new \Exception('Going the wrong way.');
			}

			// Pick up checker
			if ($move[0] === 25)
			{
				// From bar
				$this->bar->removeChecker($checker);
			}
			else
			{
				// From point
				$this->points[$move[0]]->removeChecker($checker);
			}

			// Place down checker
			if ($move[1] !== 0)
			{
				$this->points[$move[1]]->placeChecker($checker, $this->bar);
			}
			
			return true;
		}
    }
	
	/**
	 * Returns a textual representation of the current board
	 */
	public function asText()
	{
		/* Example:
		╔═╦═╦═╦═╦═╦═╦═╦═╦═╦═╦═╦═╦═╗
		║●║ ║ ║ ║○║ ║ ║○║ ║ ║ ║ ║●║
		║5║ ║ ║ ║3║ ║ ║5║ ║ ║ ║ ║2║
		║           ║2║           ║
		║           ║●║           ║
		║           ║○║           ║
		║           ║1║           ║
		║5║ ║ ║ ║3║ ║ ║5║ ║ ║ ║ ║2║
		║○║ ║ ║ ║●║ ║ ║●║ ║ ║ ║ ║○║
		╚═╩═╩═╩═╩═╩═╩═╩═╩═╩═╩═╩═╩═╝
		*/
		
		// Board ascii
		$newline = "\n";
		$top = '╔═╦═╦═╦═╦═╦═╦═╦═╦═╦═╦═╦═╦═╗'.$newline;
		$border = '║';
		$bar = $border.' '.$border;
		$middle = $border.'           '.$border;
		$bottom = '╚═╩═╩═╩═╩═╩═╩═╩═╩═╩═╩═╩═╩═╝';
		
		$board = $top;
		
		for ($i = 13; $i <= 18; $i++)
		{
			$board .= $border.$this->getPointChar($this->points[$i]);
		}
		$board .= $bar;
		for ($i = 19; $i <= 24; $i++)
		{
			$board .= $this->getPointChar($this->points[$i]).$border;
		}
		$board .= $newline;
		
		for ($i = 13; $i <= 18; $i++)
		{
			$board .= $border.$this->getPointNum($this->points[$i]);
		}
		$board .= $bar;
		for ($i = 19; $i <= 24; $i++)
		{
			$board .= $this->getPointNum($this->points[$i]).$border;
		}
		$board .= $newline;

		$board .= $middle.$this->getBarNum($this->checkers[1]).$middle.$newline;
		
		$board .= $middle.$this->getBarChar($this->checkers[1]).$middle.$newline;
		
		$board .= $middle.$this->getBarChar($this->checkers[2]).$middle.$newline;
		
		$board .= $middle.$this->getBarNum($this->checkers[2]).$middle.$newline;
		
		for ($i = 12; $i >= 7; $i--)
		{
			$board .= $border.$this->getPointNum($this->points[$i]);
		}
		$board .= $bar;
		for ($i = 6; $i >= 1; $i--)
		{
			$board .= $this->getPointNum($this->points[$i]).$border;
		}
		$board .= $newline;
		
		for ($i = 12; $i >= 7; $i--)
		{
			$board .= $border.$this->getPointChar($this->points[$i]);
		}
		$board .= $bar;
		for ($i = 6; $i >= 1; $i--)
		{
			$board .= $this->getPointChar($this->points[$i]).$border;
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
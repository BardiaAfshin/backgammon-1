<?php
namespace Backgammon\Core;

use Backgammon\Business\Board;
use Backgammon\Business\Checker;
use Backgammon\Business\Checkers;
use Backgammon\Business\Game;
use Backgammon\Business\Move;
use Backgammon\Business\Player;
use Backgammon\Business\Players;
use Backgammon\Business\Positions;
use Backgammon\Core\SoundPlayers;
use Backgammon\Business\Voices;

/**
 * Backgammon factory
 */
class Factory
{
	public function buildApplication()
	{
		$io = $this->buildIO();
		$voice = $this->buildUKEnglishMaleVoice();
		return new Application($this, $io, $voice);
	}
	
	public function buildGame(Player $player1, Player $player2, $first_turn)
	{
		$board = $this->buildBoard($player1->checker, $player2->checker, $player1->clockwise);
		return new Game($board, $player1, $player2, $first_turn);
	}
	
	public function buildBoard(Checker $p1_checker, Checker $p2_checker, $p1_clockwise)
	{
		$checkers = [1 => $p1_checker, 2 => $p2_checker];
		
		// Build bar
		$bar = $this->buildBar();
		
		// Build points
		$points = [];
		for ($i = 1; $i <= 24; $i++)
		{
			$points[$i] = $this->buildPoint();
		}
		
		// Work out player's checker locations by their direction
		if ($p1_clockwise)
		{
			$clockwise_checker = $p1_checker;
			$anticlockwise_checker = $p2_checker;
		}
		else
		{
			$clockwise_checker = $p2_checker;
			$anticlockwise_checker = $p1_checker;
		}
		
		// Setup checkers on points
		for ($i = 0; $i < 2; $i++) { $points[1]->placeChecker($clockwise_checker); }
		for ($i = 0; $i < 5; $i++) { $points[12]->placeChecker($clockwise_checker); }
		for ($i = 0; $i < 3; $i++) { $points[17]->placeChecker($clockwise_checker); }
		for ($i = 0; $i < 5; $i++) { $points[19]->placeChecker($clockwise_checker); }
		
		for ($i = 0; $i < 5; $i++) { $points[6]->placeChecker($anticlockwise_checker); }
		for ($i = 0; $i < 3; $i++) { $points[8]->placeChecker($anticlockwise_checker); }
		for ($i = 0; $i < 5; $i++) { $points[13]->placeChecker($anticlockwise_checker); }
		for ($i = 0; $i < 2; $i++) { $points[24]->placeChecker($anticlockwise_checker); }

		return new Board($bar, $points, $checkers);
	}
	
	public function buildBar()
	{
		return new Positions\Bar;
	}
	
	public function buildPoint()
	{
		return new Positions\Point;
	}
	
	public function buildMove($from, $to)
	{
		return new Move($from, $to);
	}
	
	public function buildBlackChecker()
	{
		return new Checkers\Black;
	}
	
	public function buildWhiteChecker()
	{
		return new Checkers\White;
	}
	
	public function buildHumanPlayer($name, $checker, $clockwise)
	{
		return new Players\Human($name, $checker, $clockwise);
	}
	
	public function buildComputerPlayer($name, $checker, $clockwise)
	{
		return new Players\Computer($name, $checker, $clockwise);
	}
	
	public function buildIO()
	{
		return new IO;
	}

	public function buildMPG123()
	{
		return new SoundPlayers\MPG123;
	}
	
	public function buildUKEnglishMaleVoice()
	{
		$sound_player = $this->buildMPG123();
		return new Voices\UKEnglishMale($sound_player);
	}
}
<?php
namespace Backgammon\Business\Players;

use Backgammon\Business\Player;
use Backgammon\Application\IO;
use Backgammon\Business\Checker;
use Backgammon\Business\Voice;
use Backgammon\Business\Board;

class Computer extends Player
{
	public $name = 'Computer';
	protected $voice;
	
	public function __construct(IO $io, Checker $checker, $clockwise, Voice $voice)
	{
		parent::__construct($io, $checker, $clockwise);
		$this->voice = $voice;
	}
	
	public function takeTurn(Board $board)
	{
		$this->io->output('SKIP');
		return $board;
		
		/*$this->think($board, null);
		
		core::say("Roll the dice for me please.");
		sleep(2);
		while(true)
		{
			//Get computer's dice roll
			echo "Dice Roll: ";
			$say_dice = [
				"What dice roll did I get?",
				"What's my dice roll?",
				"Is my roll any good?"
			];
			$this->voice->say($say_dice[array_rand($say_dice)]);
			$input = core::get_input();

			//Filter input
			$filtered_input = trim(preg_replace("/ +/", ' ', $input));

			//Explode dice rolls
			$dice = explode(' ', $filtered_input);

			//Check that there's two dice
			if(count($dice) !== 2)
			{
				core::error("You didn't specify the correct amount of dice rolls.");
				continue;
			}

			//Check if dice values are valid
			$dice_sides = [1, 2, 3, 4, 5, 6];
			if(!in_array($dice[0], $dice_sides) || !in_array($dice[1], $dice_sides))
			{
				core::error("You specified an incorrect dice roll.");
				continue;
			}

			//Check if rolled a double
			if($dice[0] == $dice[1])
			{
				$dice[2] = $dice[0];
				$dice[3] = $dice[0];
			}

			//Think
			return $this->think($board, $dice);
		}*/
	}
	
	/*private function think($board, $dice)
	{
		$dice = [5, 3];
		
		$combinations = [[['move' => [0, 0], 'board' => $board]]];
		
		//Loop the number of dice rolls
		for($i = 0; $i < count($dice); $i++)
		{
			$new_combinations = [];
			
			//Foreach combination already in array
			foreach($combinations as $moves)
			{
				//print_r($moves);
				//Get last move in combination
				$last_move = end($moves);
				
				//print_r($last_move);
				
				//Get points with checkers on
				//print_r($last_move['board']);
				$points = $last_move['board']->get_points($this->player);

				//Loop through combinations of points and dice values
				foreach($points as $point) { foreach($dice as $die) {
					//Get move
					if($this->player == 1) $to = $point - $die;
					else $to = $point + $die;
					$move = [$point, $to];
					
					//Clone latest board in combination
					$new_board = clone $last_move['board'];
					
					//If move is valid
					if($new_board->make_move($move, $this->player) !== false)
					{
						//Add move and board object to combinations array
						$new_combinations[] = array_merge($moves, [['move' => $move, 'board' => $new_board]]);
					}
				}}
			}
			
			//echo "#################LOOP##############\n";
			//$combinations = array_merge($combinations, $new_combinations);
			$combinations = $new_combinations;
		}
		
		foreach($combinations as $moves)
		{
			foreach($moves as $move)
			{
				echo $move['move'][0].'-'.$move['move'][1].', ';
				//echo $new_board->get_ascii_board();
			}
			echo "\n";
		}
		
		exit;
		
		return $board;
	}
	
	public function analyseBoard(Board $board)
	{
		return false;
	}*/
}
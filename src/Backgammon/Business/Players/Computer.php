<?php
namespace Backgammon\Business\Players;

use Backgammon\Business\Player;
use Backgammon\Business\Board;

class Computer extends Player
{
	public function think(Board $board, array $dice)
	{
		return [];
		
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
	
	protected function analyseBoard(Board $board)
	{
		return false;
	}
}
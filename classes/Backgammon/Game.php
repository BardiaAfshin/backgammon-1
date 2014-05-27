<?php
namespace Backgammon;

/**
 * Backgammon game
 */
class Game
{
	public $board;
	protected $players;
	public $active_player;
	
	/**
	 * @param $board Board
	 * @param $player1 Player 1
	 * @param $player2 Player 2
	 * @param $first_turn Player to go first
	 */
	public function __construct(Board $board, Player $player1, Player $player2, Player $first_turn)
	{
		$this->board = $board;
		$this->players[1] = $player1;
		$this->players[2] = $player2;
		$this->active_player = $first_turn;
	}
	
	/**
	 * Takes the turn of the active player
	 */
	public function takeTurn()
	{
		// Take turn
		$new_board = $this->active_player->takeTurn($this->board);
		
		// Update board
		$this->board = $new_board;

		// Next player
		$this->nextTurn();
	}
	
	/**
	 * Updates the active player to the next player
	 */
	protected function nextTurn()
	{
		// Get active player array key
		$key = array_search($this->active_player, $this->players, true);
		
		// If player wasn't found in array
		if ($key === false)
		{
			throw new \Exception('Active player does not exist.');
		}
		
		// Increment key
		$key++;
		
		// If incremented key exist
		if (array_key_exists($key, $this->players))
		{
			// Set turn to next player
			$this->active_player = $this->players[$key];
		}
		else
		{
			// Set key to first player in array
			$this->active_player = reset($this->players);
		}
    }
	
	/**
	 * Checks if a player has won
	 * 
	 * @return boolean
	 */
	public function checkWinner()
	{
		return false;
	}
}
<?php
namespace Backgammon\Business;

/**
 * Backgammon game
 */
class Game
{
	public $board;
	public $dice;
	protected $players;
	public $active_player;
	
	/**
	 * @param $board Board
	 * @param $dice Array of dice
	 * @param $player1 Player 1
	 * @param $player2 Player 2
	 * @param $first_turn Player to go first
	 */
	public function __construct(Board $board, array $dice, Player $player1, Player $player2, Player $first_turn)
	{
		$this->board = $board;
		$this->dice = $dice;
		$this->players[1] = $player1;
		$this->players[2] = $player2;
		$this->active_player = $first_turn;
	}
	
	/**
	 * Rolls all of the dice
	 */
	public function rollDice()
	{
		foreach ($this->dice as $die)
		{
			$die->roll();
		}
	}
	
	/**
	 * Updates the active player to the next player
	 */
	public function nextTurn()
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
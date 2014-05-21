<?php
namespace Backgammon;

/**
 * Backgammon game
 */
class Game
{
	protected $io;
	protected $board;
	protected $players;
	protected $turn;
	protected $game_over = false;
	
	public function __construct(IO $io, Board $board, Player $player1, Player $player2, $first_turn)
	{
		$this->io = $io;
		$this->board = $board;
		$this->players[1] = $player1;
		$this->players[2] = $player2;
		$this->turn = $first_turn;
	}
	
	/**
	 * Game loop
	 */
	public function loop()
	{
		while (true)
		{
			// Display board
			$this->io->output($this->board->asText());

			// Take turn
			$this->takeTurn();
			sleep(3);
			
			// If a player has won
			if (($winner = $this->checkWinner()) !== false)
			{
				$this->io->output('Game over.');
				break;
			}
		}
	}
	
	protected function takeTurn()
	{
		// Take turn
		if ($this->turn == 1)
		{
			$p1 = $this->players[1];
			$this->io->output($p1->checker->symbol.' '.$p1->name.'\'s turn:');
			$new_board = $p1->takeTurn($this->board);
		}
		else
		{
			$p2 = $this->players[2];
			$this->io->output($p2->checker->symbol.' '.$p2->name.'\'s turn:');
			$new_board = $p2->takeTurn($this->board);
		}
		
		// Update board
		$this->board = $new_board;
		
		// Toggle turn
		$this->toggleTurn();
	}
	
	protected function toggleTurn()
	{
		$this->turn = ($this->turn == 0) ? 1 : 0;
    }
	
	protected function checkWinner()
	{
		return false;
	}
}
<?php
namespace Backgammon\Core;

use Backgammon\Business\Voice;
use Backgammon\Business\Game;
use Backgammon\Business\Player;
use Backgammon\Business\Board;
use Backgammon\Business\Players\Computer;

/**
 * Main application class
 */
class Application
{
	protected $factory;
	protected $io;
	protected $namespace = 'Backgammon\\Business\\Players\\';
	protected $roll_speech = [
		'Roll the dice for me please.',
		'What dice roll did I get?',
		'What\'s my dice roll?',
		'Is my roll any good?',
		'Come on double 6.',
		'Roll a good one for me.',
		'Roll them dice.',
		'Roll my dice and make it a good one.',
		'You know the drill.',
		'Be a dear and roll my dice for me.',
		'Roll em good.',
	];
	
	public function __construct(Factory $factory, IO $io, Voice $voice)
	{
		$this->factory = $factory;
		$this->io = $io;
		$this->voice = $voice;
	}
	
	/**
	 * Game initialisation
	 */
	public function execute()
	{
		// Build checkers
		$black_checker = $this->factory->buildBlackChecker();
		$white_checker = $this->factory->buildWhiteChecker();

		// Build human player
		//$name = $this->getName();
		$name = 'Lenton';
		$players[1] = $this->factory->buildHumanPlayer($name, $black_checker, false);
		
		// Build computer player
		$players[2] = $this->factory->buildComputerPlayer('Botster', $white_checker, true);

		// Get first turn
		//$first_turn = $this->getFirstTurn($players);
		$first_turn = $players[1];

		// Build backgammon game
		$game = $this->factory->buildGame($players[1], $players[2], $first_turn);

		// Run game loop
		$this->gameLoop($game);
	}
	
	protected function getName()
	{
		return ucwords($this->io->input('What is your name?'));
	}
	
	/**
	 * Ask which player is to go first
	 */
	protected function getFirstTurn(array $players)
	{
		while (true)
		{
			// Get input
			$input = (int) $this->io->input('Which player is to go first (1 or 2)?');

			// If input is valid
			if (array_key_exists($input, $players))
			{
				// Return the player to go first
				return $players[$input];
			}

			// Invalid input
			$this->io->error('Invalid input; you must enter 1 or 2.');
		}
	}
	
	/**
	 * The game loop
	 * 
	 * @param $game Game of backgammon
	 */
	protected function gameLoop(Game $game)
	{
		// Turn loop
		while (true)
		{
			// Display board
			$this->io->output("\n".$game->board->asText());
			
			// Roll the dice
			$game->rollDice();

			// Get the active player
			$player = $game->active_player;
			$this->io->output($player->checker->symbol.' '.$player->name.'\'s Turn');
			
			// Take turn
			$this->takeTurn($game->active_player, $game->board, $game->dice);

			// Check if player has won
			if (($winner = $game->checkWinner()) !== false)
			{
				// End turn loop
				break;
			}
			
			// Next player
			$game->nextTurn();
		}

		// Game over
		$this->io->output('Game over.');
	}
	
	protected function takeTurn(Player $player, Board $board, array $dice)
	{
		while (true)
		{
			// Get player type
			switch (get_class($player))
			{
				// If player is human
				case $this->namespace.'Human':
					$moves = $this->humanGetMoves();
					break;
				// If player is computer
				case $this->namespace.'Computer':
					$moves = $this->computerGetMoves($player, $board, $dice);
					break;
				default:
					throw new \Exception('Unsupported player type.');
			}

			try
			{
				// Make moves
				$board->makeMoves($moves, $player->checker, $player->clockwise, $dice);
			}
			catch (\Exception $e)
			{
				$this->io->error($e->getMessage());
				continue;
			}
			
			return true;
		}
	}
	
	/**
	 * Get moves of a human player
	 */
	protected function humanGetMoves()
	{
		while (true)
		{
			// Get input
			$input = $this->io->input('Moves (from-to from-to):');

			// Expload moves
			$exploded_moves = preg_split("/\s/", $input, null, PREG_SPLIT_NO_EMPTY);

			// If no moves were inputted
			if (empty($exploded_moves))
			{
				$this->io->error('You must input at least 1 move.');
				continue;
			}

			// For each inputted moves
			$moves = [];
			foreach ($exploded_moves as $move)
			{
				// Exploded move
				$exploded_move = preg_split("/-/", $move, null, PREG_SPLIT_NO_EMPTY);

				// Check that move has two values
				if (count($exploded_move) !== 2)
				{
					$this->io->error('Each move must have 2 values.');
					continue 2;
				}

				// Add new move object to array
				$from = (int) $exploded_move[0];
				$to = (int) $exploded_move[1];
				$moves[] = $this->factory->buildMove($from, $to);
			}
			
			return $moves;
		}
	}
	
	protected function computerGetMoves(Computer $player, Board $board, array $dice)
	{
		// Manually overide the dice values
		$this->manuallySetDice($dice);
		
		// Think about it and return move
		return $player->think($board, $dice);
	}
	
	/**
	 * Asks user for the dice roll
	 * (This is used when not using the virtual dice)
	 */
	protected function manuallySetDice(array $dice)
	{
		sleep(2);
		$this->voice->say($this->roll_speech[array_rand($this->roll_speech)]);
		
		while (true)
		{
			// Get input
			$input = $this->io->input('Dice roll:');
			
			// Expload moves
			$exploded_dice = preg_split("/\s/", $input, null, PREG_SPLIT_NO_EMPTY);
			
			// Check correct amount of values where inputted
			if (count($exploded_dice) !== ($num_dice = count($dice)))
			{
				$this->io->error('You must input '.$num_dice.' dice rolls.');
				continue;
			}
			
			foreach ($exploded_dice as $key => $value)
			{
				// Check if dice values are valid
				if (! $dice[$key]->validValue((int) $value))
				{
					$this->io->error('"'.$value.'" is an invalid die value.');
					continue 2;
				}
				
				// Overide die value
				$dice[$key]->value = $value;
			}
			
			return true;
		}
	}
}
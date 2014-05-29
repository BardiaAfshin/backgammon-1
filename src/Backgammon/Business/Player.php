<?php
namespace Backgammon\Business;

use Backgammon\Core\IO;

abstract class Player
{
	protected $io;
	public $name;
	public $checker;
	public $clockwise;
	
	/**
	 * @param $checker Reference
	 * @param bool $clockwise Go round the board clowise or anticlockwise
	 */
	public function __construct(IO $io, Checker $checker, $clockwise)
	{
		$this->io = $io;
		$this->checker = $checker;
		$this->clockwise = $clockwise;
	}
	
	abstract function takeTurn(Board $board);
}
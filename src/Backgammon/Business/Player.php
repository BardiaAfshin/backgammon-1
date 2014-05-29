<?php
namespace Backgammon\Business;

/**
 * Abstract player class
 */
abstract class Player
{
	public $name;
	public $checker;
	public $clockwise;
	
	/**
	 * @param $checker Reference
	 * @param bool $clockwise Go round the board clowise or anticlockwise
	 */
	public function __construct($name, Checker $checker, $clockwise)
	{
		$this->name = $name;
		$this->checker = $checker;
		$this->clockwise = $clockwise;
	}
}
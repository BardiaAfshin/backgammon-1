<?php
namespace Backgammon\Business;

/**
 * Represents a single move on the board
 */
class Move
{
	public $from;
	public $to;
	
	/**
	 * @param int $from Position ID
	 * @param int $to Position ID
	 */
	public function __construct($from, $to)
	{
		$this->from = $from;
		$this->to = $to;
	}
}
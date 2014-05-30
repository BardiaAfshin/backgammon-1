<?php
namespace Backgammon\Business;

class Die_
{
	protected $values;
	public $value;
	
	/**
	 * @param $values Array of face values
	 */
	public function __construct(array $values)
	{
		$this->values = $values;
	}
	
	/**
	 * Sets the die's value to a random possible value
	 */
	public function roll()
	{
		$this->value = $this->values[array_rand($this->values)];
	}
}
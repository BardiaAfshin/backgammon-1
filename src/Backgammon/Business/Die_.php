<?php
namespace Backgammon\Business;

class Die_
{
	protected $values;
	public $value = null;
	
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
	
	/**
	 * Checks if value is on dice
	 * 
	 * @param int $value
	 * @return bool
	 */
	public function validValue($value)
	{
		return in_array($value, $this->values, true);
	}
}
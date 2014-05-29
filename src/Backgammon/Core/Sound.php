<?php
namespace Backgammon\Core;

/**
 * Sound functionality using mpg123
 */
class Sound
{
	/**
	 * Play a sound file
	 * 
	 * @param string $filename Filename of sound file
	 */
	public function play($filename)
	{
		return shell_exec('mpg123 '.$filename.' 2>&1');
	}
}
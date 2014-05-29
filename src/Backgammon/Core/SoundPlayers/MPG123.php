<?php
namespace Backgammon\Core\SoundPlayers;

use Backgammon\Core\SoundPlayer;

/**
 * MPG123 sound player
 */
class MPG123 extends SoundPlayer
{
	public function play($filename)
	{
		return shell_exec('mpg123 '.$filename.' 2>&1');
	}
}
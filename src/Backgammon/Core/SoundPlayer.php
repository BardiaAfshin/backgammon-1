<?php
namespace Backgammon\Core;

/**
 * Sound player base class
 */
abstract class SoundPlayer
{
	/**
	 * Play a sound file
	 * 
	 * @param string $filename Filename of sound file
	 */
	abstract function play($filename);
}
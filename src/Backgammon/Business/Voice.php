<?php
namespace Backgammon\Business;

use Backgammon\Core\Sound;

/**
 * Base class of an artificial voice
 */
abstract class Voice
{
	protected $sound;
	protected $voice;
	
	public function __construct(Sound $sound)
	{
		$this->sound = $sound;
	}
	
	/**
	 * Says a textual message
	 * 
	 * @param string $text
	 * @return bool
	 */
	public function say($text)
	{
		// Get path
		$path = $this->getPath($text);

		// If file doesn't exist
		if (! file_exists($path))
		{
			// Download file
			$data = $this->downloadFile($this->getUrl($text), $path);
			
			// If download failed
			if ($data === false)
			{
				return false;
			}
			
			// Save file
			$this->saveFile($path, $data);
		}
		
		// Play file
		$this->sound->play($path);
		
		return true;
	}
	
	/**
	 * Gets the path of a speech MP3
	 * 
	 * @param type $text
	 * @return type
	 */
	protected function getPath($text)
	{
		// Hash text for filename
		$filename = hash('sha256', $text);
		
		// Return path
		return 'sounds/speech/'.$this->voice.'/'.$filename.'.mp3';
	}
	
	/**
	 * Gets the full API URL
	 * 
	 * @param string $text
	 * @return string
	 */
	protected function getUrl($text)
	{
		// Return url
		return 'http://api.ispeech.org/api/rest?'.
			'speed=0'.
			'&apikey=14b3d2ea838a6930a59d5f7aaaa5e8c3'.
			'&action=convert'.
			'&format=mp3'.
			'&e=audio.mp3'.
			'&voice='.urlencode($this->voice).
			"&text=".urlencode($text);
	}
	
	protected function downloadFile($url)
	{
		// Download file
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($ch);
		curl_close($ch);
		
		// If file was successfully downloaded
		if ($data)
		{
			// Return data
			return $data;
		}
		
		return false;
	}
	
	protected function saveFile($save_path, $data)
	{
		// Save file
		return file_put_contents($save_path, $data);
	}
}
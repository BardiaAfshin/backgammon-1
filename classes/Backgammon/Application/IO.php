<?php
namespace Backgammon\Application;

/**
 * Input/Output
 */
class IO
{
	/**
	 * Outputs text
	 * 
	 * @param string $text The text to output
	 * @param bool $newline Whether to append a newline character
	 * @return void
	 */
	function output($text, $newline = true)
	{
		// If newline is true
		if ($newline)
		{
			// Append newline character
			$text .= "\n";
		}
		
		// Display text
		echo $text;
	}
	
	/**
	 * Retrieve the user's input
	 * 
	 * @param string $query Question to ask
	 * @param int $chars Number of character to retrieve
	 * @return string
	 */
	function input($query = NULL, $chars = 100)
	{
		// If query is set
		if ($query)
		{
			// Output query before getting input
			$this->output($query.' ', false);
		}
		
		// Return user's input
		return trim(fread(STDIN, $chars));
	}
	
	/**
	 * Outputs an error
	 * 
	 * @param string $message Why the error occured
	 * @param bool $fatal Whether to exit the application
	 * @return void
	 */
	function error($message, $fatal = false)
	{
		// Prepend message
		$message = '[Error] '.$message;
		
		// If error was fatal
		if ($fatal)
		{
			// Exit the application
			exit($message);
		}
		
		// Display error message
		$this->output($message);
	}
}
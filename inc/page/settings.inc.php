<?php
	if(isset($_REQUEST['iPc']) && isset($_REQUEST['iPc2']))
	{
		if($_REQUEST['iPc'] == $_REQUEST['iPc2'])
		{
			$LokiDBCon->ChangePassword(trim($_REQUEST['iPc']));
		}
	}
	else if(isset($_REQUEST['iJ']))
		$LokiDBCon->ChangeSettings("JabberID", $_REQUEST['iJ']);
	else if(isset($_REQUEST['iPL']))
		$LokiDBCon->ChangeSettings("LangID", $_REQUEST['iPL']);
	else if(isset($_REQUEST['iPLP']))
		$LokiDBCon->ChangeSettings("PageLimit", $_REQUEST['iPLP']);

	if($LokiDBCon->GetSettings("Privileges") == 0)
	{
		if(isset($_REQUEST['iUa']) && isset($_REQUEST['iPa']) && isset($_REQUEST['iPa1']))
		{
			if(trim($_REQUEST['iPa']) == trim($_REQUEST['iPa1']))
			{
				$LokiDBCon->AddNewUser($_REQUEST['iUa'], $_REQUEST['iPa'], $_REQUEST['iPIa']);
			}
		}
		else if($Option_ == 'phpinfo' && strlen($Option_))
		{
			print "<div style='padding-top:50px;'>";
			phpinfo();
			die_();
		}
		else if($Option_ == 'flushall' && strlen($Option_))
		{
			$LokiDBCon->FlushDataTables();
			foreach (glob(TEMP_."/*") as $File__) 
			{
				if (is_file($File__)) 
				{
					unlink($File__);
				}
			}
		}
		else if($Option_ == 'deluser' && strlen($Option_) && isset($_REQUEST['id']))
		{
			$LokiDBCon->RemoveUser(trim($_REQUEST['id']));
		}
	}

	$PHPInfo = '';
	$FlushDB = '';
	$DelUser = '';
	if($LokiDBCon->GetSettings("Privileges") == 0)
	{
		$PHPInfo   = "?".ACTVALUE_."=settings&".OPTVALUE_."=phpinfo#admin";
		$FlushDB   = "?".ACTVALUE_."=settings&".OPTVALUE_."=flushall#admin";
		$DelUser   = "?".ACTVALUE_."=settings&".OPTVALUE_."=deluser&id=";
	}

	$ScriptURL = $ScriptURL."?".ACTVALUE_."=settings";

	?>

			<div class="row">
				<div class='col-md-1 sidebar'></div>
				<div class='col-md-10 main'>
					<ul class="nav nav-tabs">
						<li class="active"><a aria-expanded="true" href="#user" data-toggle="tab"><?php GetText_('settings_user'); ?></a></li>
						<?php
						if($LokiDBCon->GetSettings('Privileges') == 0)
							echo '<li class=""><a aria-expanded="true" href="#admin" data-toggle="tab">' .GetText_('settings_admin', FALSE). '</a></li>';
						?>
					</ul>
					
					<div id="myTabContent" class="tab-content">
						<div class="tab-pane fade active in" id="user">
							<div class="col-sm-5 col-sm-offset-1">
								<div style="padding-top:10px;" align="center">
									<div style="width: 40%;" id="alert_id_user"></div>
									<div class="col-sm-12 col-md-10 col-md-offset-1">
										<h3><?php GetText_('settings_change_password'); ?></h3>
										<form method="POST" class="form-horizontal" action="<?php echo $ScriptURL; ?>">
											<div class="form-group has-feedback">
												<input type="password" style="width: 100%;" value="" id="p1" name="iPc" class="form-control input-sm" />
												<i class="form-control-feedback glyphicon glyphicon-lock"></i>
											</div>
											<div class="form-group has-feedback">
												<input type="password" style="width: 100%;" value="" id="p2" name="iPc2" class="form-control input-sm" />
												<i class="form-control-feedback glyphicon glyphicon-lock"></i>
											</div>
											<button type="submit" onclick="return PasswordCheck(document.getElementById('p1').value, document.getElementById('p2').value, 'alert_id_user'); return true;" class="btn-sm btn btn-primary">
												 <?php GetText_('settings_change'); ?>
											</button>
										</form>
									</div>
								</div>
								
								<div style="padding-top:10px;" align="center">
									<div style="width: 40%;" id="alert_id_user"></div>
									<div class="col-sm-12 col-md-10 col-md-offset-1">
									<h3><?php GetText_('settings_set_jid'); ?></h3>
									<form method="POST" class="form-horizontal" action="<?php echo $ScriptURL; ?>">
										<div class="form-group has-feedback">
											<input type="text" style="width: 100%;" value="<?php echo $LokiDBCon->GetSettings(" JabberID ")?>" name="iJ" class="form-control input-sm" />
											<i class="form-control-feedback glyphicon glyphicon-pencil"></i>
										</div>
										<button type="submit" class="btn-sm btn btn-primary">
											<?php GetText_('settings_set'); ?>
										</button>
									</form>
									</div>
								</div>
							</div>
							
							<div class="col-sm-5">
								<div style="padding-top:10px;" align="center">
									<div style="width: 40%;" id="alert_id_user"></div>
									<div class="col-sm-12 col-md-10 col-md-offset-1">
									<h3><?php GetText_('settings_languages'); ?></h3>
									<form method="POST" class="form-horizontal" action="<?php echo $ScriptURL; ?>">

										<div class="form-group has-feedback">
											<select class="form-control" name="iPL">
												<?php $LangSize=0 ; foreach ($TextDB as $Key=> $Value) { if($LangSize == 0) { $LangSize = sizeof($TextDB[$Key]); echo '
												<option value="'.$Key.'">'.$TextDB[$Key]['LANG_NAME'].'</option>'; } else { if($LangSize != sizeof($TextDB[$Key])) echo '
												<option value="'.$Key.'" disabled>'.$TextDB[$Key]['LANG_NAME'].' (Invalid)</option>'; else echo '
												<option value="'.$Key.'">'.$TextDB[$Key]['LANG_NAME'].'</option>'; } } ?>
											</select>
										</div>
										<button type="submit" class="btn-sm btn btn-primary">
											<?php GetText_('settings_set'); ?>
										</button>
									</form>
									</div>
								</div>
							</div>
						</div>
					
					<?php
					if($LokiDBCon->GetSettings("Privileges") == 0)
					{
					echo '<div class="tab-pane fade" id="admin">
							<div class="col-sm-5 col-sm-offset-1">
								<div style="padding-top:10px;" align="center">
									<div style="width: 40%;" id="alert_id_user"></div>
									<div class="">
										<h3>' .GetText_('settings_server_settings', FALSE). '</h3>
										<div><a class="btn btn-default" onclick="return Check(this);" type="button" href="' .$FlushDB. '">' .GetText_('settings_admin_flush_all', FALSE). '</a></div>

										<div style="padding-top:10px;"><a class="btn btn-default" type="button" href="' .$PHPInfo. '">' .GetText_('settings_show_phpinfo', FALSE). '</a></div>
									</div>
								</div>
								
								<div style="padding-top:20px;" align="center">
									<div style="width: 40%;" id="alert_id_user"></div>
									<div class="">
										<h3>' .GetText_('settings_user_list', FALSE). '</h3>
											<table class="table-condensed" data-toggle="table" data-cache="false">
												<thead>
													<tr>
														<th data-sortable="true" data-halign="center" data-align="center" data-field="data">' .GetText_('settings_admin_new_name', FALSE). '</th>
														<th data-sortable="true" data-halign="center" data-align="center">' .GetText_('settings_admin_new_priv', FALSE). '</th>
														<th data-sortable="false" data-halign="center" data-align="center">' .GetText_('settings_admin_new_action', FALSE). '</th>
													</tr>
												</thead>
												<tbody>';
											$UsersDATA = $LokiDBCon->GetUsers();
											$UsersDATASize = sizeof($UsersDATA);
											if($UsersDATA != NULL && $UsersDATASize > 0)
											{
												$i = 0;
												while($UsersDATASize > $i)
												{
													echo '
													<tr>
														<td>'.$UsersDATA[$i]['username']. '</td>
														<td>'.$PrivilegesDB[$LokiDBCon->GetSettings('Privileges', $UsersDATA[$i]['settings'])].'</td>
														<td><a href="'.$DelUser.$UsersDATA[$i]['user_id']. '"><img src="'.INCLUDE_.'/style/icon/x.png" alt="" height="16" width="16"></a></td>
													</tr>';
													$i++;
												}
											}
												echo '</tbody>
											</table>
									</div>
								</div>
							</div>
							
							<div class="col-sm-5">
								<div style="padding-top:10px;" align="center">
									<div style="width: 40%;" id="alert_id_user"></div>
									<div class="col-sm-12 col-md-10 col-md-offset-1">
									<h3>' .GetText_('settings_add_user', FALSE). '</h3>
									<form method="POST" class="form-horizontal" action="' .$ScriptURL. '">
										<div class="form-group has-feedback">
											<input type="text" style="width: 100%;" value="" name="iUa" class="form-control input-sm" />
											<i class="form-control-feedback glyphicon glyphicon-user"></i>
										</div>
										<div class="form-group has-feedback">
											<input type="password" style="width: 100%;" value="" id="pa1" name="iPa" class="form-control input-sm" />
											<i class="form-control-feedback glyphicon glyphicon-lock"></i>
										</div>
										<div class="form-group has-feedback">
											<input type="password" style="width: 100%;" value="" id="pa2" name="iPa1" class="form-control input-sm" />
											<i class="form-control-feedback glyphicon glyphicon-lock"></i>
										</div>

										<div class="form-group has-feedback">
											<select class="form-control" name="iPIa">';

												$PrivCnt = 0; 
												while (sizeof($PrivilegesDB)> $PrivCnt) 
												{ 
													echo '<option value="'.$PrivCnt.'">'.$PrivilegesDB[$PrivCnt++].'</option>'; 
												}
												
											echo '</select>
										</div>

										<button type="submit" onclick="return PasswordCheck(document.getElementById(\'pa1\').value, document.getElementById(\'pa2\').value, ' .GetText_('settings_password_dont_match', FALSE). ', \'alert_id_admin\'); return true;" class="btn-sm btn btn-primary">' .GetText_('settings_add', FALSE). '</button>
									</form>
									</div>
								</div>
							</div>
						'; } ?>
					</div>
				</div>
			</div>
			<div class='col-md-1 sidebar'></div>
		</div>
	</div>
</div>
</body>
</html>

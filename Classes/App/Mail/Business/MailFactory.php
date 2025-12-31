<?php

namespace Hfm\Kursanmeldung\App\Mail\Business;

use Hfm\Kursanmeldung\App\Mail\Business\Mailer\FluidEmailMailer;
use Hfm\Kursanmeldung\App\Mail\Business\Mailer\MailerInterface;

class MailFactory
{
    /**
     * @return \Hfm\Kursanmeldung\App\Mail\Business\Mailer\MailerInterface
     */
    public function createFluidEmailMailer(): MailerInterface
    {
        return new FluidEmailMailer();
    }
}
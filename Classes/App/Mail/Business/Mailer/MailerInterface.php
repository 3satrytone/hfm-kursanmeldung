<?php

namespace Hfm\Kursanmeldung\App\Mail\Business\Mailer;

use Hfm\Kursanmeldung\App\Dto\MailDto;

interface MailerInterface
{
    /**
     * @param \Hfm\Kursanmeldung\App\Dto\MailDto $mailDto
     * @return void
     */
    public function send(MailDto $mailDto): void;
}
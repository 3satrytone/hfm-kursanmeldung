<?php

namespace Hfm\Kursanmeldung\App\Mail\Business;

use Hfm\Kursanmeldung\App\Dto\MailDto;

class MailFacade
{
    /**
     * @param \Hfm\Kursanmeldung\App\Mail\Business\MailFactory $factory
     */
    public function __construct(
        protected readonly MailFactory $factory
    ) {
    }

    /**
     * @param \Hfm\Kursanmeldung\App\Dto\MailDto $mailDto
     * @return void
     */
    public function sendFluidEmail(MailDto $mailDto): void
    {
        $this->factory->createFluidEmailMailer()->send($mailDto);
    }
}
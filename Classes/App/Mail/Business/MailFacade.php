<?php

declare(strict_types=1);

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

    /**
     * @param \Hfm\Kursanmeldung\App\Dto\MailDto $mailDto
     * @return void
     */
    public function sendFluidMailWithPageContent(MailDto $mailDto): void
    {
        $this->factory->createFluidEmailMailer()->sendWithPageContent($mailDto);
    }
}
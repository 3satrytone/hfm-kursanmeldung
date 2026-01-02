<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\App\Mail\Business;

use Hfm\Kursanmeldung\App\Mail\Business\Hydrator\MailBodyHydrator;
use Hfm\Kursanmeldung\App\Mail\Business\Mailer\FluidEmailMailer;
use Hfm\Kursanmeldung\App\Mail\Business\Mailer\MailerInterface;
use Hfm\Kursanmeldung\App\Mail\Business\Reader\ContentReader;

class MailFactory
{
    public function __construct(
        private readonly ContentReader $contentReader,
        private readonly MailBodyHydrator $mailBodyHydrator,
    ) {
    }

    /**
     * @return \Hfm\Kursanmeldung\App\Mail\Business\Mailer\MailerInterface
     */
    public function createFluidEmailMailer(): MailerInterface
    {
        return new FluidEmailMailer(
            $this->contentReader,
            $this->mailBodyHydrator
        );
    }
}
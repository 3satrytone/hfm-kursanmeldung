<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\App\Mail\Business;

use Hfm\Kursanmeldung\App\Mail\Business\Hydrator\MailBodyHydrator;
use Hfm\Kursanmeldung\App\Mail\Business\Mailer\FluidEmailMailer;
use Hfm\Kursanmeldung\App\Mail\Business\Mailer\MailerInterface;
use Hfm\Kursanmeldung\App\Mail\Business\Reader\ContentReader;
use Hfm\Kursanmeldung\App\Mail\Business\Reader\MailHistoryReader;
use Hfm\Kursanmeldung\Domain\Repository\MailhistrecipientsRepository;
use Hfm\Kursanmeldung\Domain\Repository\MailhistRepository;

class MailFactory
{
    public function __construct(
        private readonly ContentReader $contentReader,
        private readonly MailBodyHydrator $mailBodyHydrator,
        private readonly MailhistRepository $mailhistRepository,
        private readonly MailhistrecipientsRepository $mailhistrecipientsRepository,
        private readonly MailHistoryReader $mailHistoryReader,
    ) {
    }

    /**
     * @return \Hfm\Kursanmeldung\App\Mail\Business\Mailer\MailerInterface
     */
    public function createFluidEmailMailer(): MailerInterface
    {
        return new FluidEmailMailer(
            $this->contentReader,
            $this->mailBodyHydrator,
            $this->mailhistRepository,
            $this->mailhistrecipientsRepository
        );
    }

    /**
     * @return \Hfm\Kursanmeldung\App\Mail\Business\Reader\MailHistoryReader
     */
    public function createMailHistoryReader(): MailHistoryReader
    {
        return $this->mailHistoryReader;
    }
}
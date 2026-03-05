<?php

namespace Hfm\Kursanmeldung\App\Mail\Business\Reader;

use Hfm\Kursanmeldung\Domain\Repository\MailhistrecipientsRepository;
use Hfm\Kursanmeldung\Domain\Repository\MailhistRepository;

class MailHistoryReader
{
    public function __construct(
        private readonly MailhistRepository $mailhistRepository,
        private readonly MailhistrecipientsRepository $mailhistrecipientsRepository,
    )
    {
    }

    /**
     * @param int $uid
     * @return array
     */
    public function getHistoryByUid(int $uid): array
    {
        $recipients = $this->mailhistrecipientsRepository->findByRegid($uid);
        $history = [];

        foreach ($recipients as $recipient) {
            $mailhist = $this->mailhistRepository->findByUid($recipient->getMailuid());
            if ($mailhist !== null) {
                $history[$mailhist->getMailtype()] = ($history[$mailhist->getMailtype()]) ? $history[$mailhist->getMailtype()] + 1 : 1;
            }
        }

        return $history;
    }
}
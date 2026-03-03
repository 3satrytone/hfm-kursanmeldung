<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Mailhistrecipients extends AbstractEntity
{
    protected int $mailuid = 0;
    protected string $recipient = '';
    protected ?\DateTime $datesend = null;
    protected int $regid = 0;

    public function getMailuid(): int { return $this->mailuid; }
    public function setMailuid(int $mailuid): void { $this->mailuid = $mailuid; }

    public function getRecipient(): string { return $this->recipient; }
    public function setRecipient(string $recipient): void { $this->recipient = $recipient; }

    public function getDatesend(): ?\DateTime { return $this->datesend; }
    public function setDatesend(?\DateTime $datesend): void { $this->datesend = $datesend; }

    public function getRegid(): int { return $this->regid; }
    public function setRegid(int $regid): void { $this->regid = $regid; }
}

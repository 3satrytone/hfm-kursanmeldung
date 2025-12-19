<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Mailhist extends AbstractEntity
{
    protected string $subject = '';
    protected string $sendername = '';
    protected string $sendermail = '';
    protected string $pageid = '';
    protected string $mailtype = '';
    protected string $nachricht = '';
    protected int $recipients = 0;

    public function getSubject(): string { return $this->subject; }
    public function setSubject(string $subject): void { $this->subject = $subject; }

    public function getSendername(): string { return $this->sendername; }
    public function setSendername(string $sendername): void { $this->sendername = $sendername; }

    public function getSendermail(): string { return $this->sendermail; }
    public function setSendermail(string $sendermail): void { $this->sendermail = $sendermail; }

    public function getPageid(): string { return $this->pageid; }
    public function setPageid(string $pageid): void { $this->pageid = $pageid; }

    public function getMailtype(): string { return $this->mailtype; }
    public function setMailtype(string $mailtype): void { $this->mailtype = $mailtype; }

    public function getNachricht(): string { return $this->nachricht; }
    public function setNachricht(string $nachricht): void { $this->nachricht = $nachricht; }

    public function getRecipients(): int { return $this->recipients; }
    public function setRecipients(int $recipients): void { $this->recipients = $recipients; }
}

<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\App\Dto;

use Hfm\Kursanmeldung\Domain\Model\Kursanmeldung;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;

class MailDto
{
    private RequestInterface $request;
    private string $sendTo;
    private Address $sendFrom;
    private string $subject;
    private string $format;
    private string $template;
    private array $assignments = [];
    private ?int $pageUid;
    private ?Kursanmeldung $kursanmeldung;

    /**
     * @return \TYPO3\CMS\Extbase\Mvc\RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Mvc\RequestInterface $request
     */
    public function setRequest(RequestInterface $request): void
    {
        $this->request = $request;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @param string $format
     */
    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    /**
     * @return \Symfony\Component\Mime\Address
     */
    public function getSendFrom(): Address
    {
        return $this->sendFrom;
    }

    /**
     * @param \Symfony\Component\Mime\Address $sendFrom
     * @return void
     */
    public function setSendFrom(Address $sendFrom): void
    {
        $this->sendFrom = $sendFrom;
    }

    /**
     * @return string
     */
    public function getSendTo(): string
    {
        return $this->sendTo;
    }

    /**
     * @param string $sendTo
     */
    public function setSendTo(string $sendTo): void
    {
        $this->sendTo = $sendTo;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * @return ?int
     */
    public function getPageUid(): ?int
    {
        return $this->pageUid;
    }

    /**
     * @param int $pageUid
     */
    public function setPageUid(?int $pageUid): void
    {
        $this->pageUid = $pageUid;
    }

    /**
     * @return \Hfm\Kursanmeldung\Domain\Model\Kursanmeldung|null
     */
    public function getKursanmeldung(): ?Kursanmeldung
    {
        return $this->kursanmeldung;
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Kursanmeldung|null $kursanmeldung
     */
    public function setKursanmeldung(?Kursanmeldung $kursanmeldung): void
    {
        $this->kursanmeldung = $kursanmeldung;
    }

    /**
     * @return array
     */
    public function getAssignments(): array
    {
        return $this->assignments;
    }

    /**
     * @param array $assignments
     */
    public function setAssignments(array $assignments): void
    {
        $this->assignments = $assignments;
    }
}
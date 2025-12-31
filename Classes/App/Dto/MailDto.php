<?php

namespace Hfm\Kursanmeldung\App\Dto;

use TYPO3\CMS\Extbase\Mvc\RequestInterface;

class MailDto
{
    private RequestInterface $request;
    private string $sendTo;
    private string $sendFrom;
    private string $subject;
    private string $format;
    private string $template;

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
     * @return string
     */
    public function getSendFrom(): string
    {
        return $this->sendFrom;
    }

    /**
     * @param string $sendFrom
     */
    public function setSendFrom(string $sendFrom): void
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
}
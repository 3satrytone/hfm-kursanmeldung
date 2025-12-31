<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\App\Dto;

class NovalnetResponseDto
{
    /**
     * @param string $response
     * @param bool $success
     * @param array $novalnetData
     */
    public function __construct(
        private string $response,
        private array $novalnetData,
        private bool $success,
        private string $message,
    ) {
    }

    /**
     * @return string
     */
    public function getResponse(): string
    {
        return $this->response;
    }

    /**
     * @param string $response
     */
    public function setResponse(string $response): void
    {
        $this->response = $response;
    }

    /**
     * @return array
     */
    public function getNovalnetData(): array
    {
        return $this->novalnetData;
    }

    /**
     * @param array $novalnetData
     */
    public function setNovalnetData(array $novalnetData): void
    {
        $this->novalnetData = $novalnetData;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @param bool $success
     */
    public function setSuccess(bool $success): void
    {
        $this->success = $success;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}
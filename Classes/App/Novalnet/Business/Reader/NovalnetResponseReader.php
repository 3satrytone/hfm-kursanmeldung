<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\App\Novalnet\Business\Reader;

use Hfm\Kursanmeldung\App\Dto\NovalnetResponseDto;

class NovalnetResponseReader
{
    protected const ACCESS_DENIED = 'ACCESS DENIED';
    protected const ZUGRIFF_VERWEIGERT = 'ZUGRIFF VERWEIGERT';

    public function readCurlResponse(NovalnetResponseDto $responseDto): NovalnetResponseDto
    {
        if($this->containsAccessDeniedHtml($responseDto->getResponse())){
            $responseDto->setSuccess(false);
            $responseDto->setMessage(self::ACCESS_DENIED);
        }

        return $responseDto;
    }

    /**
     * @param string $response
     * @return bool
     */
    private function containsAccessDeniedHtml(string $response): bool
    {
        if ($response === '') {
            return false;
        }

        // Fast substring checks (covers both title and body text)
        if (stripos($response, self::ZUGRIFF_VERWEIGERT) !== false || stripos($response, self::ACCESS_DENIED) !== false) {
            return true;
        }

        return false;
    }
}
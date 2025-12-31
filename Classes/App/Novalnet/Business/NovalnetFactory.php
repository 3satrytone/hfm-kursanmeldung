<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\App\Novalnet\Business;

use Hfm\Kursanmeldung\App\Novalnet\Business\Reader\NovalnetResponseReader;

readonly class NovalnetFactory
{
    /**
     * @return \Hfm\Kursanmeldung\App\Novalnet\Business\Reader\NovalnetResponseReader
     */
    public function createResponseReader(): NovalnetResponseReader
    {
        return new NovalnetResponseReader();
    }
}
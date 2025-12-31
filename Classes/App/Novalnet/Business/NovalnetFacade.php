<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\App\Novalnet\Business;

use Hfm\Kursanmeldung\App\Dto\NovalnetResponseDto;

readonly class NovalnetFacade
{
    /**
     * @param \Hfm\Kursanmeldung\App\Novlanet\Business\NovalnetFactory $factory
     */
    public function __construct(
        protected NovalnetFactory $factory
    ) {
    }

    /**
     * @param \Hfm\Kursanmeldung\App\Dto\NovalnetResponseDto $novalnetReposeDto
     * @return \Hfm\Kursanmeldung\App\Dto\NovalnetResponseDto
     */
    public function getNovalnetResponse(NovalnetResponseDto $novalnetReposeDto): NovalnetResponseDto
    {
        return $this->factory->createResponseReader()->readCurlResponse($novalnetReposeDto);
    }
}
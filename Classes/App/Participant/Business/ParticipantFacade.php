<?php

namespace Hfm\Kursanmeldung\App\Participant\Business;

use Hfm\Kursanmeldung\App\Dto\StepDataParticipantDto;

class ParticipantFacade
{
    /**
     * @param \Hfm\Kursanmeldung\App\Participant\Business\ParticipantFactory $factory
     */
    public function __construct(
        protected ParticipantFactory $factory
    )
    {

    }

    /**
     * @param \Hfm\Kursanmeldung\App\Dto\StepDataParticipantDto $dataParticipantDto
     * @return \Hfm\Kursanmeldung\App\Dto\StepDataParticipantDto
     */
    public function hydrateParticipantFromStepData(StepDataParticipantDto $dataParticipantDto): StepDataParticipantDto
    {
        return $this->factory->createParticipantHydrator()->hydrate($dataParticipantDto);
    }
}
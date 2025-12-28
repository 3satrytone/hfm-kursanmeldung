<?php

namespace Hfm\Kursanmeldung\App\Participant\Business\Hydrator;

use Hfm\Kursanmeldung\App\Dto\StepDataParticipantDto;

interface ParticipantHydratorInterface
{
    /**
     * @param \Hfm\Kursanmeldung\App\Dto\StepDataParticipantDto $dataParticipantDto
     * @return \Hfm\Kursanmeldung\App\Dto\StepDataParticipantDto
     */
    public function hydrate(StepDataParticipantDto $dataParticipantDto): StepDataParticipantDto;
}
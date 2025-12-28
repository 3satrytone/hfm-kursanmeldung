<?php

namespace Hfm\Kursanmeldung\App\Participant\Business;

use Hfm\Kursanmeldung\App\Participant\Business\Hydrator\ParticipantHydrator;
use Hfm\Kursanmeldung\App\Participant\Business\Hydrator\ParticipantHydratorInterface;

readonly class ParticipantFactory
{
    /**
     * @return \Hfm\Kursanmeldung\App\Participant\Business\Hydrator\ParticipantHydratorInterface
     */
    public function createParticipantHydrator(): ParticipantHydratorInterface
    {
        return new ParticipantHydrator();
    }
}
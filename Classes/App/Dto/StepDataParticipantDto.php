<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\App\Dto;

use Hfm\Kursanmeldung\Domain\Model\Step1Data;
use Hfm\Kursanmeldung\Domain\Model\Step2Data;
use Hfm\Kursanmeldung\Domain\Model\Teilnehmer;

class StepDataParticipantDto
{
    public function __construct(
        private Step1Data $step1Data,
        private Step2Data $step2Data,
        private Teilnehmer $teilnehmer,
    ) {
    }

    /**
     * @return \Hfm\Kursanmeldung\Domain\Model\Step1Data
     */
    public function getStep1Data(): Step1Data
    {
        return $this->step1Data;
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Step1Data $step1Data
     */
    public function setStep1Data(Step1Data $step1Data): void
    {
        $this->step1Data = $step1Data;
    }

    /**
     * @return \Hfm\Kursanmeldung\Domain\Model\Step2Data
     */
    public function getStep2Data(): Step2Data
    {
        return $this->step2Data;
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Step2Data $step2Data
     * @return void
     */
    public function setStep2Data(Step2Data $step2Data): void
    {
        $this->step2Data = $step2Data;
    }

    /**
     * @return \Hfm\Kursanmeldung\Domain\Model\Teilnehmer
     */
    public function getTeilnehmer(): Teilnehmer
    {
        return $this->teilnehmer;
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Teilnehmer $teilnehmer
     */
    public function setTeilnehmer(Teilnehmer $teilnehmer): void
    {
        $this->teilnehmer = $teilnehmer;
    }
}
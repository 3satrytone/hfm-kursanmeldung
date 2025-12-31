<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\App\Participant\Business\Hydrator;

use DateTime;
use Hfm\Kursanmeldung\App\Dto\StepDataParticipantDto;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;

class ParticipantHydrator implements ParticipantHydratorInterface
{
    /**
     * @param \Hfm\Kursanmeldung\App\Dto\StepDataParticipantDto $dataParticipantDto
     * @return \Hfm\Kursanmeldung\App\Dto\StepDataParticipantDto
     * @throws \Exception
     */
    public function hydrate(StepDataParticipantDto $dataParticipantDto): StepDataParticipantDto
    {
        $tn = $dataParticipantDto->getTeilnehmer();
        $tn->setVorname($dataParticipantDto->getStep1Data()->getFirstName());
        $tn->setNachname($dataParticipantDto->getStep1Data()->getLastName());
        $tn->setAnrede($dataParticipantDto->getStep1Data()->getGender());
        $tn->setTitel($dataParticipantDto->getStep1Data()->getTitle());
        $tn->setMatrikel($dataParticipantDto->getStep2Data()->getMatrikel());
        $tn->setGebdate(new DateTime($dataParticipantDto->getStep1Data()->getBirthday()));
        $tn->setNation($dataParticipantDto->getStep1Data()->getNationality());
        $tn->setAdresse1($dataParticipantDto->getStep1Data()->getAddress());
        $tn->setHausnr($dataParticipantDto->getStep1Data()->getHouseno());
        $tn->setAdresse2($dataParticipantDto->getStep1Data()->getAddressadd());
        $tn->setPlz($dataParticipantDto->getStep1Data()->getZip());
        $tn->setOrt($dataParticipantDto->getStep1Data()->getCity());
        $tn->setLand($dataParticipantDto->getStep1Data()->getCountry());
        $tn->setTelefon($dataParticipantDto->getStep1Data()->getPhone());
        $tn->setMobil($dataParticipantDto->getStep1Data()->getMobile());
        $tn->setEmail($dataParticipantDto->getStep1Data()->getEmail());
        $tn->setDatein(new \DateTime('NOW'));


        return $dataParticipantDto;
    }
}
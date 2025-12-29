<?php

namespace Hfm\Kursanmeldung\Utility;

use Hfm\Kursanmeldung\Domain\Model\Kurs;
use Hfm\Kursanmeldung\Domain\Model\Step1Data;
use Hfm\Kursanmeldung\Domain\Repository\KursanmeldungRepository;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class ParticipantUtility
{
    /**
     * @param \Hfm\Kursanmeldung\Domain\Repository\KursanmeldungRepository $kursanmeldungRepository
     */
    public function __construct(
        protected readonly KursanmeldungRepository $kursanmeldungRepository
    ) {
    }

    /**
     * @param Kurs|null $kurs
     * @param array $kursTn
     * @return array
     */
    public function checkKursParticipant(?Kurs $kurs, array $kursTn): array
    {
        $tnactionArr = [];
        if ($kurs != null) {
            $kursTnActive = $kurs->getAktivtn();
            $kursTnPassiv = $kurs->getPassivtn();

            // Reduzierung der Maximalen Plätze um registrierte Teilnehmer
            if (!empty($kursTn)) {
                foreach ($kursTn as $tn) {
                    if ($tn->getTeilnahmeart() == 0) {
                        $kursTnActive -= 1;
                    } else {
                        $kursTnPassiv -= 1;
                    }
                }
            }
            if ($kursTnActive > 0) {
                $tnactionArr['aktiveTn'] = $kursTnActive;
            }
            if ($kursTnPassiv > 0) {
                $tnactionArr['passivTn'] = $kursTnPassiv;
            }
        }

        return $tnactionArr;
    }

    /**
     * @return string
     */
    public function translateFromXlf(): string
    {
        $args = func_get_args();
        $key = array_shift($args);

        return LocalizationUtility::translate($key, 'kursanmeldung', $args) ?? '';
    }

    /**
     * @param array $entries
     * @param string $transTable
     * @return array
     */
    public function getOptions(
        array $entries = [],
        string $transTable = 'tx_kursanmeldung_domain_model_kursanmeldung'
    ): array {
        $categories = [];
        foreach ($entries as $key => $entry) {
            $categories[$key] = $this->translateFromXlf($transTable . $entry);
        }

        return $categories;
    }


    /**
     * @param \TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface $kurs
     * @param bool $onlyName
     * @return string
     */
    public function getKursname(DomainObjectInterface $kurs, bool $onlyName = false): string
    {
        $kursName = '';
        if (!empty($kurs) && $kurs !== null) {
            $prof = $kurs->getProfessor()->current();
            // Name für Head
            if (!empty($prof)) {
                if ($onlyName) {
                    return $prof->getName();
                }
                $kursName = $prof->getName() . ' ' . $kurs->getKurszeitstart()->format(
                        'd.m.Y'
                    ) . ' - ' . $kurs->getKurszeitend()->format('d.m.Y');
            }
        }

        return $kursName;
    }


    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage|null $hotel
     * @return array[]
     */
    public function splitHotel(?ObjectStorage $hotel): array
    {
        $hotelArr = [
            'hotel' => [],
            'price' => [],
            'room' => [],
        ];

        if (!empty($hotel)) {
            foreach ($hotel as $value) {
                $hotelArr['hotel'][$value->getUid()] = $value->getHotel();
                // Ermäßigung auf Kundenwunsch rausgenommen bspw. ezpreiserm
                $hotelArr['price'][$value->getUid()] = [
                    'ezpreis' => $value->getEzpreis(),
                    'dzpreis' => $value->getDzpreis(),
                    'dz2preis' => $value->getDz2preis(),
                ];
                $entries = [
                    'ezpreis' => '.step2.valezpreis',
                    'dzpreis' => '.step2.valdzpreis',
                    'dz2preis' => '.step2.valdz2preis'
                ];
                $hotelArr['room'] = $this->getOptions($entries);
            }
        }

        return $hotelArr;
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Step1Data $step1data
     * @param \Hfm\Kursanmeldung\Domain\Model\Kurs $kurs
     * @return bool
     */
    public function checkForParticipant(
        Step1Data $step1data,
        Kurs $kurs,
    ): bool {
        $part = $this->kursanmeldungRepository->getParticipantsByMail($kurs->getUid(), $step1data->getEmail());

        return ($part->count() > 0);
    }

    /**
     * @param string $password
     * @return string
     * @throws \TYPO3\CMS\Core\Crypto\PasswordHashing\InvalidPasswordHashException
     */
    public function getHashedPasswordFromPassword(string $password): string
    {
        $hashInstance = GeneralUtility::makeInstance(PasswordHashFactory::class)->getDefaultHashInstance('FE');

        return $hashInstance->getHashedPassword($password);
    }
}
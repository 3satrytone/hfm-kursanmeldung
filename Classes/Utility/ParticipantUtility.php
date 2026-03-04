<?php

namespace Hfm\Kursanmeldung\Utility;

use DateTime;
use Hfm\Kursanmeldung\Domain\Model\Kurs;
use Hfm\Kursanmeldung\Domain\Model\Kursanmeldung;
use Hfm\Kursanmeldung\Domain\Model\Prof;
use Hfm\Kursanmeldung\Domain\Model\Step1Data;
use Hfm\Kursanmeldung\Domain\Repository\GebuehrenRepository;
use Hfm\Kursanmeldung\Domain\Repository\HotelRepository;
use Hfm\Kursanmeldung\Domain\Repository\KursanmeldungRepository;
use TYPO3\CMS\Core\Country\CountryProvider;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Localization\Locale;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class ParticipantUtility
{
    /**
     * @param \Hfm\Kursanmeldung\Domain\Repository\KursanmeldungRepository $kursanmeldungRepository
     * @param \Hfm\Kursanmeldung\Domain\Repository\GebuehrenRepository $gebuehrenRepository
     */
    public function __construct(
        protected readonly KursanmeldungRepository $kursanmeldungRepository,
        protected readonly GebuehrenRepository $gebuehrenRepository,
        protected readonly HotelRepository $hotelRepository,
        protected readonly CountryProvider $countryProvider,
        protected readonly LanguageServiceFactory $languageServiceFactory,
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

        $lang = null;
        if(isset($args[1]) && in_array($args[1],['de','en'])){
            $lang = $args[1];
        }

        return LocalizationUtility::translate($key, 'kursanmeldung', $args, $lang) ?? '';
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
            $prof = $kurs->getProfessor();
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

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Kursanmeldung $register
     * @return array
     */
    public function getFluidAssignments(Kursanmeldung $register): array
    {
        $assignments = [];

        $assignments['hotel'] = '';
        $assignments['hotel_name'] = '';
        $hotelId = $register->getHotel();
        if ($hotelId > 0) {
            $hotelObj = $this->hotelRepository->findByUid($hotelId);
            if (!empty($hotelObj) && $hotelObj != null) {
                $roomFromDate = new DateTime($register->getRoomfrom());
                $roomToDate = new DateTime($register->getRoomTo());
                $assignments['hotel_name'] = $hotelObj->getHotel();
                $assignments['hotel'] = $hotelObj->getHotel() . ", " . $this->translateFromXlf(
                        'tx_kursanmeldung_domain_model_kursanmeldung.step2.val' . $register->getRoom()
                    ) . ', ' . $roomFromDate->format('d.m.Y') . '-' . $roomToDate->format('d.m.Y');
            }
        }

        $assignments['room'] = $register->getRoom();
        $assignments['room_de'] = $register->getRoom() ? $this->translateFromXlf('tx_kursanmeldung_domain_model_teilnehmer' . $register->getRoom(), 'kursanmeldung', 'de') : '';
        $assignments['room_en'] = $register->getRoom() ? $this->translateFromXlf('tx_kursanmeldung_domain_model_teilnehmer' . $register->getRoom(), 'kursanmeldung') : '';
        $assignments['roomwith'] = $register->getRoomwith() ?: '';
        $assignments['roomfrom'] = $register->getRoomfrom() ?: '';
        $assignments['roomto'] = $register->getRoomTo() ?: '';

        $tn = $register->getTn();
        $tn->rewind();
        $address = $tn->current();
        $assignments['birth'] = $address->getGebdate() ? $address->getGebdate()->format('d.m.Y') : '';

        $gender = $address->getAnrede();
        $genderText = $this->translateFromXlf('tx_kursanmeldung_domain_model_kursanmeldung.email.gender.' . $gender);
        $assignments['gender'] = $genderText;
        $assignments['titel'] = $address->getTitel() ?: '';
        $assignments['matrikel'] = $address->getMatrikel() ?: '';
        $assignments['gebdate'] = $address->getGebdate() ? $address->getGebdate()->format('d.m.Y') : '';
        $assignments['firstname'] = ucfirst($address->getVorname());
        $assignments['lastname'] = ucfirst($address->getNachname());
        $assignments['fee'] = $register->getGebuehr() . ' EUR';
        $assignments['kurs'] = $this->getKursname($register->getKurs(), true);
        $assignments['city'] = $address->getOrt();
        $assignments['zip'] = $address->getPlz();
        $assignments['phone'] = $address->getTelefon();
        $assignments['mobile'] = $address->getMobil();
        $assignments['email'] = $address->getEmail();
        $assignments['telefon'] = $address->getTelefon();
        $assignments['mobil'] = $address->getMobil();
        $assignments['emailfrom'] = $address->getEmail();
        $assignments['addressObj'] = $address;
        $assignments['registerObj'] = $register;
        $assignments['address'] = trim($address->getAdresse1() . ' ' . $address->getHausnr());
        $assignments['addressadd'] = $address->getAdresse2();
        $assignments['adresse1'] = $address->getAdresse1();
        $assignments['adresse2'] = $address->getAdresse2();
        $assignments['plz'] = $address->getPlz();
        $assignments['ort'] = $address->getOrt();
        $assignments['hausnr'] = $address->getHausnr();
        $assignments['anrede_de'] = $this->translateFromXlf('anrede.' . $address->getAnrede(), 'kursanmeldung', 'de');
        $assignments['anrede_en'] = $this->translateFromXlf('anrede.' . $address->getAnrede(), 'kursanmeldung');
        $assignments['anrede_add_de'] = $this->translateFromXlf('anrede.add.' . $address->getAnrede(), 'kursanmeldung', 'de');
        $assignments['anrede_add_en'] = $this->translateFromXlf('anrede.add.' . $address->getAnrede(), 'kursanmeldung');
        $assignments['anrede'] = $this->translateFromXlf('anrede' . $address->getAnrede(), 'kursanmeldung');


        $locale = $address->getSprache() === 'English' ? new Locale('en') : new Locale('de');
        $languageService = $this->languageServiceFactory->create($locale);
        $landCountry = $this->countryProvider->getByIsoCode($address->getLand());
        $assignments['country'] = $languageService->sl($landCountry->getLocalizedNameLabel());
        $landNation = $this->countryProvider->getByIsoCode($address->getNation());
        $assignments['nation'] = $languageService->sl($landNation->getLocalizedNameLabel());

        $assignments['invoiceDate'] = $register->getDatein() ? $register->getDatein()->format('d.m.Y') : '';
        $assignments['datein'] = $register->getDatein() ? $register->getDatein()->format('d.m.Y') : '';
        $assignments['no'] = $register->getUid();
        $assignments['amount'] = $register->getGebuehr();
        $assignments['kursstart'] = $register->getKurs()->getKurszeitstart()->format('d.m.Y');
        $assignments['kursend'] = $register->getKurs()->getKurszeitend()->format('d.m.Y');
        $assignments['teilnahmeart'] = $register->getTeilnahmeart();
        $assignments['teilnahmeart_de'] = $this->translateFromXlf('tx_kursanmeldung_domain_model_teilnehmer.tnart.' . $register->getTeilnahmeart(), 'kursanmeldung', 'de');
        $assignments['teilnahmeart_en'] = $this->translateFromXlf('tx_kursanmeldung_domain_model_teilnehmer.tnart.' . $register->getTeilnahmeart(), 'kursanmeldung');
        $assignments['instrument'] = $register->getKurs() ? $register->getKurs()->getInstrument() : '';
        $assignments['anmeldestatus'] = $register->getAnmeldestatus();
        $assignments['programm'] = $register->getProgramm();
        $assignments['duo'] = $register->getDuo();
        $assignments['duoname'] = $register->getDuoname();
        $assignments['duosel'] = $register->getDuosel();
        $assignments['comment'] = $register->getComment();
        $assignments['uid'] = $register->getUid();


        $assignments['gebuehr'] = $register->getGebuehr() ? number_format($register->getGebuehr(), 2, ',', '.') : '0,00';
        $assignments['bezahlt'] = $register->getBezahlt() ? 'Ja' : 'Nein';
        $assignments['zahlart'] = $register->getZahlart();
        $assignments['zahltbis'] = $register->getZahltbis() ? $register->getZahltbis()->format('d.m.Y') : '';
        $assignments['gezahlt'] = $register->getGezahlt() ? $register->getGezahltos() : '0,00';
        $assignments['gezahltag'] = $register->getGezahltag() ? $register->getGezahltag() : '0,00';
        $assignments['gezahltos'] = $register->getGezahltos() ? $register->getGezahltos() : '0,00';

        $kurs = $register->getKurs();

        if(!empty($kurs)){
            $prof = $kurs->getProfessor();
            $assignments['professor'] = $prof ? $prof->getName() : '';
            $assignments['kurszeitstart'] = ($kurs->getKurszeitstart()->format('d.m.Y') == '01.01.1970')? '' : $kurs->getKurszeitstart()->format('d.m.Y');
            $assignments['kurszeitend'] = ($kurs->getKurszeitend()->format('d.m.Y') == '01.01.1970')? '' : $kurs->getKurszeitend()->format('d.m.Y');
            $assignments['anreisedate'] = ($kurs->getAnreisedate()->format('d.m.Y') == '01.01.1970')? '' : $kurs->getAnreisedate()->format('d.m.Y');
            $assignments['kursuid'] = $kurs->getUid();
        }

        $gebuehr = $this->gebuehrenRepository->findByUid($kurs->getGebuehr());
        if(!empty($gebuehr)){
            $assignments['anmeldung'] = $gebuehr->getAnmeldung() ? number_format($gebuehr->getAnmeldung(), 2, ',','.') : '0,00';
            $assignments['anmeldungerm'] = $gebuehr->getAnmeldungerm() ? number_format($gebuehr->getAnmeldungerm(), 2, ',','.') : '0,00';
            $assignments['aktivengeb'] = $gebuehr->getAktivengeb() ? number_format($gebuehr->getAktivengeb(), 2, ',','.') : '0,00';
            $assignments['aktivengeberm'] = $gebuehr->getAktivengeberm() ? number_format($gebuehr->getAktivengeberm(), 2, ',','.') : '0,00';
            $assignments['passivgeb'] = $gebuehr->getPassivgeb() ? number_format($gebuehr->getPassivgeb(), 2, ',','.') : '0,00';
            $assignments['passivgeberm'] = $gebuehr->getPassivgeberm() ? number_format($gebuehr->getPassivgeberm(), 2, ',','.') : '0,00';
            $assignments['teilnehmergeb'] = (!empty($assignments['matrikel'])) ? $assignments['aktivengeberm'] : $assignments['aktivengeb'];
            if($register->getStipendiat() == 1) $assignments['teilnehmergeb'] = '';
        }

        return $assignments;
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Kurs $kurs
     * @return string
     */
    public function getProfInstrument(Kurs $kurs): string
    {
        $kursName = '';

        $prof = $kurs->getProfessor();

        if ($prof instanceof Prof) {
            $kursName = $prof->getName() . ' ' . $kurs->getInstrument();
        }

        return $kursName;
    }
}
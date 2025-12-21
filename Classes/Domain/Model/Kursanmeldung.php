<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Kursanmeldung extends AbstractEntity
{
    protected ?int $deflang = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hfm\Kursanmeldung\Domain\Model\Teilnehmer>
     */
    protected ObjectStorage $tn;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hfm\Kursanmeldung\Domain\Model\Kurs>
     */
    protected ObjectStorage $kurs;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hfm\Kursanmeldung\Domain\Model\Uploads>
     */
    protected ObjectStorage $uploads;

    protected int $bezahlt = 0;
    protected int $bezahltag = 0;
    protected string $zahlart = '';
    protected int $zahltbis = 0;
    protected string $gezahlt = '';
    protected string $gezahltag = '';
    protected string $gezahltos = '';
    protected int $hotel = 0;
    protected string $room = '';
    protected string $roomwith = '';
    protected string $roomfrom = '';
    protected string $roomto = '';
    protected string $gebuehr = '';
    protected string $gebuehrag = '';
    protected int $gebuehrdat = 0;
    protected int $datein = 0;
    protected string $teilnahmeart = '';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hfm\Kursanmeldung\Domain\Model\Anmeldestatus>
     */
    protected ObjectStorage $anmeldestatus;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hfm\Kursanmeldung\Domain\Model\Anmeldestatus>
     */
    protected ObjectStorage $profstatus;

    protected string $programm = '';
    protected string $orchesterstudio = '';
    protected int $duo = 0;
    protected string $duosel = '';
    protected string $duoname = '';
    protected string $comment = '';
    protected int $agb = 0;
    protected int $datenschutz = 0;
    protected int $savedata = 0;
    protected string $salt = '';
    protected string $registrationkey = '';
    protected int $doitime = 0;
    protected string $novalnettid = '';
    protected string $novalnettidag = '';
    protected string $novalnetcno = '';
    protected string $notice = '';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hfm\Kursanmeldung\Domain\Model\Ensem>
     */
    protected int $ensemble = 0;
    protected int $stipendiat = 0;
    protected int $studentship = 0;
    protected int $studystat = 0;

    public function getDeflang(): ?int
    {
        return $this->deflang;
    }

    public function setDeflang(?int $deflang): void
    {
        $this->deflang = $deflang;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hfm\Kursanmeldung\Domain\Model\Teilnehmer>
     */
    public function getTn(): ObjectStorage
    {
        return $this->tn;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hfm\Kursanmeldung\Domain\Model\Teilnehmer> $tn
     * @return void
     */
    public function setTn(ObjectStorage $tn): void
    {
        $this->tn = $tn;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hfm\Kursanmeldung\Domain\Model\Kurs>
     */
    public function getKurs(): ObjectStorage
    {
        return $this->kurs;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hfm\Kursanmeldung\Domain\Model\Kurs> $kurs
     * @return void
     */
    public function setKurs(ObjectStorage $kurs): void
    {
        $this->kurs = $kurs;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hfm\Kursanmeldung\Domain\Model\Uploads>
     */
    public function getUploads(): ObjectStorage
    {
        return $this->uploads;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hfm\Kursanmeldung\Domain\Model\Uploads> $uploads
     * @return void
     */
    public function setUploads(ObjectStorage $uploads): void
    {
        $this->uploads = $uploads;
    }

    public function getBezahlt(): int
    {
        return $this->bezahlt;
    }

    public function setBezahlt(int $bezahlt): void
    {
        $this->bezahlt = $bezahlt;
    }

    public function getBezahltag(): int
    {
        return $this->bezahltag;
    }

    public function setBezahltag(int $bezahltag): void
    {
        $this->bezahltag = $bezahltag;
    }

    public function getZahlart(): string
    {
        return $this->zahlart;
    }

    public function setZahlart(string $zahlart): void
    {
        $this->zahlart = $zahlart;
    }

    public function getZahltbis(): int
    {
        return $this->zahltbis;
    }

    public function setZahltbis(int $zahltbis): void
    {
        $this->zahltbis = $zahltbis;
    }

    public function getGezahlt(): string
    {
        return $this->gezahlt;
    }

    public function setGezahlt(string $gezahlt): void
    {
        $this->gezahlt = $gezahlt;
    }

    public function getGezahltag(): string
    {
        return $this->gezahltag;
    }

    public function setGezahltag(string $gezahltag): void
    {
        $this->gezahltag = $gezahltag;
    }

    public function getGezahltos(): string
    {
        return $this->gezahltos;
    }

    public function setGezahltos(string $gezahltos): void
    {
        $this->gezahltos = $gezahltos;
    }

    public function getHotel(): int
    {
        return $this->hotel;
    }

    public function setHotel(int $hotel): void
    {
        $this->hotel = $hotel;
    }

    public function getRoom(): string
    {
        return $this->room;
    }

    public function setRoom(string $room): void
    {
        $this->room = $room;
    }

    public function getRoomwith(): string
    {
        return $this->roomwith;
    }

    public function setRoomwith(string $roomwith): void
    {
        $this->roomwith = $roomwith;
    }

    public function getRoomfrom(): string
    {
        return $this->roomfrom;
    }

    public function setRoomfrom(string $roomfrom): void
    {
        $this->roomfrom = $roomfrom;
    }

    public function getRoomto(): string
    {
        return $this->roomto;
    }

    public function setRoomto(string $roomto): void
    {
        $this->roomto = $roomto;
    }

    public function getGebuehr(): string
    {
        return $this->gebuehr;
    }

    public function setGebuehr(string $gebuehr): void
    {
        $this->gebuehr = $gebuehr;
    }

    public function getGebuehrag(): string
    {
        return $this->gebuehrag;
    }

    public function setGebuehrag(string $gebuehrag): void
    {
        $this->gebuehrag = $gebuehrag;
    }

    public function getGebuehrdat(): int
    {
        return $this->gebuehrdat;
    }

    public function setGebuehrdat(int $gebuehrdat): void
    {
        $this->gebuehrdat = $gebuehrdat;
    }

    public function getDatein(): int
    {
        return $this->datein;
    }

    public function setDatein(int $datein): void
    {
        $this->datein = $datein;
    }

    public function getTeilnahmeart(): string
    {
        return $this->teilnahmeart;
    }

    public function setTeilnahmeart(string $teilnahmeart): void
    {
        $this->teilnahmeart = $teilnahmeart;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hfm\Kursanmeldung\Domain\Model\Anmeldestatus>
     */
    public function getAnmeldestatus(): ObjectStorage
    {
        return $this->anmeldestatus;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hfm\Kursanmeldung\Domain\Model\Anmeldestatus> $anmeldestatus
     * @return void
     */
    public function setAnmeldestatus(ObjectStorage $anmeldestatus): void
    {
        $this->anmeldestatus = $anmeldestatus;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hfm\Kursanmeldung\Domain\Model\Anmeldestatus>
     */
    public function getProfstatus(): ObjectStorage
    {
        return $this->profstatus;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hfm\Kursanmeldung\Domain\Model\Anmeldestatus> $profstatus
     * @return void
     */
    public function setProfstatus(ObjectStorage $profstatus): void
    {
        $this->profstatus = $profstatus;
    }

    public function getProgramm(): string
    {
        return $this->programm;
    }

    public function setProgramm(string $programm): void
    {
        $this->programm = $programm;
    }

    public function getOrchesterstudio(): string
    {
        return $this->orchesterstudio;
    }

    public function setOrchesterstudio(string $orchesterstudio): void
    {
        $this->orchesterstudio = $orchesterstudio;
    }

    public function getDuo(): int
    {
        return $this->duo;
    }

    public function setDuo(int $duo): void
    {
        $this->duo = $duo;
    }

    public function getDuosel(): string
    {
        return $this->duosel;
    }

    public function setDuosel(string $duosel): void
    {
        $this->duosel = $duosel;
    }

    public function getDuoname(): string
    {
        return $this->duoname;
    }

    public function setDuoname(string $duoname): void
    {
        $this->duoname = $duoname;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    public function getAgb(): int
    {
        return $this->agb;
    }

    public function setAgb(int $agb): void
    {
        $this->agb = $agb;
    }

    public function getDatenschutz(): int
    {
        return $this->datenschutz;
    }

    public function setDatenschutz(int $datenschutz): void
    {
        $this->datenschutz = $datenschutz;
    }

    public function getSavedata(): int
    {
        return $this->savedata;
    }

    public function setSavedata(int $savedata): void
    {
        $this->savedata = $savedata;
    }

    public function getSalt(): string
    {
        return $this->salt;
    }

    public function setSalt(string $salt): void
    {
        $this->salt = $salt;
    }

    public function getRegistrationkey(): string
    {
        return $this->registrationkey;
    }

    public function setRegistrationkey(string $registrationkey): void
    {
        $this->registrationkey = $registrationkey;
    }

    public function getDoitime(): int
    {
        return $this->doitime;
    }

    public function setDoitime(int $doitime): void
    {
        $this->doitime = $doitime;
    }

    public function getNovalnettid(): string
    {
        return $this->novalnettid;
    }

    public function setNovalnettid(string $novalnettid): void
    {
        $this->novalnettid = $novalnettid;
    }

    public function getNovalnettidag(): string
    {
        return $this->novalnettidag;
    }

    public function setNovalnettidag(string $novalnettidag): void
    {
        $this->novalnettidag = $novalnettidag;
    }

    public function getNovalnetcno(): string
    {
        return $this->novalnetcno;
    }

    public function setNovalnetcno(string $novalnetcno): void
    {
        $this->novalnetcno = $novalnetcno;
    }

    public function getNotice(): string
    {
        return $this->notice;
    }

    public function setNotice(string $notice): void
    {
        $this->notice = $notice;
    }

    public function getEnsemble(): int
    {
        return $this->ensemble;
    }

    public function setEnsemble(int $ensemble): void
    {
        $this->ensemble = $ensemble;
    }

    public function getStipendiat(): int
    {
        return $this->stipendiat;
    }

    public function setStipendiat(int $stipendiat): void
    {
        $this->stipendiat = $stipendiat;
    }

    public function getStudentship(): int
    {
        return $this->studentship;
    }

    public function setStudentship(int $studentship): void
    {
        $this->studentship = $studentship;
    }

    public function getStudystat(): int
    {
        return $this->studystat;
    }

    public function setStudystat(int $studystat): void
    {
        $this->studystat = $studystat;
    }
}

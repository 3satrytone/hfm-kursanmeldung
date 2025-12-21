<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Model;

use DateTime;
use DateTimeInterface;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Kurs extends AbstractEntity
{
    /**
     * @var bool
     */
    protected bool $aktiv = false;

    /**
     * @var string
     */

    /**
     * @var string
     * @Extbase\Validate("NotEmpty")
     */
    protected string $kursnr = '';

    /**
     * @var string
     * @Extbase\Validate("NotEmpty")
     */
    protected string $instrument = '';

    /**
     * @var \DateTime
     * @Extbase\Validate("NotEmpty")
     */
    public ?\DateTime $kurszeitstart = null;


    /**
     * @var \DateTime
     * @Extbase\Validate("NotEmpty")
     */
    public ?\DateTime $kurszeitend = null;

    /**
     * @var \DateTime
     * @Extbase\Validate("NotEmpty")
     */
    public ?\DateTime $anreisedate = null;


    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hfm\Kursanmeldung\Domain\Model\Orte>
     */
    protected ObjectStorage $kursort;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hfm\Kursanmeldung\Domain\Model\Prof>
     */
    protected ObjectStorage $professor;

    /**
     * @var int
     */
    protected int $gebuehr = 0;

    /**
     * @var string
     */
    protected string $gebuehrcom = '';

    /**
     * @var bool
     */
    protected bool $orchstudio = false;

    /**
     * @var int
     * @Extbase\Validate("NotEmpty")
     */
    protected int $aktivtn = 0;

    /**
     * @var int
     */
    protected int $passivtn = 0;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hfm\Kursanmeldung\Domain\Model\Hotel>
     */
    protected ObjectStorage $hotel;

    /**
     * @var int
     */
    protected int $maxupload = 0;

    /**
     * @var bool
     */
    protected bool $weblink = false;

    /**
     * @var bool
     */
    protected bool $youtube = false;
    protected bool $vita = false;
    protected bool $stipendien = false;
    protected bool $duo = false;
    protected string $duosel = '';
    protected string $ensemble = '';

    public function getAktiv(): bool
    {
        return $this->aktiv;
    }

    public function setAktiv(bool $aktiv): void
    {
        $this->aktiv = $aktiv;
    }

    public function getKursnr(): string
    {
        return $this->kursnr;
    }

    public function setKursnr(string $kursnr): void
    {
        $this->kursnr = $kursnr;
    }

    public function getInstrument(): string
    {
        return $this->instrument;
    }

    public function setInstrument(string $instrument): void
    {
        $this->instrument = $instrument;
    }


    /**
     * Returns the kurszeitstart
     *
     * @return \DateTime $kurszeitstart
     */
    public function getKurszeitstart()
    {
        return $this->kurszeitstart;
    }

    /**
     * Sets the kurszeitstart
     *
     * @param \DateTime $kurszeitstart
     * @return void
     */
    public function setKurszeitstart(\DateTime $kurszeitstart)
    {
        $this->kurszeitstart = $kurszeitstart;
    }

    /**
     * Returns the kurszeitend
     *
     * @return \DateTime $kurszeitend
     */
    public function getKurszeitend()
    {
        return $this->kurszeitend;
    }

    /**
     * Sets the kurszeitend
     *
     * @param \DateTime $kurszeitend
     * @return void
     */
    public function setKurszeitend(\DateTime $kurszeitend)
    {
        $this->kurszeitend = $kurszeitend;
    }

    /**
     * Returns the anreisedate
     *
     * @return \DateTime $anreisedate
     */
    public function getAnreisedate()
    {
        return $this->anreisedate;
    }

    /**
     * Sets the anreisedate
     *
     * @param \DateTime $anreisedate
     * @return void
     */
    public function setAnreisedate(\DateTime $anreisedate)
    {
        $this->anreisedate = $anreisedate;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hfm\Kursanmeldung\Domain\Model\Orte>
     */
    public function getKursort(): ObjectStorage
    {
        return $this->kursort;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hfm\Kursanmeldung\Domain\Model\Orte> $kursort
     * @return void
     */
    public function setKursort(ObjectStorage $kursort): void
    {
        $this->kursort = $kursort;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hfm\Kursanmeldung\Domain\Model\Prof>
     */
    public function getProfessor(): ObjectStorage
    {
        return $this->professor;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hfm\Kursanmeldung\Domain\Model\Prof> $professor
     */
    public function setProfessor(ObjectStorage $professor): void
    {
        $this->professor = $professor;
    }

    public function getGebuehr(): int
    {
        return $this->gebuehr;
    }

    public function setGebuehr(int $gebuehr): void
    {
        $this->gebuehr = $gebuehr;
    }

    public function getGebuehrcom(): string
    {
        return $this->gebuehrcom;
    }

    public function setGebuehrcom(string $gebuehrcom): void
    {
        $this->gebuehrcom = $gebuehrcom;
    }

    public function getOrchstudio(): bool
    {
        return $this->orchstudio;
    }

    public function setOrchstudio(bool $orchstudio): void
    {
        $this->orchstudio = $orchstudio;
    }

    public function getAktivtn(): int
    {
        return $this->aktivtn;
    }

    public function setAktivtn(int $aktivtn): void
    {
        $this->aktivtn = $aktivtn;
    }

    public function getPassivtn(): int
    {
        return $this->passivtn;
    }

    public function setPassivtn(int $passivtn): void
    {
        $this->passivtn = $passivtn;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hfm\Kursanmeldung\Domain\Model\Hotel>
     */
    public function getHotel(): ObjectStorage
    {
        return $this->hotel;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hfm\Kursanmeldung\Domain\Model\Hotel> $hotel
     * @return void
     */
    public function setHotel(ObjectStorage $hotel): void
    {
        $this->hotel = $hotel;
    }

    public function getMaxupload(): int
    {
        return $this->maxupload;
    }

    public function setMaxupload(int $maxupload): void
    {
        $this->maxupload = $maxupload;
    }

    public function getWeblink(): bool
    {
        return $this->weblink;
    }

    public function setWeblink(bool $weblink): void
    {
        $this->weblink = $weblink;
    }

    public function getYoutube(): bool
    {
        return $this->youtube;
    }

    public function setYoutube(bool $youtube): void
    {
        $this->youtube = $youtube;
    }

    public function getVita(): bool
    {
        return $this->vita;
    }

    public function setVita(bool $vita): void
    {
        $this->vita = $vita;
    }

    public function getStipendien(): bool
    {
        return $this->stipendien;
    }

    public function setStipendien(bool $stipendien): void
    {
        $this->stipendien = $stipendien;
    }

    public function getDuo(): bool
    {
        return $this->duo;
    }

    public function setDuo(bool $duo): void
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

    public function getEnsemble(): string
    {
        return $this->ensemble;
    }

    public function setEnsemble(string $ensemble): void
    {
        $this->ensemble = $ensemble;
    }
}

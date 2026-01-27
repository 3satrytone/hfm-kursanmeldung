<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * ProfStatus
 */
class ProfStatus extends AbstractEntity
{
    /**
     * @var \Hfm\Kursanmeldung\Domain\Model\Anmeldestatus|null
     */
    protected $status = null;

    /**
     * @var string
     */
    protected string $kurz = '';

    /**
     * @var int
     */
    protected int $kursanmeldung = 0;

    /**
     * @var int
     */
    protected int $feuser = 0;

    /**
     * @return \Hfm\Kursanmeldung\Domain\Model\Anmeldestatus|null
     */
    public function getStatus(): ?Anmeldestatus
    {
        return $this->status;
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Anmeldestatus|null $status
     */
    public function setStatus(?Anmeldestatus $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getKurz(): string
    {
        return $this->kurz;
    }

    /**
     * @param string $kurz
     */
    public function setKurz(string $kurz): void
    {
        $this->kurz = $kurz;
    }

    /**
     * @return int
     */
    public function getKursanmeldung(): int
    {
        return $this->kursanmeldung;
    }

    /**
     * @param int $kursanmeldung
     */
    public function setKursanmeldung(int $kursanmeldung): void
    {
        $this->kursanmeldung = $kursanmeldung;
    }

    /**
     * @return int
     */
    public function getFeuser(): int
    {
        return $this->feuser;
    }

    /**
     * @param int $feuser
     */
    public function setFeuser(int $feuser): void
    {
        $this->feuser = $feuser;
    }
}

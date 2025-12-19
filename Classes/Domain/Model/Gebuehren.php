<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Gebuehren extends AbstractEntity
{
    /**
     * @var float
     */
    protected float $anmeldung = 0.00;

    /**
     * @var float
     */
    protected float $anmeldungerm = 0.00;

    /**
     * @var float
     */
    protected float $aktivengeb = 0.00;

    /**
     * @var float
     */
    protected float $aktivengeberm = 0.00;

    /**
     * @var float
     */
    protected float $passivgeb = 0.00;

    /**
     * @var float
     */
    protected float $passivgeberm = 0.00;

    public function getAnmeldung(): float { return $this->anmeldung; }
    public function setAnmeldung(float $anmeldung): void { $this->anmeldung = $anmeldung; }

    public function getAnmeldungerm(): float { return $this->anmeldungerm; }
    public function setAnmeldungerm(float $anmeldungerm): void { $this->anmeldungerm = $anmeldungerm; }

    public function getAktivengeb(): float { return $this->aktivengeb; }
    public function setAktivengeb(float $aktivengeb): void { $this->aktivengeb = $aktivengeb; }

    public function getAktivengeberm(): float { return $this->aktivengeberm; }
    public function setAktivengeberm(float $aktivengeberm): void { $this->aktivengeberm = $aktivengeberm; }

    public function getPassivgeb(): float { return $this->passivgeb; }
    public function setPassivgeb(float $passivgeb): void { $this->passivgeb = $passivgeb; }

    public function getPassivgeberm(): float { return $this->passivgeberm; }
    public function setPassivgeberm(float $passivgeberm): void { $this->passivgeberm = $passivgeberm; }
}

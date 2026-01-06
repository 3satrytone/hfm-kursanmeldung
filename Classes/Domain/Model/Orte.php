<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Orte extends AbstractEntity
{
    protected string $name = '';
    protected string $ort = '';
    protected string $plz = '';
    protected string $adresse = '';
    /**
     * @var float|null
     */
    protected ?float $longi = null;
    /**
     * @var float|null
     */
    protected ?float $lati = null;
    protected string $beschreibung = '';

    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }

    public function getOrt(): string { return $this->ort; }
    public function setOrt(string $ort): void { $this->ort = $ort; }

    public function getPlz(): string { return $this->plz; }
    public function setPlz(string $plz): void { $this->plz = $plz; }

    public function getAdresse(): string { return $this->adresse; }
    public function setAdresse(string $adresse): void { $this->adresse = $adresse; }

    public function getLongi(): ?float { return $this->longi; }
    public function setLongi(?float $longi): void { $this->longi = $longi; }

    public function getLati(): ?float { return $this->lati; }
    public function setLati(?float $lati): void { $this->lati = $lati; }

    public function getBeschreibung(): string { return $this->beschreibung; }
    public function setBeschreibung(string $beschreibung): void { $this->beschreibung = $beschreibung; }
}

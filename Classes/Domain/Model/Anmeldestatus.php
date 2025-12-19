<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Anmeldestatus extends AbstractEntity
{
    protected string $anmeldestatus = '';
    protected string $kurz = '';
    protected string $beschreibung = '';
    protected int $reducetnart = 0;

    public function getAnmeldestatus(): string { return $this->anmeldestatus; }
    public function setAnmeldestatus(string $anmeldestatus): void { $this->anmeldestatus = $anmeldestatus; }

    public function getKurz(): string { return $this->kurz; }
    public function setKurz(string $kurz): void { $this->kurz = $kurz; }

    public function getBeschreibung(): string { return $this->beschreibung; }
    public function setBeschreibung(string $beschreibung): void { $this->beschreibung = $beschreibung; }

    public function getReducetnart(): int { return $this->reducetnart; }
    public function setReducetnart(int $reducetnart): void { $this->reducetnart = $reducetnart; }
}

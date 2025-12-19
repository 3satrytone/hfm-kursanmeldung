<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Uploads extends AbstractEntity
{
    protected int $kurs = 0;
    protected string $kat = '';
    protected string $name = '';
    protected string $pfad = '';
    protected string $datein = '';

    public function getKurs(): int { return $this->kurs; }
    public function setKurs(int $kurs): void { $this->kurs = $kurs; }

    public function getKat(): string { return $this->kat; }
    public function setKat(string $kat): void { $this->kat = $kat; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }

    public function getPfad(): string { return $this->pfad; }
    public function setPfad(string $pfad): void { $this->pfad = $pfad; }

    public function getDatein(): string { return $this->datein; }
    public function setDatein(string $datein): void { $this->datein = $datein; }
}

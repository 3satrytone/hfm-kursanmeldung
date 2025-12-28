<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Model;

use DateTime;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Teilnehmer extends AbstractEntity
{
    protected string $vorname = '';
    protected string $nachname = '';
    protected int $anrede = 0;
    protected string $titel = '';
    protected string $matrikel = '';
    protected DateTime $gebdate;
    protected string $sprache = '';
    protected string $nation = '';
    protected string $adresse1 = '';
    protected string $hausnr = '';
    protected string $adresse2 = '';
    protected string $plz = '';
    protected string $ort = '';
    protected string $land = '';
    protected string $telefon = '';
    protected string $mobil = '';
    protected string $telefax = '';
    protected string $email = '';
    protected DateTime $datein;

    public function getVorname(): string { return $this->vorname; }
    public function setVorname(string $vorname): void { $this->vorname = $vorname; }

    public function getNachname(): string { return $this->nachname; }
    public function setNachname(string $nachname): void { $this->nachname = $nachname; }

    public function getAnrede(): int { return $this->anrede; }
    public function setAnrede(int $anrede): void { $this->anrede = $anrede; }

    public function getTitel(): string { return $this->titel; }
    public function setTitel(string $titel): void { $this->titel = $titel; }

    public function getMatrikel(): string { return $this->matrikel; }
    public function setMatrikel(string $matrikel): void { $this->matrikel = $matrikel; }

    public function getGebdate(): DateTime { return $this->gebdate; }
    public function setGebdate(DateTime $gebdate): void { $this->gebdate = $gebdate; }

    public function getSprache(): string { return $this->sprache; }
    public function setSprache(string $sprache): void { $this->sprache = $sprache; }

    public function getNation(): string { return $this->nation; }
    public function setNation(string $nation): void { $this->nation = $nation; }

    public function getAdresse1(): string { return $this->adresse1; }
    public function setAdresse1(string $adresse1): void { $this->adresse1 = $adresse1; }

    public function getHausnr(): string { return $this->hausnr; }
    public function setHausnr(string $hausnr): void { $this->hausnr = $hausnr; }

    public function getAdresse2(): string { return $this->adresse2; }
    public function setAdresse2(string $adresse2): void { $this->adresse2 = $adresse2; }

    public function getPlz(): string { return $this->plz; }
    public function setPlz(string $plz): void { $this->plz = $plz; }

    public function getOrt(): string { return $this->ort; }
    public function setOrt(string $ort): void { $this->ort = $ort; }

    public function getLand(): string { return $this->land; }
    public function setLand(string $land): void { $this->land = $land; }

    public function getTelefon(): string { return $this->telefon; }
    public function setTelefon(string $telefon): void { $this->telefon = $telefon; }

    public function getMobil(): string { return $this->mobil; }
    public function setMobil(string $mobil): void { $this->mobil = $mobil; }

    public function getTelefax(): string { return $this->telefax; }
    public function setTelefax(string $telefax): void { $this->telefax = $telefax; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): void { $this->email = $email; }

    public function getDatein(): DateTime { return $this->datein; }
    public function setDatein(DateTime $datein): void { $this->datein = $datein; }
}

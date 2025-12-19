<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Hotel extends AbstractEntity
{
    protected string $hotel = '';
    protected string $beschreibung = '';

    protected float $ezpreis = 0.00;
    protected float $ezpreiserm = 0.00;
    protected float $dzpreis = 0.00;
    protected float $dzpreiserm = 0.00;
    protected float $dz2preis = 0.00;
    protected float $dz2preiserm = 0.00;

    public function getHotel(): string
    {
        return $this->hotel;
    }

    public function setHotel(string $hotel): void
    {
        $this->hotel = $hotel;
    }

    public function getBeschreibung(): string
    {
        return $this->beschreibung;
    }

    public function setBeschreibung(string $beschreibung): void
    {
        $this->beschreibung = $beschreibung;
    }

    public function getEzpreis(): float
    {
        return $this->ezpreis;
    }

    public function setEzpreis(float $ezpreis): void
    {
        $this->ezpreis = $ezpreis;
    }

    public function getEzpreiserm(): float
    {
        return $this->ezpreiserm;
    }

    public function setEzpreiserm(float $ezpreiserm): void
    {
        $this->ezpreiserm = $ezpreiserm;
    }

    public function getDzpreis(): float
    {
        return $this->dzpreis;
    }

    public function setDzpreis(float $dzpreis): void
    {
        $this->dzpreis = $dzpreis;
    }

    public function getDzpreiserm(): float
    {
        return $this->dzpreiserm;
    }

    public function setDzpreiserm(float $dzpreiserm): void
    {
        $this->dzpreiserm = $dzpreiserm;
    }

    public function getDz2preis(): float
    {
        return $this->dz2preis;
    }

    public function setDz2preis(float $dz2preis): void
    {
        $this->dz2preis = $dz2preis;
    }

    public function getDz2preiserm(): float
    {
        return $this->dz2preiserm;
    }

    public function setDz2preiserm(float $dz2preiserm): void
    {
        $this->dz2preiserm = $dz2preiserm;
    }
}

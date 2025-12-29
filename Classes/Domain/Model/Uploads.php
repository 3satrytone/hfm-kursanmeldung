<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Model;

use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Uploads extends AbstractEntity
{
    protected Kurs $kurs;
    protected string $kat = '';
    protected string $name = '';
    protected string $pfad = '';
    protected \DateTime $datein;
    /**
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference|null
     */
    protected ?FileReference $fileref;

    public function getKurs(): Kurs { return $this->kurs; }
    public function setKurs(Kurs $kurs): void { $this->kurs = $kurs; }

    public function getKat(): string { return $this->kat; }
    public function setKat(string $kat): void { $this->kat = $kat; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }

    public function getPfad(): string { return $this->pfad; }
    public function setPfad(string $pfad): void { $this->pfad = $pfad; }

    public function getDatein(): \DateTime { return $this->datein; }
    public function setDatein(\DateTime $datein): void { $this->datein = $datein; }

    public function getFileref(): ?FileReference {return $this->fileref; }
    public function setFileref(?FileReference $fileref): void { $this->fileref = $fileref; }
}

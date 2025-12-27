<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;

class Step3Data extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * tnb
	 *
	 * @var int|null
     * @Extbase\Validate("NotEmpty")
	 */
	protected ?int $tnb;

	/**
	 * privacy
	 *
	 * @var int|null
     * @Extbase\Validate("NotEmpty")
	 */
	protected ?int $privacy;

    /**
     * @var int|null
     */
	protected ?int $savedata;

    /**
     * @return int|null
     */
	public function getTnb(): ?int
    {
		return $this->tnb;
	}

    /**
     * @param int|null $tnb
     * @return void
     */
	public function setTnb(?int $tnb): void
    {
		$this->tnb = $tnb;
	}

    /**
     * @return int|null
     */
	public function getPrivacy(): ?int
    {
		return $this->privacy;
	}

    /**
     * @param int|null $privacy
     * @return void
     */
	public function setPrivacy(?int $privacy): void
    {
		$this->privacy = $privacy;
	}

    /**
     * @return int|null
     */
	public function getSavedata(): ?int
    {
		return $this->savedata;
	}

    /**
     * @param int|null $savedata
     * @return void
     */
	public function setSavedata(?int $savedata): void
    {
		$this->savedata = $savedata;
	}
}
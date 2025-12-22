<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;

class Step3Data extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * tnb
	 *
	 * @var int
	 * @Extbase\Validate("NotEmpty")
	 */
	protected int $tnb = 0;

	/**
	 * privacy
	 *
	 * @var int
	 * @Extbase\Validate("NotEmpty")
	 */
	protected int $privacy = 0;

    /**
     * @var int
     */
	protected int $savedata = 0;

    /**
     * @return int
     */
	public function getTnb(): int
    {
		return $this->tnb;
	}

    /**
     * @param int $tnb
     * @return void
     */
	public function setTnb(int $tnb): void
    {
		$this->tnb = $tnb;
	}

    /**
     * @return int
     */
	public function getPrivacy(): int
    {
		return $this->privacy;
	}

    /**
     * @param int $privacy
     * @return void
     */
	public function setPrivacy(int $privacy): void
    {
		$this->privacy = $privacy;
	}

    /**
     * @return int
     */
	public function getSavedata(): int
    {
		return $this->savedata;
	}

    /**
     * @param int $savedata
     * @return void
     */
	public function setSavedata(int $savedata): void
    {
		$this->savedata = $savedata;
	}
}
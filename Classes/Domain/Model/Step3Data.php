<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Model;
/**
 * Btstep3 Data
 *
 */
class Step3Data extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * tnb
	 *
	 * @var integer
	 * @validate NotEmpty
	 */
	protected $tnb;

	/**
	 * privacy
	 *
	 * @var integer
	 * @validate NotEmpty
	 */
	protected $privacy;

	/**
	 * savedata
	 *
	 * @var integer
	 */
	protected $savedata;

	/**
	 * Returns the tnb
	 *
	 * @return integer $tnb
	 */
	public function getTnb() {
		return $this->tnb;
	}

	/**
	 * Sets the tnb
	 *
	 * @param integer $tnb
	 * @return void
	 */
	public function setTnb($tnb) {
		$this->tnb = $tnb;
	}

	/**
	 * Returns the privacy
	 *
	 * @return integer $privacy
	 */
	public function getPrivacy() {
		return $this->privacy;
	}

	/**
	 * Sets the privacy
	 *
	 * @param integer $privacy
	 * @return void
	 */
	public function setPrivacy($privacy) {
		$this->privacy = $privacy;
	}

	/**
	 * Returns the savedata
	 *
	 * @return integer $savedata
	 */
	public function getSavedata() {
		return $this->savedata;
	}

	/**
	 * Sets the savedata
	 *
	 * @param integer $savedata
	 * @return void
	 */
	public function setSavedata($savedata) {
		$this->savedata = $savedata;
	}
}
?>
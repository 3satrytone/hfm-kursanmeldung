<?php
namespace Justorange\JoKursanmeldung\Domain\Model;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 Steffen Schneider <info@justorange.de>, JUSTORANGE
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Btstep3 Data
 *
 * @package jo_kursanmeldung
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
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
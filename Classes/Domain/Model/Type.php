<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Model;
/**
 * Type
 */
class Type extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * name
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $name = '';

	/**
	 * description
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * category
	 *
	 * @var string
	 */
	protected $category = '';

	/**
	 * duration
	 *
	 * @var integer
	 * @validate NotEmpty
	 */
	protected $duration = 0;

	/**
	 * durationGastro
	 *
	 * @var integer
	 */
	protected $durationGastro = 0;

	/**
	 * maximum numbers of participants
	 *
	 * @var integer
	 */
	protected $maxparticipant = 0;

	/**
	 * Returns the name
	 *
	 * @return string $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Sets the name
	 *
	 * @param string $name
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * Returns the description
	 *
	 * @return string $description
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Sets the description
	 *
	 * @param string $description
	 * @return void
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * Returns the category
	 *
	 * @return string $category
	 */
	public function getCategory() {
		return $this->category;
	}

	/**
	 * Sets the category
	 *
	 * @param string $category
	 * @return void
	 */
	public function setCategory($category) {
		$this->category = $category;
	}

	/**
	 * Returns the duration
	 *
	 * @return integer $duration
	 */
	public function getDuration() {
		return $this->duration;
	}

	/**
	 * Sets the duration
	 *
	 * @param integer $duration
	 * @return void
	 */
	public function setDuration($duration) {
		$this->duration = $duration;
	}

	/**
	 * Returns the durationGastro
	 *
	 * @return integer $durationGastro
	 */
	public function getDurationGastro() {
		return $this->durationGastro;
	}

	/**
	 * Sets the durationGastro
	 *
	 * @param integer $durationGastro
	 * @return void
	 */
	public function setDurationGastro($durationGastro) {
		$this->durationGastro = $durationGastro;
	}

	/**
	 * Returns the maxparticipant
	 *
	 * @return integer $maxparticipant
	 */
	public function getMaxparticipant() {
		return $this->maxparticipant;
	}

	/**
	 * Sets the maxparticipant
	 *
	 * @param integer $maxparticipant
	 * @return void
	 */
	public function setMaxparticipant($maxparticipant) {
		$this->maxparticipant = $maxparticipant;
	}

}
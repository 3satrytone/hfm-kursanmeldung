<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Model;

/**
 * Date
 */
class Date extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * starton
	 *
	 * @var \DateTime
	 * @validate NotEmpty
	 */
	protected $starton = NULL;

	/**
	 * endon
	 *
	 * @var \DateTime
	 */
	protected $endon = NULL;

	/**
	 * mm from type table
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Justorange\JoBrewerytour\Domain\Model\Type>
	 * @validate NotEmpty
	 */
	protected $typeid = 0;

	/**
	 * freeplaces
	 *
	 * @var integer
	 */
	protected $freeplaces = 0;

	/**
	 * description
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * Returns the starton
	 *
	 * @return \DateTime $starton
	 */
	public function getStarton() {
		return $this->starton;
	}

	/**
	 * Sets the starton
	 *
	 * @param \DateTime $starton
	 * @return void
	 */
	public function setStarton(\DateTime $starton) {
		$this->starton = $starton;
	}

	/**
	 * Returns the endon
	 *
	 * @return \DateTime $endon
	 */
	public function getEndon() {
		return $this->endon;
	}

	/**
	 * Sets the endon
	 *
	 * @param \DateTime $endon
	 * @return void
	 */
	public function setEndon(\DateTime $endon) {
		$this->endon = $endon;
	}

	/**
	 * Returns the typeid
	 *
	 * @return integer $typeid
	 */
	public function getTypeid() {
		return $this->typeid;
	}

	/**
	 * Sets the typeid
	 *
	 * @param \Justorange\JoBrewerytour\Domain\Model\Type $typeid
	 * @return void
	 */
	public function setTypeid($typeid) {
		$this->typeid = $typeid;
	}

	/**
	 * Adds a type
	 *
	 * @param \Justorange\JoBrewerytour\Domain\Model\Type $typeid
	 * @return void
	 */
	 public function addTypeid(\Justorange\JoBrewerytour\Domain\Model\Type $typeid) {
	 	if($this->typeid == NULL) {
            $this->typeid = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
      	}
	 	$this->typeid->attach($typeid);
	 }

	 /**
	 * Remove a type
	 *
	 * @param ObjectStorage $typeids
	 * @return void
	 */
	 public function removeTypeid(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $typeids) {
        if(!empty($typeids)) {
            foreach ($typeids as $typeid) {
                $this->typeid->detach($typeid);
            }
        }
	 }

	/**
	 * Returns the freeplaces
	 *
	 * @return integer $freeplaces
	 */
	public function getFreeplaces() {
		return $this->freeplaces;
	}

	/**
	 * Sets the freeplaces
	 *
	 * @param integer $freeplaces
	 * @return void
	 */
	public function setFreeplaces($freeplaces) {
		$this->freeplaces = $freeplaces;
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

}
<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Model;
/**
 * Register
 */
class Register extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * mm from date table
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Justorange\JoBrewerytour\Domain\Model\Date>
	 * @validate NotEmpty
	 */
	protected $dateid = NULL;

	/**
	 * number of participants
	 *
	 * @var integer
	 * @validate NotEmpty
	 */
	protected $participants = 0;

	/**
	 * customerid
	 *
	 * @var string
	 */
	protected $customerid = '';

	/**
	 * creator
	 *
	 * @var integer
	 */
	protected $creator = 0;

	/**
	 * mm from tt_address table
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Justorange\JoBrewerytour\Domain\Model\Address>
	 */
	protected $ttaddressid = NULL;

	/**
	 * subscribedtime
	 *
	 * @var \DateTime
	 */
	protected $subscribedtime = NULL;

	/**
	 * registrationkey
	 *
	 * @var string
	 */
	protected $registrationkey = '';

	/**
	 * salt
	 *
	 * @var string
	 */
	protected $salt = '';

	/**
	 * doitime
	 *
	 * @var \DateTime
	 */
	protected $doitime = '';

	/**
	 * notice
	 *
	 * @var string
	 */
	protected $notice = '';

	/**
	 * commercial
	 *
	 * @var string
	 */
	protected $commercial = '';

	/**
	 * Returns the dateid
	 *
	 * @return \Justorange\JoBrewerytour\Domain\Model\Date $dateid
	 */
	public function getDateid() {
		return $this->dateid;
	}

	/**
	 * Sets the dateid
	 *
	 * @param \Justorange\JoBrewerytour\Domain\Model\Date $dateid
	 * @return void
	 */
	public function setDateid(\Justorange\JoBrewerytour\Domain\Model\Date $dateid) {
		$this->dateid = $dateid;
	}

	/**
	 * Adds a dateid
	 *
	 * @param \Justorange\JoBrewerytour\Domain\Model\Date $dateid
	 * @return void
	 */
	 public function addDateid(\Justorange\JoBrewerytour\Domain\Model\Date $dateid) {
	 	if($this->dateid == NULL) {
            $this->dateid = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
      	}
	 	$this->dateid->attach($dateid);
	 }

	/**
	 * Returns the participants
	 *
	 * @return integer $participants
	 */
	public function getParticipants() {
		return $this->participants;
	}

	/**
	 * Sets the participants
	 *
	 * @param integer $participants
	 * @return void
	 */
	public function setParticipants($participants) {
		$this->participants = $participants;
	}

	/**
	 * Returns the customerid
	 *
	 * @return string $customerid
	 */
	public function getCustomerid() {
		return $this->customerid;
	}

	/**
	 * Sets the customerid
	 *
	 * @param string $customerid
	 * @return void
	 */
	public function setCustomerid($customerid) {
		$this->customerid = $customerid;
	}

	/**
	 * Returns the creator
	 *
	 * @return integer $creator
	 */
	public function getCreator() {
		return $this->creator;
	}

	/**
	 * Sets the creator
	 *
	 * @param integer $creator
	 * @return void
	 */
	public function setCreator($creator) {
		$this->creator = $creator;
	}

	/**
	 * Returns the ttaddressid
	 *
	 * @return \Justorange\JoBrewerytour\Domain\Model\Address $ttaddressid
	 */
	public function getTtaddressid() {
		return $this->ttaddressid;
	}

	/**
	 * Sets the ttaddressid
	 *
	 * @param \Justorange\JoBrewerytour\Domain\Model\Address $ttaddressid
	 * @return void
	 */
	public function setTtaddressid(\Justorange\JoBrewerytour\Domain\Model\Address $ttaddressid) {
		$this->ttaddressid = $ttaddressid;
	}

	/**
	 * Adds a ttaddressid
	 *
	 * @param \Justorange\JoBrewerytour\Domain\Model\Address $ttaddressid
	 * @return void
	 */
	 public function addTtaddressid(\Justorange\JoBrewerytour\Domain\Model\Address $ttaddressid) {
	 	if($this->ttaddressid == NULL) {
            $this->ttaddressid = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
      	}
	 	$this->ttaddressid->attach($ttaddressid);
	 }


	/**
	 * Returns the subscribedtime
	 *
	 * @return \DateTime $subscribedtime
	 */
	public function getSubscribedtime() {
		return $this->subscribedtime;
	}

	/**
	 * Sets the subscribedtime
	 *
	 * @param \DateTime $subscribedtime
	 * @return void
	 */
	public function setSubscribedtime(\DateTime $subscribedtime) {
		$this->subscribedtime = $subscribedtime;
	}

	/**
	 * Returns the registrationkey
	 *
	 * @return string $registrationkey
	 */
	public function getRegistrationkey() {
		return $this->registrationkey;
	}

	/**
	 * Sets the registrationkey
	 *
	 * @param string $registrationkey
	 * @return void
	 */
	public function setRegistrationkey($registrationkey) {
		$this->registrationkey = $registrationkey;
	}

	/**
	 * Returns the salt
	 *
	 * @return string $salt
	 */
	public function getSalt() {
		return $this->salt;
	}

	/**
	 * Sets the salt
	 *
	 * @param string $salt
	 * @return void
	 */
	public function setSalt($salt) {
		$this->salt = $salt;
	}

	/**
	 * Returns the doitime
	 *
	 * @return string $doitime
	 */
	public function getDoitime() {
		return $this->doitime;
	}

	/**
	 * Sets the doitime
	 *
	 * @param string $doitime
	 * @return void
	 */
	public function setDoitime($doitime) {
		$this->doitime = $doitime;
	}

	/**
	 * Returns the notice
	 *
	 * @return string $notice
	 */
	public function getNotice() {
		return $this->notice;
	}

	/**
	 * Sets the notice
	 *
	 * @param string $notice
	 * @return void
	 */
	public function setNotice($notice) {
		$this->notice = $notice;
	}

	/**
	 * Returns the commercial
	 *
	 * @return integer $commercial
	 */
	public function getCommercial() {
		return $this->commercial;
	}

	/**
	 * Sets the commercial
	 *
	 * @param integer $commercial
	 * @return void
	 */
	public function setCommercial($commercial) {
		$this->commercial = $commercial;
	}
}
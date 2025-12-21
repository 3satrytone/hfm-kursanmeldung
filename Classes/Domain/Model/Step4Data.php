<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Model;

class Step4Data extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * gender
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $gender;

	/**
	 * firstName
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $firstName;

	/**
	 * lastName
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $lastName;

	/**
	 * address
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $address;

	/**
	 * city
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $city;

	/**
	 * zip
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $zip;

	/**
	 * company
	 *
	 * @var string
	 */
	protected $company;

	/**
	 * phone
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $phone;

	/**
	 * email
	 *
	 * @var string
	 * @validate NotEmpty,EmailAddress
	 */
	protected $email;

	/**
	 * emailrp
	 *
	 * @var string
	 * @validate NotEmpty,EmailAddress
	 */
	protected $emailrp;

	/**
	 * privacyhint
	 *
	 * @var integer
	 * @validate NotEmpty
	 */
	protected $privacyhint;

	/**
	 * commercial
	 *
	 * @var integer
	 */
	protected $commercial;

	/**
	 * Returns the gender
	 *
	 * @return integer $gender
	 */
	public function getGender() {
		return $this->gender;
	}

	/**
	 * Sets the gender
	 *
	 * @param integer $gender
	 * @return void
	 */
	public function setGender($gender) {
		$this->gender = $gender;
	}

	/**
	 * Returns the firstName
	 *
	 * @return string $firstName
	 */
	public function getFirstName() {
		return $this->firstName;
	}

	/**
	 * Sets the firstName
	 *
	 * @param string $firstName
	 * @return void
	 */
	public function setFirstName($firstName) {
		$this->firstName = $firstName;
	}

	/**
	 * Returns the lastName
	 *
	 * @return string $lastName
	 */
	public function getLastName() {
		return $this->lastName;
	}

	/**
	 * Sets the lastName
	 *
	 * @param string $lastName
	 * @return void
	 */
	public function setLastName($lastName) {
		$this->lastName = $lastName;
	}

	/**
	 * Returns the address
	 *
	 * @return string $address
	 */
	public function getAddress() {
		return $this->address;
	}

	/**
	 * Sets the address
	 *
	 * @param string $address
	 * @return void
	 */
	public function setAddress($address) {
		$this->address = $address;
	}

	/**
	 * Returns the city
	 *
	 * @return string $city
	 */
	public function getCity() {
		return $this->city;
	}

	/**
	 * Sets the city
	 *
	 * @param string $city
	 * @return void
	 */
	public function setCity($city) {
		$this->city = $city;
	}

	/**
	 * Returns the zip
	 *
	 * @return string $zip
	 */
	public function getZip() {
		return $this->zip;
	}

	/**
	 * Sets the zip
	 *
	 * @param string $zip
	 * @return void
	 */
	public function setZip($zip) {
		$this->zip = $zip;
	}

	/**
	 * Returns the company
	 *
	 * @return string $company
	 */
	public function getCompany() {
		return $this->company;
	}

	/**
	 * Sets the company
	 *
	 * @param string $company
	 * @return void
	 */
	public function setCompany($company) {
		$this->company = $company;
	}

	/**
	 * Returns the phone
	 *
	 * @return string $phone
	 */
	public function getPhone() {
		return $this->phone;
	}

	/**
	 * Sets the phone
	 *
	 * @param string $phone
	 * @return void
	 */
	public function setPhone($phone) {
		$this->phone = $phone;
	}

	/**
	 * Returns the email
	 *
	 * @return string $email
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * Sets the email
	 *
	 * @param string $email
	 * @return void
	 */
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	 * Returns the emailrp
	 *
	 * @return string $emailrp
	 */
	public function getEmailrp() {
		return $this->emailrp;
	}

	/**
	 * Sets the emailrp
	 *
	 * @param string $emailrp
	 * @return void
	 */
	public function setEmailrp($emailrp) {
		$this->emailrp = $emailrp;
	}

	/**
	 * Returns the privacyhint
	 *
	 * @return string $privacyhint
	 */
	public function getPrivacyhint() {
		return $this->privacyhint;
	}

	/**
	 * Sets the privacyhint
	 *
	 * @param string $privacyhint
	 * @return void
	 */
	public function setPrivacyhint($privacyhint) {
		$this->privacyhint = $privacyhint;
	}

	/**
	 * Returns the commercial
	 *
	 * @return string $commercial
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
?>
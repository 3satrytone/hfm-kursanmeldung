<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Address extends AbstractEntity {
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
	protected $firstName = NULL;

	/**
	 * lastName
	 *
	 * @var string
	 */
	protected $lastName = NULL;

	/**
	 * email
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $email = NULL;

	/**
	 * phone
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $phone = NULL;

	/**
	 * address
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $address = NULL;

	/**
	 * company
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $company = NULL;

	/**
	 * city
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $city = NULL;

	/**
	 * zip
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $zip = NULL;

	/**
	 * Returns the gender
	 *
	 * @return string $gender
	 */
	public function getGender() {
		return $this->gender;
	}

	/**
	 * Sets the gender
	 *
	 * @param string $gender
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
	public function setLastName( $lastName) {
		$this->lastName = $lastName;
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
	 * Returns the tstamp
	 *
	 * @return string $tstamp
	 */
	public function getTstamp() {
		return $this->tstamp;
	}

	/**
	 * Sets the tstamp
	 *
	 * @param string $tstamp
	 * @return void
	 */
	public function setTstamp($tstamp) {
		$this->tstamp = $tstamp;
	}

}
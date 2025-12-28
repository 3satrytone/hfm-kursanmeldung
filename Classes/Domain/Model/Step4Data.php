<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Model;

class Step4Data extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * gender
     *
     * @var string
     * @validate NotEmpty
     */
    protected string $gender;

    /**
     * firstName
     *
     * @var string
     * @validate NotEmpty
     */
    protected string $firstName;

    /**
     * lastName
     *
     * @var string
     * @validate NotEmpty
     */
    protected string $lastName;

    /**
     * address
     *
     * @var string
     * @validate NotEmpty
     */
    protected string $address;

    /**
     * city
     *
     * @var string
     * @validate NotEmpty
     */
    protected string $city;

    /**
     * zip
     *
     * @var string
     * @validate NotEmpty
     */
    protected string $zip;

    /**
     * company
     *
     * @var string
     */
    protected string $company;

    /**
     * phone
     *
     * @var string
     * @validate NotEmpty
     */
    protected string $phone;

    /**
     * email
     *
     * @var string
     * @validate NotEmpty,EmailAddress
     */
    protected string $email;

    /**
     * emailrp
     *
     * @var string
     * @validate NotEmpty,EmailAddress
     */
    protected string $emailrp;

    /**
     * privacyhint
     *
     * @var int
     * @validate NotEmpty
     */
    protected int $privacyhint;

    /**
     * commercial
     *
     * @var int
     */
    protected int $commercial;

    /**
     * Returns the gender
     *
     * @return string $gender
     */
    public function getGender(): string
    {
        return $this->gender;
    }

    /**
     * Sets the gender
     *
     * @param string $gender
     * @return void
     */
    public function setGender(string $gender): void
    {
        $this->gender = $gender;
    }

    /**
     * Returns the firstName
     *
     * @return string $firstName
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * Sets the firstName
     *
     * @param string $firstName
     * @return void
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * Returns the lastName
     *
     * @return string $lastName
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * Sets the lastName
     *
     * @param string $lastName
     * @return void
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * Returns the address
     *
     * @return string $address
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * Sets the address
     *
     * @param string $address
     * @return void
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * Returns the city
     *
     * @return string $city
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * Sets the city
     *
     * @param string $city
     * @return void
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * Returns the zip
     *
     * @return string $zip
     */
    public function getZip(): string
    {
        return $this->zip;
    }

    /**
     * Sets the zip
     *
     * @param string $zip
     * @return void
     */
    public function setZip(string $zip): void
    {
        $this->zip = $zip;
    }

    /**
     * Returns the company
     *
     * @return string $company
     */
    public function getCompany(): string
    {
        return $this->company;
    }

    /**
     * Sets the company
     *
     * @param string $company
     * @return void
     */
    public function setCompany(string $company): void
    {
        $this->company = $company;
    }

    /**
     * Returns the phone
     *
     * @return string $phone
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * Sets the phone
     *
     * @param string $phone
     * @return void
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * Returns the email
     *
     * @return string $email
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Sets the email
     *
     * @param string $email
     * @return void
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * Returns the emailrp
     *
     * @return string $emailrp
     */
    public function getEmailrp(): string
    {
        return $this->emailrp;
    }

    /**
     * Sets the emailrp
     *
     * @param string $emailrp
     * @return void
     */
    public function setEmailrp(string $emailrp): void
    {
        $this->emailrp = $emailrp;
    }

    /**
     * Returns the privacyhint
     *
     * @return int $privacyhint
     */
    public function getPrivacyhint(): int
    {
        return $this->privacyhint;
    }

    /**
     * Sets the privacyhint
     *
     * @param int $privacyhint
     * @return void
     */
    public function setPrivacyhint(int $privacyhint): void
    {
        $this->privacyhint = $privacyhint;
    }

    /**
     * Returns the commercial
     *
     * @return int $commercial
     */
    public function getCommercial(): int
    {
        return $this->commercial;
    }

    /**
     * Sets the commercial
     *
     * @param int $commercial
     * @return void
     */
    public function setCommercial(int $commercial): void
    {
        $this->commercial = $commercial;
    }
}

?>
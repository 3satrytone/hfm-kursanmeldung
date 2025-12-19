<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Step1Data extends AbstractEntity {

	/**
	 * gender
	 *
	 * @var integer
	 * @Extbase\Validate("NotEmpty")
	 */
	protected $gender;

	/**
	 * title
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * firstName
	 *
	 * @var string
	 * @Extbase\Validate("NotEmpty")
	 */
	protected $firstName;

	/**
	 * lastName
	 *
	 * @var string
	 * @Extbase\Validate("NotEmpty")
	 */
	protected $lastName;

	/**
	 * birthday
	 *
	 * @var string
	 * @Extbase\Validate("NotEmpty")
	 */
	protected $birthday;

	/**
	 * nationality
	 *
	 * @var string
	 * @Extbase\Validate("NotEmpty")
	 */
	protected $nationality;

	/**
	 * address
	 *
	 * @var string
	 * @Extbase\Validate("NotEmpty")
	 */
	protected $address;

	/**
	 * houseno
	 *
	 * @var string
	 * @Extbase\Validate("NotEmpty")
	 */
	protected $houseno;

	/**
	 * addressadd
	 *
	 * @var string
	 */
	protected $addressadd;

	/**
	 * city
	 *
	 * @var string
	 * @Extbase\Validate("NotEmpty")
	 */
	protected $city;

	/**
	 * zip
	 *
	 * @var string
	 * @Extbase\Validate("NotEmpty")
	 */
	protected $zip;

	/**
	 * country
	 *
	 * @var integer
	 * @Extbase\Validate("NotEmpty")
	 */
	protected $country;

	/**
	 * phone
	 *
	 * @var string
	 */
	protected $phone;

	/**
	 * mobile
	 *
	 * @var string
	 * @Extbase\Validate("NotEmpty")
	 */

	protected $mobile;
	/**
	 * email
	 *
	 * @var string
	 * @Extbase\Validate("NotEmpty"),EmailAddress
	 */
	protected $email;

	/**
	 * emailrp
	 *
	 * @var string
	 * @Extbase\Validate("NotEmpty"),EmailAddress
	 */
	protected $emailrp;

	/**
	 * duo
	 *
	 * @var string
	 */
	protected $duo;

	/**
	 * duosel
	 *
	 * @var string
	 */
	protected $duosel;

	/**
	 * duoname
	 *
	 * @var string
	 */
	protected $duoname;
	
	/**
     * enconf
     *
     * @var integer
     */
    protected $enconf;
	
	/**
     * enuid
     *
     * @var array
     */
    protected $enuid;

	/**
     * enname
     *
     * @var string
     */
    protected $enname;
	
	/**
     * entn
     *
     * @var string
     */
    protected $entn;

    /**
     * enfirstn
     *
     * @var array
     */
    protected $enfirstn;

    /**
     * enlastn
     *
     * @var array
     */
    protected $enlastn;

    /**
     * eninstru
     *
     * @var array
     */
    protected $eninstru;

    /**
     * engebdate
     *
     * @var array
     */
    protected $engebdate;

    /**
     * ennatio
     *
     * @var array
     */
    protected $ennatio;

    /**
     * engrdate
     *
     * @var string
     */
    protected $engrdate;
    
	/**
     * entype
     *
     * @var string
     */
    protected $entype;
    
	/**
     * engrplace
     *
     * @var string
     */
    protected $engrplace;
	
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
	 * Returns the title
	 *
	 * @return string $title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Sets the title
	 *
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
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
		$this->firstName = ucfirst ($firstName);
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
		$this->lastName = ucfirst ($lastName);
	}

	/**
	 * Returns the birthday
	 *
	 * @return string $birthday
	 */
	public function getBirthday() {
		return $this->birthday;
	}

	/**
	 * Sets the birthday
	 *
	 * @param string $birthday
	 * @return void
	 */
	public function setBirthday($birthday) {
		$this->birthday = $birthday;
	}

	/**
	 * Returns the nationality
	 *
	 * @return string $nationality
	 */
	public function getNationality() {
		return $this->nationality;
	}

	/**
	 * Sets the nationality
	 *
	 * @param string $nationality
	 * @return void
	 */
	public function setNationality($nationality) {
		$this->nationality = $nationality;
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
	 * Returns the houseno
	 *
	 * @return string $houseno
	 */
	public function getHouseno() {
		return $this->houseno;
	}

	/**
	 * Sets the houseno
	 *
	 * @param string $houseno
	 * @return void
	 */
	public function setHouseno($houseno) {
		$this->houseno = $houseno;
	}

		/**
	 * Returns the addressadd
	 *
	 * @return string $addressadd
	 */
	public function getAddressadd() {
		return $this->addressadd;
	}

	/**
	 * Sets the addressadd
	 *
	 * @param string $addressadd
	 * @return void
	 */
	public function setAddressadd($addressadd) {
		$this->addressadd = $addressadd;
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
	 * Returns the country
	 *
	 * @return string $country
	 */
	public function getCountry() {
		return $this->country;
	}

	/**
	 * Sets the country
	 *
	 * @param string $country
	 * @return void
	 */
	public function setCountry($country) {
		$this->country = $country;
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
	 * Returns the mobile
	 *
	 * @return string $mobile
	 */
	public function getMobile() {
		return $this->mobile;
	}

	/**
	 * Sets the mobile
	 *
	 * @param string $mobile
	 * @return void
	 */
	public function setMobile($mobile) {
		$this->mobile = $mobile;
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
	 * Returns the duo
	 *
	 * @return string $duo
	 */
	public function getDuo() {
		return $this->duo;
	}

	/**
	 * Sets the duo
	 *
	 * @param string $duo
	 * @return void
	 */
	public function setDuo($duo) {
		$this->duo = $duo;
	}

	/**
	 * Returns the duosel
	 *
	 * @return string $duosel
	 */
	public function getDuosel() {
		return $this->duosel;
	}

	/**
	 * Sets the duosel
	 *
	 * @param string $duosel
	 * @return void
	 */
	public function setDuosel($duosel) {
		$this->duosel = $duosel;
	}

	/**
	 * Returns the duoname
	 *
	 * @return string $duoname
	 */
	public function getDuoname() {
		return $this->duoname;
	}

	/**
	 * Sets the duoname
	 *
	 * @param string $duoname
	 * @return void
	 */
	public function setDuoname($duoname) {
		$this->duoname = $duoname;
	}
	
	/**
     * Returns the enconf
     *
     * @return integer $enconf
     */
    public function getEnconf()
    {
        return $this->enconf;
    }

    /**
     * Sets the enconf
     *
     * @param integer $enconf
     * @return void
     */
    public function setEnconf($enconf)
    {
        $this->enconf = $enconf;
    }
	
    /**
     * Returns the entn
     *
     * @return string $entn
     */
    public function getEntn()
    {
        return $this->entn;
    }

    /**
     * Sets the entn
     *
     * @param string $entn
     * @return void
     */
    public function setEntn($entn)
    {
        $this->entn = $entn;
    }

	/**
     * Returns the enuid
     *
     * @return array $enuid
     */
    public function getEnuid()
    {
        return $this->enuid;
    }

    /**
     * Sets the enuid
     *
     * @param array $enuid
     * @return void
     */
    public function setEnuid(array $enuid)
    {
        $this->enuid = $enuid;
    }
	
    /**
     * Returns the enfirstn
     *
     * @return array $enfirstn
     */
    public function getEnfirstn()
    {
        return $this->enfirstn;
    }

    /**
     * Sets the enfirstn
     *
     * @param array $enfirstn
     * @return void
     */
    public function setEnfirstn(array $enfirstn)
    {
        $this->enfirstn = $enfirstn;
    }

    /**
     * Returns the enlastn
     *
     * @return array $enlastn
     */
    public function getEnlastn()
    {
        return $this->enlastn;
    }

    /**
     * Sets the enlastn
     *
     * @param array $enlastn
     * @return void
     */
    public function setEnlastn(array $enlastn)
    {
        $this->enlastn = $enlastn;
    }

    /**
     * Returns the eninstru
     *
     * @return array $eninstru
     */
    public function getEninstru()
    {
        return $this->eninstru;
    }

    /**
     * Sets the eninstru
     *
     * @param array $eninstru
     * @return void
     */
    public function setEninstru(array $eninstru)
    {
        $this->eninstru = $eninstru;
    }

    /**
     * Returns the engebdate
     *
     * @return array $engebdate
     */
    public function getEngebdate()
    {
        return $this->engebdate;
    }

    /**
     * Sets the engebdate
     *
     * @param array $engebdate
     * @return void
     */
    public function setEngebdate(array $engebdate)
    {
        $this->engebdate = $engebdate;
    }

    /**
     * Returns the ennatio
     *
     * @return array $ennatio
     */
    public function getEnnatio()
    {
        return $this->ennatio;
    }

    /**
     * Sets the ennatio
     *
     * @param array $ennatio
     * @return void
     */
    public function setEnnatio(array $ennatio)
    {
        $this->ennatio = $ennatio;
    }

    /**
     * Returns the engrdate
     *
     * @return string $engrdate
     */
    public function getEngrdate()
    {
        return $this->engrdate;
    }

    /**
     * Sets the engrdate
     *
     * @param string $engrdate
     * @return void
     */
    public function setEngrdate($engrdate)
    {
        $this->engrdate = $engrdate;
    }

    /**
     * Returns the entype
     *
     * @return string $entype
     */
    public function getEntype()
    {
        return $this->entype;
    }

    /**
     * Sets the entype
     *
     * @param string $entype
     * @return void
     */
    public function setEntype($entype)
    {
        $this->entype = $entype;
    }

    /**
     * Returns the engrplace
     *
     * @return string $engrplace
     */
    public function getEngrplace()
    {
        return $this->engrplace;
    }

    /**
     * Sets the engrplace
     *
     * @param string $engrplace
     * @return void
     */
    public function setEngrplace($engrplace)
    {
        $this->engrplace = $engrplace;
    }
	
    /**
     * Returns the enname
     *
     * @return string $enname
     */
    public function getEnname()
    {
        return $this->enname;
    }

    /**
     * Sets the enname
     *
     * @param string $enname
     * @return void
     */
    public function setEnname($enname)
    {
        $this->enname = $enname;
    }
}
?>
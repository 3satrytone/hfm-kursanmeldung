<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Model;

use TYPO3\CMS\Extbase\Annotation\Validate;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Step1Data extends AbstractEntity {

    #[Validate(['validator' => 'NotEmpty'])]
	protected int $gender;

	protected string $title;

    #[Validate(['validator' => 'NotEmpty'])]
	protected string $firstName;

    #[Validate(['validator' => 'NotEmpty'])]
	protected string $lastName;

    #[Validate(['validator' => 'NotEmpty'])]
	protected string $birthday;

    #[Validate(['validator' => 'NotEmpty'])]
	protected string $nationality;

    #[Validate(['validator' => 'NotEmpty'])]
	protected string $address;

    #[Validate(['validator' => 'NotEmpty'])]
	protected string $houseno;

	protected string $addressadd;

    #[Validate(['validator' => 'NotEmpty'])]
	protected string $city;

    #[Validate(['validator' => 'NotEmpty'])]
	protected string $zip;

    #[Validate(['validator' => 'NotEmpty'])]
	protected string $country;

	protected string $phone;

    #[Validate(['validator' => 'NotEmpty'])]
	protected string $mobile;

    #[Validate(['validator' => 'NotEmpty'])]
    #[Validate(['validator' => 'EmailAddress'])]
	protected string $email;

    #[Validate(['validator' => 'NotEmpty'])]
    #[Validate(['validator' => 'EmailAddress'])]
	protected string $emailrp;

	protected string $duo;

	protected string $duosel;

	protected string $duoname;

    protected ?int $enconf;
	
    protected array $enuid;

    protected string $enname;
	
    protected string $entn;

    protected array $enfirstn;

    protected array $enlastn;

    protected array $eninstru;

    protected array $engebdate;

    protected array $ennatio;

    protected string $engrdate;
    
    protected string $entype;

    protected string $engrplace;

    public function __construct()
    {
        $this->enconf = 0;
    }

    public function initializeObject()
    {
        $this->enconf = $this->enconf ?? 0;
    }

    /**
     * @return int
     */
	public function getGender(): int
    {
		return $this->gender;
	}

    /**
     * @param int $gender
     * @return void
     */
	public function setGender(int $gender): void
    {
		$this->gender = $gender;
	}

    /**
     * @return string
     */
	public function getTitle(): string
    {
		return $this->title;
	}

    /**
     * @param string $title
     * @return void
     */
	public function setTitle(string $title): void
    {
		$this->title = $title;
	}

    /**
     * @return string
     */
	public function getFirstName(): string
    {
		return $this->firstName;
	}

    /**
     * @param string $firstName
     * @return void
     */
	public function setFirstName(string $firstName): void
    {
		$this->firstName = ucfirst ($firstName);
	}

    /**
     * @return string
     */
	public function getLastName(): string
    {
		return $this->lastName;
	}

    /**
     * @param string $lastName
     * @return void
     */
	public function setLastName(string $lastName): void
    {
		$this->lastName = ucfirst ($lastName);
	}

    /**
     * @return string
     */
	public function getBirthday(): string
    {
		return $this->birthday;
	}

    /**
     * @param string $birthday
     * @return void
     */
	public function setBirthday(string $birthday): void
    {
		$this->birthday = $birthday;
	}

    /**
     * @return string
     */
	public function getNationality(): string
    {
		return $this->nationality;
	}

    /**
     * @param string $nationality
     * @return void
     */
	public function setNationality(string $nationality): void
    {
		$this->nationality = $nationality;
	}

    /**
     * @return string
     */
	public function getAddress(): string
    {
		return $this->address;
	}

    /**
     * @param string $address
     * @return void
     */
	public function setAddress(string $address): void
    {
		$this->address = $address;
	}

    /**
     * @return string
     */
	public function getHouseno(): string
    {
		return $this->houseno;
	}

    /**
     * @param string $houseno
     * @return void
     */
	public function setHouseno(string $houseno): void
    {
		$this->houseno = $houseno;
	}

    /**
     * @return string
     */
	public function getAddressadd(): string
    {
		return $this->addressadd;
	}

    /**
     * @param string $addressadd
     * @return void
     */
	public function setAddressadd(string $addressadd): void
    {
		$this->addressadd = $addressadd;
	}

    /**
     * @return string
     */
	public function getCity(): string
    {
		return $this->city;
	}

    /**
     * @param string $city
     * @return void
     */
	public function setCity(string $city): void
    {
		$this->city = $city;
	}

    /**
     * @return string
     */
	public function getZip(): string
    {
		return $this->zip;
	}

    /**
     * @param string $zip
     * @return void
     */
	public function setZip(string $zip): void
    {
		$this->zip = $zip;
	}

    /**
     * @return string
     */
	public function getCountry(): string
    {
		return $this->country;
	}

    /**
     * @param string $country
     * @return void
     */
	public function setCountry(string $country): void
    {
		$this->country = $country;
	}

    /**
     * @return string
     */
	public function getPhone(): string
    {
		return $this->phone;
	}

    /**
     * @param string $phone
     * @return void
     */
	public function setPhone(string $phone): void
    {
		$this->phone = $phone;
	}

    /**
     * @return string
     */
	public function getMobile(): string
    {
		return $this->mobile;
	}

    /**
     * @param string $mobile
     * @return void
     */
	public function setMobile(string $mobile): void
    {
		$this->mobile = $mobile;
	}

    /**
     * @return string
     */
	public function getEmail(): string
    {
		return $this->email;
	}

    /**
     * @param string $email
     * @return void
     */
	public function setEmail(string $email): void
    {
		$this->email = $email;
	}

    /**
     * @return string
     */
	public function getEmailrp(): string
    {
		return $this->emailrp;
	}

    /**
     * @param string $emailrp
     * @return void
     */
	public function setEmailrp(string $emailrp): void
    {
		$this->emailrp = $emailrp;
	}

    /**
     * @return string
     */
	public function getDuo(): string
    {
		return $this->duo;
	}

    /**
     * @param string $duo
     * @return void
     */
	public function setDuo(string $duo): void
    {
		$this->duo = $duo;
	}

    /**
     * @return string
     */
	public function getDuosel(): string
    {
		return $this->duosel;
	}

    /**
     * @param string $duosel
     * @return void
     */
	public function setDuosel(string $duosel): void
    {
		$this->duosel = $duosel;
	}

    /**
     * @return string
     */
	public function getDuoname(): string
    {
		return $this->duoname;
	}

    /**
     * @param string $duoname
     * @return void
     */
	public function setDuoname(string $duoname): void
    {
		$this->duoname = $duoname;
	}

    /**
     * @return int|null
     */
    public function getEnconf(): ?int
    {
        return $this->enconf;
    }

    /**
     * @param int|null $enconf
     * @return void
     */
    public function setEnconf(?int $enconf): void
    {
        $this->enconf = $enconf;
    }

    /**
     * @return string
     */
    public function getEntn(): string
    {
        return $this->entn;
    }

    /**
     * @param string $entn
     * @return void
     */
    public function setEntn(string $entn): void
    {
        $this->entn = $entn;
    }

    /**
     * @return array
     */
    public function getEnuid(): array
    {
        return $this->enuid;
    }

    /**
     * @param array $enuid
     * @return void
     */
    public function setEnuid(array $enuid): void
    {
        $this->enuid = $enuid;
    }

    /**
     * @return array
     */
    public function getEnfirstn(): array
    {
        return $this->enfirstn;
    }

    /**
     * @param array $enfirstn
     * @return void
     */
    public function setEnfirstn(array $enfirstn): void
    {
        $this->enfirstn = $enfirstn;
    }

    /**
     * @return array
     */
    public function getEnlastn(): array
    {
        return $this->enlastn;
    }

    /**
     * @param array $enlastn
     * @return void
     */
    public function setEnlastn(array $enlastn): void
    {
        $this->enlastn = $enlastn;
    }

    /**
     * @return array
     */
    public function getEninstru(): array
    {
        return $this->eninstru;
    }

    /**
     * @param array $eninstru
     * @return void
     */
    public function setEninstru(array $eninstru): void
    {
        $this->eninstru = $eninstru;
    }

    /**
     * @return array
     */
    public function getEngebdate(): array
    {
        return $this->engebdate;
    }

    /**
     * @param array $engebdate
     * @return void
     */
    public function setEngebdate(array $engebdate): void
    {
        $this->engebdate = $engebdate;
    }

    /**
     * @return array
     */
    public function getEnnatio(): array
    {
        return $this->ennatio;
    }

    /**
     * @param array $ennatio
     * @return void
     */
    public function setEnnatio(array $ennatio): void
    {
        $this->ennatio = $ennatio;
    }

    /**
     * @return string
     */
    public function getEngrdate(): string
    {
        return $this->engrdate;
    }

    /**
     * @param string $engrdate
     * @return void
     */
    public function setEngrdate(string $engrdate): void
    {
        $this->engrdate = $engrdate;
    }

    /**
     * @return string
     */
    public function getEntype(): string
    {
        return $this->entype;
    }

    /**
     * @param string $entype
     * @return void
     */
    public function setEntype(string $entype): void
    {
        $this->entype = $entype;
    }

    /**
     * @return string
     */
    public function getEngrplace(): string
    {
        return $this->engrplace;
    }

    /**
     * @param string $engrplace
     * @return void
     */
    public function setEngrplace(string $engrplace): void
    {
        $this->engrplace = $engrplace;
    }

    /**
     * @return string
     */
    public function getEnname(): string
    {
        return $this->enname;
    }

    /**
     * @param string $enname
     * @return void
     */
    public function setEnname(string $enname): void
    {
        $this->enname = $enname;
    }
}
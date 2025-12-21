<?php

namespace Hfm\Kursanmeldung\Domain\Model;

use TYPO3\CMS\Core\Resource\Enum\DuplicationBehavior;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Annotation\FileUpload;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Step2Data extends AbstractEntity
{
    /**
     * tnaction
     *
     * @var integer
     */
    protected $tnaction;

    /**
     * studentship
     *
     * @var integer
     */
    protected $studentship;

    /**
     * studystat
     *
     * @var integer
     */
    protected $studystat;

    /**
     * matrikel
     *
     * @var string
     */
    protected $matrikel;

    /**
     * programm
     *
     * @var string
     */
    protected $programm;

    /**
     * orchesterstudio
     *
     * @var string
     */
    protected $orchesterstudio;

    /**
     * zahlungsart
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     */
    protected $zahlungsart;

    /**
     * zahlungstermin
     *
     * @var string
     */
    protected $zahlungstermin;

    /**
     * hotel
     *
     * @var integer
     */
    protected $hotel;

    /**
     * room
     *
     * @var string
     */
    protected $room;

    /**
     * roomwith
     *
     * @var string
     */
    protected $roomwith;

    /**
     * roomfrom
     *
     * @var string
     */
    protected $roomfrom;

    /**
     * roomto
     *
     * @var string
     */
    protected $roomto;

    /**
     * link
     *
     * @var string
     */
    protected $link;

    /**
     * youtube
     *
     * @var string
     */
    protected $youtube;

    /**
     * A collection of files.
     * @var ObjectStorage<FileReference>
     */
    #[FileUpload([
        'validation' => [
            'required' => false,
            'fileSize' => ['minimum' => '0K', 'maximum' => '5M'],
            'mimeType' => [
                'allowedMimeTypes' => ['image/jpg','image/jpeg','image/gif'],
                'ignoreFileExtensionCheck' => false,
                'notAllowedMessage' => 'LLL:EXT:kursanmeldung/Resources/Private/Language/locallang_be.xlf:upload.notallowed',
                'invalidExtensionMessage' => 'LLL:EXT:kursanmeldung/Resources/Private/Language/locallang_be.xlf:upload.invalidextension',
            ],
            'fileExtension' => ['allowedFileExtensions' => ['jpg', 'jpeg', 'gif']],
        ],
        'uploadFolder' => '1:/user_upload/hfm_kursanmeldung/',
        'addRandomSuffix' => true,
        'duplicationBehavior' => DuplicationBehavior::RENAME,
    ])]
    protected ObjectStorage $download;

    /**
     * vita
     *
     * @var string
     */
    protected $vita;

    /**
     * comment
     *
     * @var string
     */
    protected $comment;

    // When using ObjectStorages, it is vital to initialize these.
    public function __construct()
    {
        $this->download = new ObjectStorage();
    }

    /**
     * Called again with initialize object, as fetching an entity from the DB does not use the constructor
     */
    public function initializeObject(): void
    {
        $this->download = $this->download ?? new ObjectStorage();
    }

    /**
     * Returns the tnaction
     *
     * @return integer $tnaction
     */
    public function getTnaction()
    {
        return $this->tnaction;
    }

    /**
     * Sets the integer
     *
     * @param integer $tnaction
     * @return void
     */
    public function setTnaction($tnaction)
    {
        $this->tnaction = $tnaction;
    }

    /**
     * Returns the studentship
     *
     * @return integer $studentship
     */
    public function getStudentship()
    {
        return $this->studentship;
    }

    /**
     * Sets the integer
     *
     * @param integer $studentship
     * @return void
     */
    public function setStudentship($studentship)
    {
        $this->studentship = $studentship;
    }

    /**
     * Returns the studystat
     *
     * @return integer $studystat
     */
    public function getStudystat()
    {
        return $this->studystat;
    }

    /**
     * Sets the integer
     *
     * @param integer $studystat
     * @return void
     */
    public function setStudystat($studystat)
    {
        $this->studystat = $studystat;
    }

    /**
     * Returns the matrikel
     *
     * @return string $matrikel
     */
    public function getMatrikel()
    {
        return $this->matrikel;
    }

    /**
     * Sets the string
     *
     * @param string $matrikel
     * @return void
     */
    public function setMatrikel($matrikel)
    {
        $this->matrikel = $matrikel;
    }

    /**
     * Returns the programm
     *
     * @return string $programm
     */
    public function getProgramm()
    {
        return $this->programm;
    }

    /**
     * Sets the string
     *
     * @param string $programm
     * @return void
     */
    public function setProgramm($programm)
    {
        $this->programm = $programm;
    }

    /**
     * Returns the orchesterstudio
     *
     * @return string $orchesterstudio
     */
    public function getOrchesterstudio()
    {
        return $this->orchesterstudio;
    }

    /**
     * Sets the string
     *
     * @param string $orchesterstudio
     * @return void
     */
    public function setOrchesterstudio($orchesterstudio)
    {
        $this->orchesterstudio = $orchesterstudio;
    }

    /**
     * Returns the zahlungsart
     *
     * @return string $zahlungsart
     */
    public function getZahlungsart()
    {
        return $this->zahlungsart;
    }

    /**
     * Sets the string
     *
     * @param string $zahlungsart
     * @return void
     */
    public function setZahlungsart($zahlungsart)
    {
        $this->zahlungsart = $zahlungsart;
    }

    /**
     * Returns the zahlungstermin
     *
     * @return string $zahlungstermin
     */
    public function getZahlungstermin()
    {
        return $this->zahlungstermin;
    }

    /**
     * Sets the string
     *
     * @param string $zahlungstermin
     * @return void
     */
    public function setZahlungstermin($zahlungstermin)
    {
        $this->zahlungstermin = $zahlungstermin;
    }

    /**
     * Returns the hotel
     *
     * @return integer $hotel
     */
    public function getHotel()
    {
        return $this->hotel;
    }

    /**
     * Sets the integer
     *
     * @param integer $hotel
     * @return void
     */
    public function setHotel($hotel)
    {
        $this->hotel = $hotel;
    }

    /**
     * Returns the room
     *
     * @return string $room
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * Sets the room
     *
     * @param string $room
     * @return void
     */
    public function setRoom($room)
    {
        $this->room = $room;
    }

    /**
     * Returns the roomwith
     *
     * @return string $roomwith
     */
    public function getRoomwith()
    {
        return $this->roomwith;
    }

    /**
     * Sets the string
     *
     * @param string $roomwith
     * @return void
     */
    public function setRoomwith($roomwith)
    {
        $this->roomwith = $roomwith;
    }

    /**
     * Returns the roomfrom
     *
     * @return string $roomfrom
     */
    public function getRoomfrom()
    {
        return $this->roomfrom;
    }

    /**
     * Sets the string
     *
     * @param string $roomfrom
     * @return void
     */
    public function setRoomfrom($roomfrom)
    {
        $this->roomfrom = $roomfrom;
    }

    /**
     * Returns the roomto
     *
     * @return string $roomto
     */
    public function getRoomto()
    {
        return $this->roomto;
    }

    /**
     * Sets the string
     *
     * @param string $roomto
     * @return void
     */
    public function setRoomto($roomto)
    {
        $this->roomto = $roomto;
    }

    /**
     * Returns the link
     *
     * @return string $link
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Sets the string
     *
     * @param string $link
     * @return void
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * Returns the youtube
     *
     * @return string $youtube
     */
    public function getYoutube()
    {
        return $this->youtube;
    }

    /**
     * Sets the string
     *
     * @param string $youtube
     * @return void
     */
    public function setYoutube($youtube)
    {
        $this->youtube = $youtube;
    }

    /**
     * @return ObjectStorage<FileReference>
     */
    public function getDownload(): ObjectStorage
    {
        return $this->download;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<FileReference> $files
     * @return void
     */
    public function setDownload(ObjectStorage $files): void
    {
        $this->download = $files;
    }

    /**
     * Returns the vita
     *
     * @return string $vita
     */
    public function getVita()
    {
        return $this->vita;
    }

    /**
     * Sets the vita
     *
     * @param string $vita
     * @return void
     */
    public function setVita($vita)
    {
        $this->vita = $vita;
    }

    /**
     * Returns the comment
     *
     * @return string $comment
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Sets the comment
     *
     * @param string $comment
     * @return void
     */
    public function setComment($comment)
    {
        $this->comment = strip_tags($comment);
    }

}

?>
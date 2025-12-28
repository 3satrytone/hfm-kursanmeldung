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
     * @var int
     */
    protected int $tnaction = 0;

    /**
     * studentship
     *
     * @var int
     */
    protected ?int $studentship = 0;

    /**
     * studystat
     *
     * @var int
     */
    protected ?int $studystat;

    /**
     * matrikel
     *
     * @var string
     */
    protected string $matrikel = '';

    /**
     * programm
     *
     * @var string
     */
    protected string $programm = '';

    /**
     * @var string|null
     */
    protected ?string $orchesterstudio = null;

    /**
     * zahlungsart
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     */
    protected string $zahlungsart = '';

    /**
     * zahlungstermin
     *
     * @var string
     */
    protected string $zahlungstermin = '';

    /**
     * @var int
     */
    protected int $hotel = 0;

    /**
     * room
     *
     * @var string
     */
    protected string $room = '';

    /**
     * roomwith
     *
     * @var string
     */
    protected string $roomwith = '';

    /**
     * roomfrom
     *
     * @var string
     */
    protected string $roomfrom = '';

    /**
     * roomto
     *
     * @var string
     */
    protected string $roomto = '';

    /**
     * link
     *
     * @var string
     */
    protected string $link = '';

    /**
     * youtube
     *
     * @var string
     */
    protected string $youtube = '';

    /**
     * A collection of files.
     * @var ObjectStorage<FileReference>
     */
    #[FileUpload([
        'validation' => [
            'required' => false,
            'fileSize' => ['minimum' => '0K', 'maximum' => '5M'],
            'mimeType' => [
                'allowedMimeTypes' => [
                    'image/jpg',
                    'image/jpeg',
                    'image/gif',
                    'application/pdf',
                    'audio/x-aiff',
                    'audio/wav',
                    'audio/mp4',
                    'video/mp4',
                    'video/mpeg',

                ],
                'ignoreFileExtensionCheck' => false,
                'notAllowedMessage' => 'LLL:EXT:kursanmeldung/Resources/Private/Language/locallang_be.xlf:upload.notallowed',
                'invalidExtensionMessage' => 'LLL:EXT:kursanmeldung/Resources/Private/Language/locallang_be.xlf:upload.invalidextension',
            ],
            'fileExtension' => [
                'allowedFileExtensions' => [
                    'jpg',
                    'jpeg',
                    'gif',
                    'pdf',
                    'wmv',
                    'aiff',
                    'aif',
                    'mpeg',
                    'mpg',
                    'mp4',
                    'mp3',
                    'wav'
                ]
            ],
        ],
        'uploadFolder' => '1:/user_upload/hfm_kursanmeldung/',
        'addRandomSuffix' => true,
        'duplicationBehavior' => DuplicationBehavior::RENAME,
    ])]
    protected ObjectStorage $download;

    /**
     * vita
     *
     * A collection of files.
     * @var FileReference
     */
    #[FileUpload([
        'validation' => [
            'required' => false,
            'fileSize' => ['minimum' => '0K', 'maximum' => '5M'],
            'mimeType' => [
                'allowedMimeTypes' => [
                    'text/plain',
                    'application/pdf',

                ],
                'ignoreFileExtensionCheck' => false,
                'notAllowedMessage' => 'LLL:EXT:kursanmeldung/Resources/Private/Language/locallang_be.xlf:upload.notallowed',
                'invalidExtensionMessage' => 'LLL:EXT:kursanmeldung/Resources/Private/Language/locallang_be.xlf:upload.invalidextension',
            ],
            'fileExtension' => [
                'allowedFileExtensions' => [
                    'pdf',
                    'txt'
                ]
            ],
        ],
        'uploadFolder' => '1:/user_upload/hfm_kursanmeldung/',
        'addRandomSuffix' => true,
        'duplicationBehavior' => DuplicationBehavior::RENAME,
    ])]
    protected ?FileReference $vita = null;

    /**
     * comment
     *
     * @var string
     */
    protected string $comment = '';

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
        $this->vita = $this->vita ?? null;
    }

    /**
     * Returns the tnaction
     *
     * @return int $tnaction
     */
    public function getTnaction(): int
    {
        return $this->tnaction;
    }

    /**
     * Sets the int
     *
     * @param int $tnaction
     * @return void
     */
    public function setTnaction(int $tnaction): void
    {
        $this->tnaction = $tnaction;
    }

    /**
     * @return int|null
     */
    public function getStudentship(): ?int
    {
        return $this->studentship;
    }

    /**
     * @param int|null $studentship
     * @return void
     */
    public function setStudentship(?int $studentship): void
    {
        $this->studentship = $studentship;
    }

    /**
     * @return int|null
     */
    public function getStudystat(): ?int
    {
        return $this->studystat;
    }

    /**
     * @param int|null $studystat
     * @return void
     */
    public function setStudystat(?int $studystat): void
    {
        $this->studystat = $studystat;
    }

    /**
     * Returns the matrikel
     *
     * @return string $matrikel
     */
    public function getMatrikel(): string
    {
        return $this->matrikel;
    }

    /**
     * Sets the string
     *
     * @param string $matrikel
     * @return void
     */
    public function setMatrikel(string $matrikel): void
    {
        $this->matrikel = $matrikel;
    }

    /**
     * Returns the programm
     *
     * @return string $programm
     */
    public function getProgramm(): string
    {
        return $this->programm;
    }

    /**
     * Sets the string
     *
     * @param string $programm
     * @return void
     */
    public function setProgramm(string $programm): void
    {
        $this->programm = $programm;
    }

    /**
     * @return string|null
     */
    public function getOrchesterstudio(): ?string
    {
        return $this->orchesterstudio;
    }

    /**
     * @param string|null $orchesterstudio
     * @return void
     */
    public function setOrchesterstudio(?string $orchesterstudio): void
    {
        $this->orchesterstudio = $orchesterstudio;
    }

    /**
     * Returns the zahlungsart
     *
     * @return string $zahlungsart
     */
    public function getZahlungsart(): string
    {
        return $this->zahlungsart;
    }

    /**
     * Sets the string
     *
     * @param string $zahlungsart
     * @return void
     */
    public function setZahlungsart(string $zahlungsart): void
    {
        $this->zahlungsart = $zahlungsart;
    }

    /**
     * Returns the zahlungstermin
     *
     * @return string $zahlungstermin
     */
    public function getZahlungstermin(): string
    {
        return $this->zahlungstermin;
    }

    /**
     * Sets the string
     *
     * @param string $zahlungstermin
     * @return void
     */
    public function setZahlungstermin(string $zahlungstermin): void
    {
        $this->zahlungstermin = $zahlungstermin;
    }

    /**
     * @return int
     */
    public function getHotel(): int
    {
        return $this->hotel;
    }

    /**
     * @param int
     * @return void
     */
    public function setHotel(int $hotel): void
    {
        $this->hotel = $hotel;
    }

    /**
     * Returns the room
     *
     * @return string $room
     */
    public function getRoom(): string
    {
        return $this->room;
    }

    /**
     * Sets the room
     *
     * @param string $room
     * @return void
     */
    public function setRoom(string $room): void
    {
        $this->room = $room;
    }

    /**
     * Returns the roomwith
     *
     * @return string $roomwith
     */
    public function getRoomwith(): string
    {
        return $this->roomwith;
    }

    /**
     * Sets the string
     *
     * @param string $roomwith
     * @return void
     */
    public function setRoomwith(string $roomwith): void
    {
        $this->roomwith = $roomwith;
    }

    /**
     * Returns the roomfrom
     *
     * @return string $roomfrom
     */
    public function getRoomfrom(): string
    {
        return $this->roomfrom;
    }

    /**
     * Sets the string
     *
     * @param string $roomfrom
     * @return void
     */
    public function setRoomfrom(string $roomfrom): void
    {
        $this->roomfrom = $roomfrom;
    }

    /**
     * Returns the roomto
     *
     * @return string $roomto
     */
    public function getRoomto(): string
    {
        return $this->roomto;
    }

    /**
     * Sets the string
     *
     * @param string $roomto
     * @return void
     */
    public function setRoomto(string $roomto): void
    {
        $this->roomto = $roomto;
    }

    /**
     * Returns the link
     *
     * @return string $link
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param $link
     * @return void
     */
    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    /**
     * @return string
     */
    public function getYoutube(): string
    {
        return $this->youtube;
    }

    /**
     * Sets the string
     *
     * @param string $youtube
     * @return void
     */
    public function setYoutube(string $youtube): void
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
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference|null
     */
    public function getVita(): ?FileReference
    {
        return $this->vita;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference|null $vita
     * @return void
     */
    public function setVita(?FileReference $vita): void
    {
        $this->vita = $vita;
    }

    /**
     * Returns the comment
     *
     * @return string $comment
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * Sets the comment
     *
     * @param string $comment
     * @return void
     */
    public function setComment(string $comment): void
    {
        $this->comment = strip_tags($comment);
    }

}

?>
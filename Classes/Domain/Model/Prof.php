<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Model;

use TYPO3\CMS\Core\Resource\Enum\DuplicationBehavior;
use TYPO3\CMS\Extbase\Annotation\FileUpload;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Property\TypeConverter\FileConverter;
use TYPO3\CMS\Extbase\Annotation\IgnoreValidation;

class Prof extends AbstractEntity
{
    /**
     * @var string
     */
    protected string $name = '';

    /**
     * @var string
     */
    protected string $link = '';

    #[FileUpload([
        'validation' => [
            'required' => false,
            'maxFiles' => 1,
            'fileSize' => ['minimum' => '0K', 'maximum' => '2M'],
            'mimeType' => [
                'allowedMimeTypes' => ['image/jpg','image/jpeg','image/gif'],
                'ignoreFileExtensionCheck' => false,
                'notAllowedMessage' => 'LLL:EXT:kursanmeldung/Resources/Private/Language/locallang_be.xlf:upload.notallowed',
                'invalidExtensionMessage' => 'LLL:EXT:kursanmeldung/Resources/Private/Language/locallang_be.xlf:upload.invalidextension',
            ],
            'imageDimensions' => ['maxWidth' => 4096, 'maxHeight' => 4096],
        ],
        'uploadFolder' => '1:/user_upload/hfm_kursanmeldung/',
        'addRandomSuffix' => true,
        'duplicationBehavior' => DuplicationBehavior::RENAME,
    ])]
    protected ?FileReference $image = null;

    /**
     * @return string
     */
    public function getName(): string { return $this->name; }

    /**
     * @param string $name
     */
    public function setName(string $name): void { $this->name = $name; }

    /**
     * @return string
     */
    public function getLink(): string { return $this->link; }

    /**
     * @param string $link
     */
    public function setLink(string $link): void { $this->link = $link; }

    /**
     * @return ?FileReference
     */
    public function getImage(): ?FileReference { return $this->image; }

    /**
     * @param ?FileReference $image
     */
    public function setImage(?FileReference $image): void { $this->image = $image; }
}

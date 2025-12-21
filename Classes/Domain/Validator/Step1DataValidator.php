<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Validator;


use Hfm\Kursanmeldung\Domain\Model\Step1Data;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

class Step1DataValidator extends AbstractValidator
{
    /**
     * @param mixed $value
     * @return void
     */
    protected function isValid(mixed $value): void
    {
        if (!$value instanceof Step1Data) {
            $errorString = 'The Step1DataValidator can only handle classes '
                . 'of type Hfm\Kursanmeldung\Domain\Validator\Step1Data. '
                . $value::class . ' given instead.';
            $this->addError($errorString, time());
        }

        if (!preg_match('/^\d{4}[-]\d{2}[-]\d{2}$/', $value->getBirthday())) {
            $errorString = LocalizationUtility::translate(
                'step1data.validator.birthday.format',
                'kursanmeldung'
            );
            $this->addErrorForProperty('birthday', $errorString, time());
        }

        if ($value->getEmail() !== $value->getEmailrp()) {
            $errorString = LocalizationUtility::translate(
                'step1data.validator.email.repeat',
                'kursanmeldung'
            );
            $this->addErrorForProperty('email', $errorString, time());
            $this->addErrorForProperty('emailrp', $errorString, time());
        }
    }
}
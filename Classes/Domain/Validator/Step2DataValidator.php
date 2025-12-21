<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Validator;

use Hfm\Kursanmeldung\Domain\Model\Step2Data;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

class Step2DataValidator extends AbstractValidator {
    /**
     * @param mixed $value
     * @return void
     */
    protected function isValid(mixed $value): void
    {
        if (!$value instanceof Step2Data) {
            $errorString = 'The Step2DataValidator can only handle classes '
                . 'of type Hfm\Kursanmeldung\Domain\Validator\Step2Data. '
                . $value::class . ' given instead.';
            $this->addError($errorString, time());
		}

		if ($value->getTnaction() === 0 && trim($value->getProgramm()) === '') {
            $errorString = LocalizationUtility::translate(
                'step2data.validator.programm',
                'kursanmeldung'
            );
            $this->addErrorForProperty('programm', $errorString, time());
		}

		if ($value->getStudystat() == 1 && trim($value->getMatrikel()) == '') {
            $errorString = LocalizationUtility::translate(
					'tx_kursanmeldung_domain_model_kursanmeldung.error.step2data.validator.matrikel',
					'kursanmeldung'
            );
            $this->addErrorForProperty('matrikel', $errorString, time());
		}

		if (!empty($value->getHotel()) && empty($value->getRoom())) {
            $errorString = LocalizationUtility::translate(
					'tx_jokursanmeldung_domain_model_kursanmeldung.error.step2data.validator.room',
					'kursanmeldung'
			);
            $this->addErrorForProperty('room', $errorString, time());
		}
	}
}
?>
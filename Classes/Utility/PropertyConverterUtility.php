<?php

namespace Hfm\Kursanmeldung\Utility;

use Hfm\Kursanmeldung\Constants\Constants;
use TYPO3\CMS\Extbase\Mvc\Controller\Arguments;
use TYPO3\CMS\Extbase\Property\TypeConverter\FloatConverter;
use TYPO3\CMS\Extbase\Property\TypeConverter\ObjectStorageConverter;
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;

class PropertyConverterUtility
{
    /**
     * @param Arguments $arguments
     * @return void
     */
    public function convertArgumentsGebuehren(Arguments $arguments): void
    {
        if(isset($arguments[Constants::GEBUEHREN])) {
            $arguments[Constants::GEBUEHREN]->getPropertyMappingConfiguration()
                ->forProperty('anmeldung')
                ->setTypeConverterOption(
                    FloatConverter::class,
                    FloatConverter::CONFIGURATION_DECIMAL_POINT,
                    ','
                )
                ->setTypeConverterOption(
                    FloatConverter::class,
                    FloatConverter::CONFIGURATION_THOUSANDS_SEPARATOR,
                    '.' // Use '.' as thousands separator
                );
            $arguments[Constants::GEBUEHREN]->getPropertyMappingConfiguration()
                ->forProperty('anmeldungerm')
                ->setTypeConverterOption(
                    FloatConverter::class,
                    FloatConverter::CONFIGURATION_DECIMAL_POINT,
                    ','
                )
                ->setTypeConverterOption(
                    FloatConverter::class,
                    FloatConverter::CONFIGURATION_THOUSANDS_SEPARATOR,
                    '.' // Use '.' as thousands separator
                );
            $arguments[Constants::GEBUEHREN]->getPropertyMappingConfiguration()
                ->forProperty('aktivengeb')
                ->setTypeConverterOption(
                    FloatConverter::class,
                    FloatConverter::CONFIGURATION_DECIMAL_POINT,
                    ','
                )
                ->setTypeConverterOption(
                    FloatConverter::class,
                    FloatConverter::CONFIGURATION_THOUSANDS_SEPARATOR,
                    '.' // Use '.' as thousands separator
                );
            $arguments[Constants::GEBUEHREN]->getPropertyMappingConfiguration()
                ->forProperty('aktivengeberm')
                ->setTypeConverterOption(
                    FloatConverter::class,
                    FloatConverter::CONFIGURATION_DECIMAL_POINT,
                    ','
                )
                ->setTypeConverterOption(
                    FloatConverter::class,
                    FloatConverter::CONFIGURATION_THOUSANDS_SEPARATOR,
                    '.' // Use '.' as thousands separator
                );
            $arguments[Constants::GEBUEHREN]->getPropertyMappingConfiguration()
                ->forProperty('passivgeb')
                ->setTypeConverterOption(
                    FloatConverter::class,
                    FloatConverter::CONFIGURATION_DECIMAL_POINT,
                    ','
                )
                ->setTypeConverterOption(
                    FloatConverter::class,
                    FloatConverter::CONFIGURATION_THOUSANDS_SEPARATOR,
                    '.' // Use '.' as thousands separator
                );
            $arguments[Constants::GEBUEHREN]->getPropertyMappingConfiguration()
                ->forProperty('passivgeberm')
                ->setTypeConverterOption(
                    FloatConverter::class,
                    FloatConverter::CONFIGURATION_DECIMAL_POINT,
                    ','
                )
                ->setTypeConverterOption(
                    FloatConverter::class,
                    FloatConverter::CONFIGURATION_THOUSANDS_SEPARATOR,
                    '.' // Use '.' as thousands separator
                );
        }
    }

    /**
     * @param \TYPO3\CMS\Extbase\Mvc\Controller\Arguments $arguments
     * @return void
     */
    public function convertArgumentsHotel(Arguments $arguments): void
    {
        if(isset($arguments[Constants::HOTEL])) {
            $arguments[Constants::HOTEL]->getPropertyMappingConfiguration()
                ->forProperty('ezpreis')
                ->setTypeConverterOption(
                    FloatConverter::class,
                    FloatConverter::CONFIGURATION_DECIMAL_POINT,
                    ','
                )
                ->setTypeConverterOption(
                    FloatConverter::class,
                    FloatConverter::CONFIGURATION_THOUSANDS_SEPARATOR,
                    '.' // Use '.' as thousands separator
                );
            $arguments[Constants::HOTEL]->getPropertyMappingConfiguration()
                ->forProperty('ezpreiserm')
                ->setTypeConverterOption(
                    FloatConverter::class,
                    FloatConverter::CONFIGURATION_DECIMAL_POINT,
                    ','
                )
                ->setTypeConverterOption(
                    FloatConverter::class,
                    FloatConverter::CONFIGURATION_THOUSANDS_SEPARATOR,
                    '.' // Use '.' as thousands separator
                );
            $arguments[Constants::HOTEL]->getPropertyMappingConfiguration()
                ->forProperty('dzpreis')
                ->setTypeConverterOption(
                    FloatConverter::class,
                    FloatConverter::CONFIGURATION_DECIMAL_POINT,
                    ','
                )
                ->setTypeConverterOption(
                    FloatConverter::class,
                    FloatConverter::CONFIGURATION_THOUSANDS_SEPARATOR,
                    '.' // Use '.' as thousands separator
                );
            $arguments[Constants::HOTEL]->getPropertyMappingConfiguration()
                ->forProperty('dzpreiserm')
                ->setTypeConverterOption(
                    FloatConverter::class,
                    FloatConverter::CONFIGURATION_DECIMAL_POINT,
                    ','
                )
                ->setTypeConverterOption(
                    FloatConverter::class,
                    FloatConverter::CONFIGURATION_THOUSANDS_SEPARATOR,
                    '.' // Use '.' as thousands separator
                );
            $arguments[Constants::HOTEL]->getPropertyMappingConfiguration()
                ->forProperty('dz2preis')
                ->setTypeConverterOption(
                    FloatConverter::class,
                    FloatConverter::CONFIGURATION_DECIMAL_POINT,
                    ','
                )
                ->setTypeConverterOption(
                    FloatConverter::class,
                    FloatConverter::CONFIGURATION_THOUSANDS_SEPARATOR,
                    '.' // Use '.' as thousands separator
                );
            $arguments[Constants::HOTEL]->getPropertyMappingConfiguration()
                ->forProperty('dz2preiserm')
                ->setTypeConverterOption(
                    FloatConverter::class,
                    FloatConverter::CONFIGURATION_DECIMAL_POINT,
                    ','
                )
                ->setTypeConverterOption(
                    FloatConverter::class,
                    FloatConverter::CONFIGURATION_THOUSANDS_SEPARATOR,
                    '.' // Use '.' as thousands separator
                );
        }
    }


    /**
     * @param \TYPO3\CMS\Extbase\Mvc\Controller\Arguments $arguments
     * @return void
     */
    public function convertArgumentsOrte(Arguments $arguments): void
    {
        if(isset($arguments[Constants::ORTE])) {
            $arguments[Constants::ORTE]->getPropertyMappingConfiguration()
                ->forProperty('longi')
                ->setTypeConverterOption(
                    FloatConverter::class,
                    FloatConverter::CONFIGURATION_DECIMAL_POINT,
                    '.'
                )
                ->setTypeConverterOption(
                    FloatConverter::class,
                    FloatConverter::CONFIGURATION_THOUSANDS_SEPARATOR,
                    '' // Use '.' as thousands separator
                );
            $arguments[Constants::ORTE]->getPropertyMappingConfiguration()
                ->forProperty('lati')
                ->setTypeConverterOption(
                    FloatConverter::class,
                    FloatConverter::CONFIGURATION_DECIMAL_POINT,
                    '.'
                )
                ->setTypeConverterOption(
                    FloatConverter::class,
                    FloatConverter::CONFIGURATION_THOUSANDS_SEPARATOR,
                    '' // Use '.' as thousands separator
                );
        }
    }

    /**
     * Configure mapping for Step2Data, specifically the `hotel` property to use ObjectStorageConverter
     * and allow identity-based mapping for contained Hotel objects.
     *
     * Expects incoming data to represent an ObjectStorage of Hotel, e.g. a numeric-keyed array of
     * hotel identifiers or identity arrays. Example acceptable shapes:
     * - step2data[hotel][0][__identity] = 123
     * - step2data[hotel][0] = 123 (will still be processed by child converter)
     *
     * @param Arguments $arguments
     * @return void
     */
    public function convertArgumentsStep2Data(Arguments $arguments): void
    {
        if (isset($arguments[Constants::ACTION_STEP_2_DATA])) {
            $config = $arguments[Constants::ACTION_STEP_2_DATA]->getPropertyMappingConfiguration();
            $config->allowProperties('vita');
        }
    }
}
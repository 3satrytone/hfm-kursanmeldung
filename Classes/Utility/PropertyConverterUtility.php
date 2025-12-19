<?php

namespace Hfm\Kursanmeldung\Utility;

use Hfm\Kursanmeldung\Constants\Constants;
use TYPO3\CMS\Extbase\Mvc\Controller\Arguments;
use TYPO3\CMS\Extbase\Property\TypeConverter\FloatConverter;

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
}
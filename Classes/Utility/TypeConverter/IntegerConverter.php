<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\Utility\TypeConverter;

use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;
use TYPO3\CMS\Extbase\Property\TypeConverter\AbstractTypeConverter;

/**
 * Converter which transforms a simple type to an integer, by simply casting it.
 */
class IntegerConverter extends AbstractTypeConverter
{
    /**
     * @param $source
     * @param string $targetType
     * @param array $convertedChildProperties
     * @param \TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface|null $configuration
     * @return int
     */
    public function convertFrom(
        $source,
        string $targetType,
        array $convertedChildProperties = [],
        ?PropertyMappingConfigurationInterface $configuration = null
    ): int {
        return (int)$source;
    }
}

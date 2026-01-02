<?php

declare(strict_types=1);

defined('TYPO3') or die('Access denied.');

use Hfm\Kursanmeldung\Controller\FrontendController;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\Writer\FileWriter;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

// Define TypoScript as content rendering template
$GLOBALS['TYPO3_CONF_VARS']['FE']['contentRenderingTemplates'][] = 'kursanmeldung/Configuration/TypoScript/';
$GLOBALS['TYPO3_CONF_VARS']['FE']['contentRenderingTemplates'][] = 'kursanmeldung/Configuration/TypoScript/ContentElement/';

// Make the extension configuration accessible
$extensionConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
    \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
);

// Configure view paths for the backend module
ExtensionManagementUtility::addTypoScriptSetup(
    "
    module.tx_kursanmeldung {
      view {
        templateRootPaths.0 = EXT:kursanmeldung/Resources/Private/Templates/
        partialRootPaths.0 = EXT:kursanmeldung/Resources/Private/Partials/
        layoutRootPaths.0 = EXT:kursanmeldung/Resources/Private/Layouts/
      }
    }
    "
);

ExtensionUtility::configurePlugin(
    'Kursanmeldung',           // Extension name (UpperCamelCase)
    'KursanmeldungFe',          // Plugin name (UpperCamelCase)
    [
        FrontendController::class => [
            'kurswahl',
            'step1',
            'step1redirect',
            'step2',
            'step2redirect',
            'step3',
            'step3redirect',
            'step4',
            'step4redirect',
            'step5',
            'step5novalnet',
            'doiconfirm',
            'close',
            'zahlart',
            'paylater'
        ]
    ],
    [
        FrontendController::class => [
            'kurswahl',
            'step1',
            'step1redirect',
            'step2',
            'step2redirect',
            'step3',
            'step3redirect',
            'step4',
            'step4redirect',
            'step5',
            'step5novalnet',
            'doiconfirm',
            'close',
            'zahlart',
            'paylater'
        ]
    ],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
);

$GLOBALS['TYPO3_CONF_VARS']['LOG']['Hfm']['Kursanmeldung']['Controller']['FrontendController']['writerConfiguration'] = [
    LogLevel::INFO => [
        // Add a FileWriter
        FileWriter::class => [
            // Configuration for the writer
            'logFile' => Environment::getVarPath() . '/log/kursanmeldung.log',
        ],
    ],
];

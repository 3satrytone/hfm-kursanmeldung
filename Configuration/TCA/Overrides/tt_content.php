<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') or die();

(static function (): void {
    $ctypeKey = ExtensionUtility::registerPlugin(
    // extension name, matching the PHP namespaces (but without the vendor)
        'Kursanmeldung',
        // arbitrary, but unique plugin name (not visible in the backend)
        'KursanmeldungFe',
        // plugin title, as visible in the drop-down in the backend, use "LLL:" for localization
        'Kursanmeldung Frontend Plugin',
        // plugin icon, use an icon identifier from the icon registry
        'kursanmeldung-logo',
        // plugin group, to define where the new plugin will be located in
        'plugins',
        // plugin description, as visible in the new content element wizard
        'Für die Kursanmeldung Frontendformular.',
    );

    ExtensionManagementUtility::addToAllTCAtypes(
        'tt_content',
        '--div--;Configuration,pi_flexform,',
        $ctypeKey,
        'after:subheader',
    );

    ExtensionManagementUtility::addPiFlexFormValue(
        '',
        'FILE:EXT:kursanmeldung/Configuration/FlexForms/KursanmeldungFe.xml',
        $ctypeKey,
    );

    $ctypeKeyKl = ExtensionUtility::registerPlugin(
    // extension name, matching the PHP namespaces (but without the vendor)
        'Kursanmeldung',
        // arbitrary, but unique plugin name (not visible in the backend)
        'KursanmeldungKl',
        // plugin title, as visible in the drop-down in the backend, use "LLL:" for localization
        'Kurslisten Frontend Plugin',
        // plugin icon, use an icon identifier from the icon registry
        'kursanmeldung-logo',
        // plugin group, to define where the new plugin will be located in
        'plugins',
        // plugin description, as visible in the new content element wizard
        'Für die Kursanmeldung Kurslistenformular.',
    );

    ExtensionManagementUtility::addToAllTCAtypes(
        'tt_content',
        '--div--;Configuration,pi_flexform,',
        $ctypeKeyKl,
        'after:subheader',
    );

    ExtensionManagementUtility::addPiFlexFormValue(
        '',
        'FILE:EXT:kursanmeldung/Configuration/FlexForms/KursanmeldungKl.xml',
        $ctypeKeyKl,
    );
})();
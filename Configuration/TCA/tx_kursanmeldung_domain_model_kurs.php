<?php
return [
    'ctrl' => [
        'title' => 'Kurs',
        'label' => 'kursnr',
        'label_alt' => 'kursnr, instrument, professor, kurszeitstart, kurszeitend',
        'label_alt_force' => 1,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'kursnr,instrument,professor,gebuehrcom,duosel,ensemble',
        'iconfile' => 'EXT:kursanmeldung/Resources/Public/Icons/Logo.svg',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'translationSource' => 'l10n_source',
    ],
    'types' => [
        '1' => [
            'showitem' =>
                '--div--;Record, hidden, aktiv, kursnr, instrument, kurszeitstart, kurszeitend, anreisedate, kursort, professor, gebuehr, gebuehrcom, orchstudio, aktivtn, passivtn, hotel, maxupload,' .
                '--div--;Optionen, weblink, youtube, vita, stipendien, duo, duosel, ensemble,' .
                '--div--;Language, sys_language_uid, l10n_parent, l10n_diffsource,' .
                '--div--;Access, starttime, endtime'
        ],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent', 0]
                ],
                'foreign_table' => 'tx_kursanmeldung_domain_model_kurs',
                'foreign_table_where' => 'AND {#tx_kursanmeldung_domain_model_kurs}.{#pid}=###CURRENT_PID### AND {#tx_kursanmeldung_domain_model_kurs}.{#sys_language_uid} IN (0,-1)',
                'default' => 0,
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'l10n_source' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'Hidden',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    ['label' => '', 'invertStateDisplay' => true],
                ],
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'Start time',
            'config' => [
                'type' => 'datetime',
                'eval' => 'datetime',
                'default' => 0,
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'End time',
            'config' => [
                'type' => 'datetime',
                'eval' => 'datetime',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ],
            ],
        ],

        'aktiv' => [
            'exclude' => false,
            'label' => 'Aktiv',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
        'kursnr' => [
            'exclude' => false,
            'label' => 'Kurs-Nr.',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ],
        ],
        'instrument' => [
            'exclude' => false,
            'label' => 'Instrument',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'kurszeitstart' => [
            'exclude' => false,
            'label' => 'Kurszeit Start (Unixzeit)',
            'config' => [
                'type' => 'datetime',
                'dbType' => 'datetime',
                'nullable' => true,
            ],
        ],
        'kurszeitend' => [
            'exclude' => false,
            'label' => 'Kurszeit Ende (Unixzeit)',
            'config' => [
                'type' => 'datetime',
                'dbType' => 'datetime',
                'nullable' => true,
            ],
        ],
        'anreisedate' => [
            'exclude' => false,
            'label' => 'Anreisedatum (Unixzeit)',
            'config' => [
                'type' => 'datetime',
                'dbType' => 'datetime',
                'nullable' => true,
            ],
        ],
        'kursort' => [
            'exclude' => false,
            'label' => 'Kursort',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_kursanmeldung_domain_model_orte',
                'items' => [
                    ['-', 0],
                ],
                'foreign_table' => 'tx_kursanmeldung_domain_model_orte',
                'MM' => 'tx_kursanmeldung_domain_model_orte_mm',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'professor' => [
            'exclude' => false,
            'label' => 'Professor',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['-', 0],
                ],
                'foreign_table' => 'tx_kursanmeldung_domain_model_prof',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'gebuehr' => [
            'exclude' => false,
            'label' => 'Gebühr (ID/Code)',
            'config' => [
                'type' => 'number',
                'size' => 10,
                'default' => 0,
            ],
        ],
        'gebuehrcom' => [
            'exclude' => false,
            'label' => 'Gebühren Kommentar',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'cols' => 40,
                'rows' => 5,
            ],
        ],
        'orchstudio' => [
            'exclude' => false,
            'label' => 'Orchesterstudio Plätze',
            'config' => [
                'type' => 'number',
                'size' => 5,
                'default' => 0,
            ],
        ],
        'aktivtn' => [
            'exclude' => false,
            'label' => 'Aktive TN',
            'config' => [
                'type' => 'number',
                'size' => 5,
                'default' => 0,
            ],
        ],
        'passivtn' => [
            'exclude' => false,
            'label' => 'Passive TN',
            'config' => [
                'type' => 'number',
                'size' => 5,
                'default' => 0,
            ],
        ],
        'hotel' => [
            'exclude' => false,
            'label' => 'Hotel',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['-', 0],
                ],
                'foreign_table' => 'tx_kursanmeldung_domain_model_hotel',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'maxupload' => [
            'exclude' => false,
            'label' => 'Max. Uploads',
            'config' => [
                'type' => 'number',
                'size' => 5,
                'default' => 0,
            ],
        ],
        'weblink' => [
            'exclude' => false,
            'label' => 'Weblink erlaubt',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
        'youtube' => [
            'exclude' => false,
            'label' => 'YouTube erlaubt',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
        'vita' => [
            'exclude' => false,
            'label' => 'Vita erforderlich',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
        'stipendien' => [
            'exclude' => false,
            'label' => 'Stipendien möglich',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
        'duo' => [
            'exclude' => false,
            'label' => 'Duo möglich',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
        'duosel' => [
            'exclude' => false,
            'label' => 'Duo Auswahl',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 3,
            ],
        ],
        'ensemble' => [
            'exclude' => false,
            'label' => 'Ensemble',
            'config' => [
                'type' => 'check',
                'items' => array(
                    array('LLL:EXT:kursanmeldung/Resources/Private/Language/locallang.xlf:tx_kursanmeldung_domain_model_kurs.ensemble.0', ''),
                    array('LLL:EXT:kursanmeldung/Resources/Private/Language/locallang.xlf:tx_kursanmeldung_domain_model_kurs.ensemble.1', ''),
                    array('LLL:EXT:kursanmeldung/Resources/Private/Language/locallang.xlf:tx_kursanmeldung_domain_model_kurs.ensemble.2', ''),
                    array('LLL:EXT:kursanmeldung/Resources/Private/Language/locallang.xlf:tx_kursanmeldung_domain_model_kurs.ensemble.3', ''),
                    array('LLL:EXT:kursanmeldung/Resources/Private/Language/locallang.xlf:tx_kursanmeldung_domain_model_kurs.ensemble.4', ''),
                    array('LLL:EXT:kursanmeldung/Resources/Private/Language/locallang.xlf:tx_kursanmeldung_domain_model_kurs.ensemble.5', ''),
                    array('LLL:EXT:kursanmeldung/Resources/Private/Language/locallang.xlf:tx_kursanmeldung_domain_model_kurs.ensemble.6', ''),
                    array('LLL:EXT:kursanmeldung/Resources/Private/Language/locallang.xlf:tx_kursanmeldung_domain_model_kurs.ensemble.7', ''),
                    array('LLL:EXT:kursanmeldung/Resources/Private/Language/locallang.xlf:tx_kursanmeldung_domain_model_kurs.ensemble.8', ''),
                    array('LLL:EXT:kursanmeldung/Resources/Private/Language/locallang.xlf:tx_kursanmeldung_domain_model_kurs.ensemble.9', ''),
                    array('LLL:EXT:kursanmeldung/Resources/Private/Language/locallang.xlf:tx_kursanmeldung_domain_model_kurs.ensemble.10', '')
                ),
            ],
        ],
    ],
];

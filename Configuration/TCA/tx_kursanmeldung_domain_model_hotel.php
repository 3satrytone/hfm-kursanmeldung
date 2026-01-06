<?php
return [
    'ctrl' => [
        'title' => 'Hotel',
        'label' => 'hotel',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'hotel,beschreibung,ezpreis,ezpreiserm,dzpreis,dzpreiserm,dz2preis,dz2preiserm',
        'iconfile' => 'EXT:kursanmeldung/Resources/Public/Icons/Logo.svg',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'translationSource' => 'l10n_source',
    ],
    'types' => [
        '1' => [
            'showitem' => '--div--;Record, hidden, hotel, beschreibung, --div--;Preise, ezpreis, ezpreiserm, dzpreis, dzpreiserm, dz2preis, dz2preiserm, --div--;Language, sys_language_uid, l10n_parent, l10n_diffsource, --div--;Access, starttime, endtime'
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
                'foreign_table' => 'tx_kursanmeldung_domain_model_hotel',
                'foreign_table_where' => 'AND {#tx_kursanmeldung_domain_model_hotel}.{#pid}=###CURRENT_PID### AND {#tx_kursanmeldung_domain_model_hotel}.{#sys_language_uid} IN (0,-1)',
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
                    [
                        'label' => '',
                        'invertStateDisplay' => true,
                    ],
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

        'hotel' => [
            'exclude' => false,
            'label' => 'Hotel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ],
        ],
        'beschreibung' => [
            'exclude' => false,
            'label' => 'Beschreibung',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'cols' => 40,
                'rows' => 5,
            ],
        ],
        'ezpreis' => [
            'exclude' => false,
            'label' => 'EZ Preis',
            'config' => [
                'type' => 'number',
                'format' => 'decimal',
                'size' => 10,
                'default' => 0.00,
            ],
        ],
        'ezpreiserm' => [
            'exclude' => false,
            'label' => 'EZ Preis (erm.)',
            'config' => [
                'type' => 'number',
                'format' => 'decimal',
                'size' => 10,
                'default' => 0.00,
            ],
        ],
        'dzpreis' => [
            'exclude' => false,
            'label' => 'DZ Preis',
            'config' => [
                'type' => 'number',
                'format' => 'decimal',
                'size' => 10,
                'default' => 0.00,
            ],
        ],
        'dzpreiserm' => [
            'exclude' => false,
            'label' => 'DZ Preis (erm.)',
            'config' => [
                'type' => 'number',
                'format' => 'decimal',
                'size' => 10,
                'default' => 0.00,
            ],
        ],
        'dz2preis' => [
            'exclude' => false,
            'label' => 'DZ+ Preis',
            'config' => [
                'type' => 'number',
                'format' => 'decimal',
                'size' => 10,
                'default' => 0.00,
            ],
        ],
        'dz2preiserm' => [
            'exclude' => false,
            'label' => 'DZ+ Preis (erm.)',
            'config' => [
                'type' => 'number',
                'format' => 'decimal',
                'size' => 10,
                'default' => 0.00,
            ],
        ],
    ],
];

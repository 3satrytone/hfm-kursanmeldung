<?php
return [
    'ctrl' => [
        'title' => 'Ensemble',
        'label' => 'enname',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'enname,enfirstn,enlastn,ennatio,eninstru,engrplace,entype',
        'iconfile' => 'EXT:kursanmeldung/Resources/Public/Icons/Logo.svg',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'translationSource' => 'l10n_source',
    ],
    'types' => [
        '1' => [
            'showitem' => '--div--;Record, hidden, enconf, enname, engrdate, entype, engrplace, entn, enfirstn, enlastn, eninstru, engebdate, ennatio, --div--;Language, sys_language_uid, l10n_parent, l10n_diffsource, --div--;Access, starttime, endtime'
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
                'foreign_table' => 'tx_kursanmeldung_domain_model_ensemble',
                'foreign_table_where' => 'AND {#tx_kursanmeldung_domain_model_ensemble}.{#pid}=###CURRENT_PID### AND {#tx_kursanmeldung_domain_model_ensemble}.{#sys_language_uid} IN (0,-1)',
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

        'enconf' => [
            'exclude' => false,
            'label' => 'Konf.-ID',
            'config' => [
                'type' => 'number',
                'size' => 10,
                'default' => 0,
            ],
        ],
        'enname' => [
            'exclude' => false,
            'label' => 'Name',
            'config' => [
                'type' => 'input',
                'size' => 40,
                'eval' => 'trim,required',
            ],
        ],
        'engrdate' => [
            'exclude' => false,
            'label' => 'Registrierdatum',
            'config' => [
                'type' => 'date',
                'eval' => 'date',
                'default' => 0,
            ],
        ],
        'entype' => [
            'exclude' => false,
            'label' => 'Typ',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'engrplace' => [
            'exclude' => false,
            'label' => 'Registrierort',
            'config' => [
                'type' => 'input',
                'size' => 40,
                'eval' => 'trim',
            ],
        ],
        'entn' => [
            'exclude' => false,
            'label' => 'TN-ID',
            'config' => [
                'type' => 'number',
                'size' => 10,
                'default' => 0,
            ],
        ],
        'enfirstn' => [
            'exclude' => false,
            'label' => 'Vorname',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'enlastn' => [
            'exclude' => false,
            'label' => 'Nachname',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'eninstru' => [
            'exclude' => false,
            'label' => 'Instrument',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'engebdate' => [
            'exclude' => false,
            'label' => 'Geburtsdatum',
            'config' => [
                'type' => 'date',
                'eval' => 'date',
                'default' => 0,
            ],
        ],
        'ennatio' => [
            'exclude' => false,
            'label' => 'Nationalität',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
    ],
];

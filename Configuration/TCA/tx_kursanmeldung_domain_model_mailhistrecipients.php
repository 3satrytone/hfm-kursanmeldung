<?php
return [
    'ctrl' => [
        'title' => 'Mail-Empfänger',
        'label' => 'recipient',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'recipient',
        'iconfile' => 'EXT:kursanmeldung/Resources/Public/Icons/Logo.svg',
    ],
    'types' => [
        '1' => [
            'showitem' => '--div--;Record, hidden, mailuid, recipient, datesend, regid, --div--;Access, starttime, endtime'
        ],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label' => 'Hidden',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [ 'label' => '', 'invertStateDisplay' => true ],
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

        'mailuid' => [
            'exclude' => false,
            'label' => 'Mail-Historie',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [[ '-', 0 ]],
                'foreign_table' => 'tx_kursanmeldung_domain_model_mailhist',
                'default' => 0,
            ],
        ],
        'recipient' => [
            'exclude' => false,
            'label' => 'Empfänger',
            'config' => [
                'type' => 'input',
                'size' => 40,
                'eval' => 'trim,email,required',
            ],
        ],
        'datesend' => [
            'exclude' => false,
            'label' => 'Gesendet am',
            'config' => [
                'type' => 'datetime',
                'eval' => 'datetime',
                'default' => 0,
            ],
        ],
        'regid' => [
            'exclude' => false,
            'label' => 'Registrierung',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [[ '-', 0 ]],
                'foreign_table' => 'tx_kursanmeldung_domain_model_kursanmeldung',
                'default' => 0,
            ],
        ],
    ],
];

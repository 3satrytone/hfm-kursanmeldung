<?php
return [
    'ctrl' => [
        'title' => 'Uploads',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'name,kat,pfad,datein',
        'iconfile' => 'EXT:core/Resources/Public/Icons/T3Icons/content/content-table.svg',
    ],
    'types' => [
        '1' => [
            'showitem' => '--div--;Record, hidden, kurs, kat, name, pfad, datein, --div--;Access, starttime, endtime'
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

        'kurs' => [
            'exclude' => false,
            'label' => 'Kurs',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['-', 0],
                ],
                'foreign_table' => 'tx_kursanmeldung_domain_model_kurs',
                'default' => 0,
            ],
        ],
        'kat' => [
            'exclude' => false,
            'label' => 'Kategorie',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
            ],
        ],
        'name' => [
            'exclude' => false,
            'label' => 'Name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ],
        ],
        'pfad' => [
            'exclude' => false,
            'label' => 'Pfad',
            'config' => [
                'type' => 'input',
                'size' => 40,
                'eval' => 'trim',
            ],
        ],
        'datein' => [
            'exclude' => false,
            'label' => 'Datein',
            'config' => [
                'type' => 'datetime',
                'dbType' => 'datetime',
                'format' => 'datetime',
                'nullable' => true,
            ],
        ],
        'fileref' => [
            'label' => 'Upload Filereference',
            'config' => [
                'type' => 'file',
                'minitems' => 1,
                'maxitems' => 1,
                'allowed' => ['common-image-types','common-text-types','common-media-types']
            ],
        ]
    ],
];

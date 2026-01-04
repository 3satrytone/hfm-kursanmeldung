<?php
return [
    'ctrl' => [
        'title' => 'Orte',
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
        'searchFields' => 'name,ort,plz,adresse,beschreibung',
        'iconfile' => 'EXT:kursanmeldung/Resources/Public/Icons/Logo.svg',
    ],
    'types' => [
        '1' => [
            'showitem' => '--div--;Record, hidden, name, ort, plz, adresse, tx_jokursanmeldung_long, lat, beschreibung, --div--;Access, starttime, endtime'
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

        'name' => [
            'exclude' => false,
            'label' => 'Name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ],
        ],
        'ort' => [
            'exclude' => false,
            'label' => 'Ort',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'plz' => [
            'exclude' => false,
            'label' => 'PLZ',
            'config' => [
                'type' => 'input',
                'size' => 10,
                'eval' => 'trim',
            ],
        ],
        'adresse' => [
            'exclude' => false,
            'label' => 'Adresse',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 5,
            ],
        ],
        'longi' => [
            'exclude' => false,
            'label' => 'Long',
            'config' => [
                'type' => 'number',
                'format' => 'decimal',
                'size' => 10,
                'default' => 0.00,
            ],
        ],
        'lati' => [
            'exclude' => false,
            'label' => 'Lat',
            'config' => [
                'type' => 'number',
                'format' => 'decimal',
                'size' => 10,
                'default' => 0.00,
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
    ],
];

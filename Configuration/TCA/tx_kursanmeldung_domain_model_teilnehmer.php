<?php
return [
    'ctrl' => [
        'title' => 'Teilnehmer',
        'label' => 'nachname',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'vorname,nachname,email,matrikel,adresse1,ort,land,telefon,mobil',
        'iconfile' => 'EXT:core/Resources/Public/Icons/T3Icons/content/content-table.svg',
    ],
    'types' => [
        '1' => [
            'showitem' => '--div--;Record, hidden, vorname, nachname, anrede, titel, matrikel, gebdate, sprache, nation, adresse1, hausnr, adresse2, plz, ort, land, telefon, mobil, telefax, email, datein, --div--;Access, starttime, endtime'
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

        'vorname' => [
            'exclude' => false,
            'label' => 'Vorname',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'nachname' => [
            'exclude' => false,
            'label' => 'Nachname',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ],
        ],
        'anrede' => [
            'exclude' => false,
            'label' => 'Anrede',
            'config' => [
                'type' => 'number',
                'size' => 5,
                'default' => 0,
            ],
        ],
        'titel' => [
            'exclude' => false,
            'label' => 'Titel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'matrikel' => [
            'exclude' => false,
            'label' => 'Matrikel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'gebdate' => [
            'exclude' => false,
            'label' => 'Geburtsdatum',
            'config' => [
                'type' => 'datetime',
                'dbType' => 'datetime',
                'format' => 'date',
                'nullable' => true,
            ],
        ],
        'sprache' => [
            'exclude' => false,
            'label' => 'Sprache',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'nation' => [
            'exclude' => false,
            'label' => 'Nation',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'adresse1' => [
            'exclude' => false,
            'label' => 'Adresse 1',
            'config' => [
                'type' => 'input',
                'size' => 40,
                'eval' => 'trim',
            ],
        ],
        'hausnr' => [
            'exclude' => false,
            'label' => 'Hausnummer',
            'config' => [
                'type' => 'input',
                'size' => 10,
                'eval' => 'trim',
            ],
        ],
        'adresse2' => [
            'exclude' => false,
            'label' => 'Adresse 2',
            'config' => [
                'type' => 'input',
                'size' => 40,
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
        'ort' => [
            'exclude' => false,
            'label' => 'Ort',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'land' => [
            'exclude' => false,
            'label' => 'Land',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'telefon' => [
            'exclude' => false,
            'label' => 'Telefon',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'mobil' => [
            'exclude' => false,
            'label' => 'Mobil',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'telefax' => [
            'exclude' => false,
            'label' => 'Telefax',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'email' => [
            'exclude' => false,
            'label' => 'E-Mail',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,email',
            ],
        ],
        'datein' => [
            'exclude' => false,
            'label' => 'Eingangsdatum',
            'config' => [
                'type' => 'datetime',
                'dbType' => 'datetime',
                'format' => 'datetime',
                'nullable' => true,
            ],
        ],
    ],
];

<?php
return [
    'ctrl' => [
        'title' => 'Anmeldestatus',
        'label' => 'anmeldestatus',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'anmeldestatus,kurz,beschreibung',
        'iconfile' => 'EXT:kursanmeldung/Resources/Public/Icons/Logo.svg',
    ],
    'types' => [
        '1' => [
            'showitem' => '--div--;Record, hidden, anmeldestatus, kurz, beschreibung, reducetnart, --div--;Access, starttime, endtime'
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

        'anmeldestatus' => [
            'exclude' => false,
            'label' => 'Anmeldestatus',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ],
        ],
        'kurz' => [
            'exclude' => false,
            'label' => 'Kurz',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
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
        'reducetnart' => [
            'exclude' => false,
            'label' => 'Teilnahmeart reduzieren',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
    ],
];

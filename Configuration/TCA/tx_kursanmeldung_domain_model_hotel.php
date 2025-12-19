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
        'iconfile' => 'EXT:core/Resources/Public/Icons/T3Icons/content/content-table.svg',
    ],
    'types' => [
        '1' => [
            'showitem' => '--div--;Record, hidden, hotel, beschreibung, --div--;Preise, ezpreis, ezpreiserm, dzpreis, dzpreiserm, dz2preis, dz2preiserm, --div--;Access, starttime, endtime'
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

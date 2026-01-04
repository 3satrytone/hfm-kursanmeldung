<?php
return [
    'ctrl' => [
        'title' => 'Gebühren',
        'label' => 'anmeldung',
        'label_alt' => 'anmeldungerm, aktivengeb, aktivengeberm, passivgeb, passivgeberm',
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
        'searchFields' => 'anmeldung,anmeldungerm,aktivengeb,aktivengeberm,passivgeb,passivgeberm',
        'iconfile' => 'EXT:kursanmeldung/Resources/Public/Icons/Logo.svg',
    ],
    'types' => [
        '1' => [
            'showitem' => '--div--;Record, hidden, anmeldung, anmeldungerm, aktivengeb, aktivengeberm, passivgeb, passivgeberm, --div--;Access, starttime, endtime'
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

        'anmeldung' => [
            'exclude' => false,
            'label' => 'Anmeldung',
            'config' => [
                'type' => 'number',
                'format' => 'decimal',
                'size' => 10,
                'default' => 0.00,
            ],
        ],
        'anmeldungerm' => [
            'exclude' => false,
            'label' => 'Anmeldung (erm.)',
            'config' => [
                'type' => 'number',
                'format' => 'decimal',
                'size' => 10,
                'default' => 0.00,
            ],
        ],
        'aktivengeb' => [
            'exclude' => false,
            'label' => 'Aktivengebühr',
            'config' => [
                'type' => 'number',
                'format' => 'decimal',
                'size' => 10,
                'default' => 0.00,
            ],
        ],
        'aktivengeberm' => [
            'exclude' => false,
            'label' => 'Aktivengebühr (erm.)',
            'config' => [
                'type' => 'number',
                'format' => 'decimal',
                'size' => 10,
                'default' => 0.00,
            ],
        ],
        'passivgeb' => [
            'exclude' => false,
            'label' => 'Passivgebühr',
            'config' => [
                'type' => 'number',
                'format' => 'decimal',
                'size' => 10,
                'default' => 0.00,
            ],
        ],
        'passivgeberm' => [
            'exclude' => false,
            'label' => 'Passivgebühr (erm.)',
            'config' => [
                'type' => 'number',
                'format' => 'decimal',
                'size' => 10,
                'default' => 0.00,
            ],
        ],
    ],
];

<?php
return [
    'ctrl' => [
        'title' => 'Exportlisten',
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
        'searchFields' => 'name,tables,notice',
        'iconfile' => 'EXT:kursanmeldung/Resources/Public/Icons/Logo.svg',
    ],
    'types' => [
        '1' => [
            'showitem' => '--div--;Record, hidden, name, tables, notice, --div--;Access, starttime, endtime'
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
                'size' => 40,
                'eval' => 'trim,required',
            ],
        ],
        'tables' => [
            'exclude' => false,
            'label' => 'Tabellen',
            'description' => 'Liste von Tabellen (z. B. CSV oder JSON).',
            'config' => [
                'type' => 'text',
                'cols' => 60,
                'rows' => 5,
            ],
        ],
        'notice' => [
            'exclude' => false,
            'label' => 'Hinweis',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'cols' => 60,
                'rows' => 5,
            ],
        ],
    ],
];

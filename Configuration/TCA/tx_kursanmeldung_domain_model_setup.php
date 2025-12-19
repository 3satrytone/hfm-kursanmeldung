<?php
return [
    'ctrl' => [
        'title' => 'Setup',
        'label' => 'propname',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'propname,propvalue',
        'iconfile' => 'EXT:core/Resources/Public/Icons/T3Icons/content/content-table.svg',
    ],
    'types' => [
        '1' => [
            'showitem' => '--div--;Record, hidden, propname, propvalue, --div--;Access, starttime, endtime'
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

        'propname' => [
            'exclude' => false,
            'label' => 'Property name',
            'config' => [
                'type' => 'input',
                'size' => 40,
                'eval' => 'trim,required',
            ],
        ],
        'propvalue' => [
            'exclude' => false,
            'label' => 'Property value',
            'config' => [
                'type' => 'text',
                'cols' => 60,
                'rows' => 5,
            ],
        ],
    ],
];

<?php
return [
    'ctrl' => [
        'title' => 'Mail-Historie',
        'label' => 'subject',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'subject,sendername,sendermail,pageid,mailtype,nachricht',
        'iconfile' => 'EXT:kursanmeldung/Resources/Public/Icons/Logo.svg',
    ],
    'types' => [
        '1' => [
            'showitem' => '--div--;Record, hidden, subject, sendername, sendermail, pageid, mailtype, nachricht, recipients, mailhist_recipients, --div--;Access, starttime, endtime'
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

        'subject' => [
            'exclude' => false,
            'label' => 'Betreff',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim,required',
            ],
        ],
        'sendername' => [
            'exclude' => false,
            'label' => 'Absendername',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'sendermail' => [
            'exclude' => false,
            'label' => 'Absender E-Mail',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,email',
            ],
        ],
        'pageid' => [
            'exclude' => false,
            'label' => 'Seiten-ID',
            'config' => [
                'type' => 'input',
                'size' => 10,
                'eval' => 'trim',
            ],
        ],
        'mailtype' => [
            'exclude' => false,
            'label' => 'Mailtyp',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
            ],
        ],
        'nachricht' => [
            'exclude' => false,
            'label' => 'Nachricht',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'cols' => 60,
                'rows' => 10,
            ],
        ],
        'recipients' => [
            'exclude' => false,
            'label' => 'Anzahl Empfänger',
            'config' => [
                'type' => 'number',
                'size' => 10,
                'default' => 0,
            ],
        ],
        'mailhist_recipients' => [
            'exclude' => false,
            'label' => 'Empfänger (Datensätze)',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_kursanmeldung_domain_model_mailhistrecipients',
                'foreign_field' => 'mailuid',
                'appearance' => [
                    'useSortable' => true,
                    'expandSingle' => true,
                    'newRecordLinkAddTitle' => true,
                    'levelLinksPosition' => 'top',
                ],
            ],
        ],
    ],
];

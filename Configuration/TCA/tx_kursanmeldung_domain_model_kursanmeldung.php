<?php
return [
    'ctrl' => [
        'title' => 'Kursanmeldungen',
        'label' => 'tn',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'tn,kurs,zahlart,gebuehr,duoname,comment,programm,orchesterstudio,registrationkey,novalnettid,novalnetcno,notice',
        'iconfile' => 'EXT:core/Resources/Public/Icons/T3Icons/content/content-table.svg',
    ],
    'types' => [
        '1' => [
            'showitem' => '--div--;Record, hidden, deflang, tn, kurs, uploads, anmeldestatus, profstatus, teilnahmeart, duo, duosel, duoname, programm, orchesterstudio, hotel, room, roomwith, roomfrom, roomto, --div--;Zahlung, bezahlt, bezahltag, zahlart, zahltbis, gezahlt, gezahltag, gezahltos, gebuehr, gebuehrag, gebuehrdat, novalnettid, novalnettidag, novalnetcno, --div--;Rechtliches, agb, datenschutz, savedata, --div--;Technik, salt, registrationkey, doitime, notice, ensemble, stipendiat, studentship, studystat, datein, --div--;Access, starttime, endtime'
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

        'deflang' => [
            'exclude' => false,
            'label' => 'Def. Sprache',
            'config' => [
                'type' => 'number',
                'size' => 3,
                'default' => 0,
            ],
        ],
        'tn' => [
            'exclude' => false,
            'label' => 'Teilnehmer',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [[ '-', 0 ]],
                'foreign_table' => 'tx_kursanmeldung_domain_model_teilnehmer',
                'default' => 0,
            ],
        ],
        'kurs' => [
            'exclude' => false,
            'label' => 'Kurs',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [[ '-', 0 ]],
                'foreign_table' => 'tx_kursanmeldung_domain_model_kurs',
                'default' => 0,
            ],
        ],
        'uploads' => [
            'exclude' => false,
            'label' => 'Uploads',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [[ '-', 0 ]],
                'foreign_table' => 'tx_kursanmeldung_domain_model_uploads',
                'default' => 0,
            ],
        ],
        'bezahlt' => [
            'exclude' => false,
            'label' => 'Bezahlt',
            'config' => [
                'type' => 'number',
                'size' => 5,
                'default' => 0,
            ],
        ],
        'bezahltag' => [
            'exclude' => false,
            'label' => 'Bezahlt am',
            'config' => [
                'type' => 'datetime',
                'eval' => 'datetime',
                'default' => 0,
            ],
        ],
        'zahlart' => [
            'exclude' => false,
            'label' => 'Zahlart',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
            ],
        ],
        'zahltbis' => [
            'exclude' => false,
            'label' => 'Zahlt bis',
            'config' => [
                'type' => 'datetime',
                'dbType' => 'datetime',
                'format' => 'datetime',
                'nullable' => true,
            ],
        ],
        'gezahlt' => [
            'exclude' => false,
            'label' => 'Gezahlt',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
            ],
        ],
        'gezahltag' => [
            'exclude' => false,
            'label' => 'Gezahlt am (Text)',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
            ],
        ],
        'gezahltos' => [
            'exclude' => false,
            'label' => 'Gezahlt sonstiges',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
            ],
        ],
        'hotel' => [
            'exclude' => false,
            'label' => 'Hotel',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [[ '-', 0 ]],
                'foreign_table' => 'tx_kursanmeldung_domain_model_hotel',
                'default' => 0,
            ],
        ],
        'room' => [
            'exclude' => false,
            'label' => 'Zimmer',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
            ],
        ],
        'roomwith' => [
            'exclude' => false,
            'label' => 'Zimmer mit',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'roomfrom' => [
            'exclude' => false,
            'label' => 'Zimmer von',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
            ],
        ],
        'roomto' => [
            'exclude' => false,
            'label' => 'Zimmer bis',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
            ],
        ],
        'gebuehr' => [
            'exclude' => false,
            'label' => 'Gebühr',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
            ],
        ],
        'gebuehrag' => [
            'exclude' => false,
            'label' => 'Gebühr (AG)',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
            ],
        ],
        'gebuehrdat' => [
            'exclude' => false,
            'label' => 'Gebühr Datum',
            'config' => [
                'type' => 'datetime',
                'eval' => 'datetime',
                'default' => 0,
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
        'teilnahmeart' => [
            'exclude' => false,
            'label' => 'Teilnahmeart',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
            ],
        ],
        'anmeldestatus' => [
            'exclude' => false,
            'label' => 'Anmeldestatus',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [[ '-', 0 ]],
                'foreign_table' => 'tx_kursanmeldung_domain_model_anmeldestatus',
                'default' => 0,
            ],
        ],
        'profstatus' => [
            'exclude' => false,
            'label' => 'Prof-Status',
            'config' => [
                'type' => 'number',
                'size' => 3,
                'default' => 0,
            ],
        ],
        'programm' => [
            'exclude' => false,
            'label' => 'Programm',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'cols' => 40,
                'rows' => 5,
            ],
        ],
        'orchesterstudio' => [
            'exclude' => false,
            'label' => 'Orchesterstudio',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 5,
            ],
        ],
        'duo' => [
            'exclude' => false,
            'label' => 'Duo',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
        'duosel' => [
            'exclude' => false,
            'label' => 'Duo Auswahl',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'duoname' => [
            'exclude' => false,
            'label' => 'Duo Name',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 3,
            ],
        ],
        'comment' => [
            'exclude' => false,
            'label' => 'Kommentar',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 3,
            ],
        ],
        'agb' => [
            'exclude' => false,
            'label' => 'AGB akzeptiert',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
        'datenschutz' => [
            'exclude' => false,
            'label' => 'Datenschutz akzeptiert',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
        'savedata' => [
            'exclude' => false,
            'label' => 'Daten speichern',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
        'salt' => [
            'exclude' => false,
            'label' => 'Salt',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'registrationkey' => [
            'exclude' => false,
            'label' => 'Registration Key',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 3,
            ],
        ],
        'doitime' => [
            'exclude' => false,
            'label' => 'Double Opt In Zeit',
            'config' => [
                'type' => 'datetime',
                'dbType' => 'datetime',
                'format' => 'datetime',
                'nullable' => true,
            ],
        ],
        'novalnettid' => [
            'exclude' => false,
            'label' => 'Novalnet TID',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'novalnettidag' => [
            'exclude' => false,
            'label' => 'Novalnet TID (AG)',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'novalnetcno' => [
            'exclude' => false,
            'label' => 'Novalnet CNO',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'notice' => [
            'exclude' => false,
            'label' => 'Notiz',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 3,
            ],
        ],
        'ensemble' => [
            'exclude' => false,
            'label' => 'Ensemble',
            'config' => [
                'type' => 'number',
                'size' => 5,
                'default' => 0,
            ],
        ],
        'stipendiat' => [
            'exclude' => false,
            'label' => 'Stipendiat',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
        'studentship' => [
            'exclude' => false,
            'label' => 'Studentship',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
        'studystat' => [
            'exclude' => false,
            'label' => 'Studienstatus',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
    ],
];

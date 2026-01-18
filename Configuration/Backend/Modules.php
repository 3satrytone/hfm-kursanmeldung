<?php

use Hfm\Kursanmeldung\Controller;

$GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['extbase.consistentDateTimeHandling'] = true;

return [
    'web_Kursanmeldung' => [
        'parent' => 'web',
        'position' => ['after' => 'web_list'],
        'access' => 'user,group',
        'workspaces' => 'live',
        'path' => '/module/web/kursanmeldung',
        'labels' => 'LLL:EXT:kursanmeldung/Resources/Private/Language/locallang_be.xlf:module.kursanmeldung',
        'extensionName' => 'kursanmeldung',
        'iconIdentifier' => 'kursanmeldung-logo',
        'icon' => 'EXT:kursanmeldung/Resources/Public/Icons/Extension.svg',
        'controllerActions' => [
            Controller\GeneralController::class => ['index'],
            Controller\GebuehrenController::class => ['list','show','new','create','edit','update','delete'],
            Controller\HotelController::class => ['list','show','new','create','edit','update','delete'],
            Controller\KursanmeldungController::class => ['list'],
            Controller\OrteController::class => ['list','show','new','create','edit','update','delete'],
            Controller\ProfController::class => ['list','show','new','create','edit','update','delete'],
            Controller\KursController::class => ['list','show','new','create','edit','update','delete'],
            Controller\TeilnehmerController::class => ['list','edit','delete','update','updateAnmeldestatus'],
            Controller\MailingController::class => ['list'],
        ],
    ],
];

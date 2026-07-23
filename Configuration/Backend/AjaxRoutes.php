<?php

use Hfm\Kursanmeldung\Controller\ExportlistController;
use Hfm\Kursanmeldung\Controller\TeilnehmerController;

return [
    'kursanmeldung_teilnehmer_updateanmeldestatus' => [
        'path' => '/kursanmeldung/teilnehmer/update-anmeldestatus',
        'target' => TeilnehmerController::class . '::updateAnmeldestatusAction',
    ],
    'kursanmeldung_exportlist_ajaxlist' => [
        'path' => '/kursanmeldung/exportlist/ajax-list',
        'target' => ExportlistController::class . '::ajaxListAction',
        'inheritAccessFromModule' => 'web_Kursanmeldung',
    ],
    'kursanmeldung_exportlist_savesetup' => [
        'path' => '/kursanmeldung/exportlist/save-setup',
        'target' => ExportlistController::class . '::saveSetupAction',
        'inheritAccessFromModule' => 'web_Kursanmeldung',
    ],
    'kursanmeldung_exportlist_loadsetup' => [
        'path' => '/kursanmeldung/exportlist/load-setup',
        'target' => ExportlistController::class . '::loadSetupAction',
        'inheritAccessFromModule' => 'web_Kursanmeldung',
    ],
    'kursanmeldung_exportlist_listsetups' => [
        'path' => '/kursanmeldung/exportlist/list-setups',
        'target' => ExportlistController::class . '::listSetupsAction',
        'inheritAccessFromModule' => 'web_Kursanmeldung',
    ],
    'kursanmeldung_exportlist_deletesetup' => [
        'path' => '/kursanmeldung/exportlist/delete-setup',
        'target' => ExportlistController::class . '::deleteSetupAction',
        'inheritAccessFromModule' => 'web_Kursanmeldung',
    ],
];
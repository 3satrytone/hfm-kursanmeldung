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
];
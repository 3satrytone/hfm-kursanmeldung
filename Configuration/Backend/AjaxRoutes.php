<?php

use Hfm\Kursanmeldung\Controller\TeilnehmerController;

return [
    'kursanmeldung_teilnehmer_updateanmeldestatus' => [
        'path' => '/kursanmeldung/teilnehmer/update-anmeldestatus',
        'target' => TeilnehmerController::class . '::updateAnmeldestatusAction',
    ],
];
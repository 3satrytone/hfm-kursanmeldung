<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\Controller;

use Hfm\Kursanmeldung\Domain\Repository\AnmeldestatusRepository;
use Hfm\Kursanmeldung\Domain\Repository\KursanmeldungRepository;
use Hfm\Kursanmeldung\Domain\Repository\ProfRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * KurslisteController
 */
class KursListeController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * hideIfClassMemberArr
     *
     * @var array hideIfClassMemberArr Keine Anzeige wenn UID aus anmeldestatus überienstimmt
     */
    protected $hideIfClassMemberArr = array(5, 6);

    public function __construct(
        private readonly KursanmeldungRepository $kursanmeldungRepository,
        private readonly AnmeldestatusRepository $anmeldestatusRepository,
        private readonly ProfRepository $profRepository,
    ) {
    }

    public function initializeAction(): void
    {
        if (isset($this->settings)) {
            // if dbdata distributed over more pages
            if (isset($this->settings['dataPages'])) {
                if (isset($this->KursanmeldungKursRepository)) {
                    $this->KursanmeldungKursRepository->setPageIds($this->settings['dataPages']);
                }
                if (isset($this->hotelRepository)) {
                    $this->hotelRepository->setPageIds($this->settings['dataPages']);
                }
                if (isset($this->profRepository)) {
                    $this->profRepository->setPageIds($this->settings['dataPages']);
                }
                if (isset($this->gebuehrenRepository)) {
                    $this->gebuehrenRepository->setPageIds($this->settings['dataPages']);
                }
                if (isset($this->orteRepository)) {
                    $this->orteRepository->setPageIds($this->settings['dataPages']);
                }
                if (isset($this->ExportlistRepository)) {
                    $this->ExportlistRepository->setPageIds($this->settings['dataPages']);
                }
                if (isset($this->anmeldestatusRepository)) {
                    $this->anmeldestatusRepository->setPageIds($this->settings['dataPages']);
                }
            }
        }
        $disStat = $this->anmeldestatusRepository->findByReducetnart(1);

        if ($disStat->count() > 0) {
            $this->hideIfClassMemberArr = array();
            foreach ($disStat as $key => $value) {
                array_push($this->hideIfClassMemberArr, $value->getUid());
            }
        }
        if (isset($this->settings['hideIfClassMemberArr'])) {
            $this->hideIfClassMemberArr = $this->settings['hideIfClassMemberArr'];
        }
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAction(): ResponseInterface
    {
        $kurse = [];
        if (isset($this->settings['kursids'])) {
            $kurse = explode(',', $this->settings['kursids']);
        }

        $kursanmeldungenGrouped = [];
        $kursanmeldungen = null;

        $showGrouped = [];

        if (!empty($kurse)) {
            foreach ($kurse as $kursid) {
                $kursanmeldungen = $this->kursanmeldungRepository->findByKursNotPassive((int)$kursid);
                if (!empty($kursanmeldungen)) {
                    foreach ($kursanmeldungen as $kursanmeldung) {
                        // nicht in der Liste Anzeigen wenn Status Abgesagt oder Abgemeldet ist
                        $showIfClassMember = true;
                        $status = $this->getAnmeldestatus($kursanmeldung);


                        if (in_array($status, $this->hideIfClassMemberArr) || $status === null) {
                            $showIfClassMember = false;
                        }
                        // nur kurse mit aktiver Teilnahme = 0
                        if ($kursanmeldung->getTeilnahmeart() === "0") {
                            // Kursdaten ermitteln
                            $kurs = $kursanmeldung->getKurs();
                            if (!empty($kurs)) {
                                if ($showIfClassMember) {
                                    $kursanmeldungenGrouped[$kurs->getUid()]['registrations'][] = $kursanmeldung;
                                }
                                $kursanmeldungenGrouped[$kurs->getUid()]['showGrouped'] = 0;
                                $kursanmeldungenGrouped[$kurs->getUid()]['total']++;
                                if (isset($showGrouped[$kurs->getUid()])) {
                                    $kursanmeldungenGrouped[$kurs->getUid(
                                    )]['showGrouped'] = $showGrouped[$kurs->getUid()];
                                }
                            }
                        }
                    }
                }
            }
        }
        if ($this->request->hasArgument('paginateSearch')) {
            $search = $this->request->getArgument('paginateSearch');
            if (!empty($search)) {
                foreach ($search as $key => $value) {
                    $kursanmeldungenSearch[$key] = $this->kursanmeldungRepository->findBySearchExtbase($key, $value);

                    if ($key == 0) {
                        $kursanmeldungen = array();
                        $kursanmeldungen = $kursanmeldungenSearch[$key];
                        $kursanmeldungen['search'] = $value;
                    } else {
                        if (!empty($kursanmeldungenSearch[$key])) {
                            $kursanmeldungenGrouped[$key]['kurs'] = '';
                            foreach ($kursanmeldungenSearch[$key] as $skey => $svalue) {
                                // nicht in der Liste Anzeigen wenn Status Abgesagt oder Abgemeldet ist
                                $showIfClassMember = true;
                                $status = $this->getAnmeldestatus($svalue);
                                if (in_array($status, $this->hideIfClassMemberArr)) {
                                    $showIfClassMember = false;
                                }
                                if ($svalue->getTeilnahmeart() == 0) {
                                    if ($showIfClassMember) {
                                        $kursanmeldungenGrouped[$key]['kurs'][] = $svalue;
                                    }
                                }
                            }
                        }
                        $kursanmeldungenGrouped[$key]['search'] = $value;
                        $kursanmeldungenGrouped[$key]['showGrouped'] = 1;
                    }
                }
            }
        }
        $profStatuus = $this->anmeldestatusRepository->findAnmeldeStatusByProfPrefix();
        $profSelStatus = [];
        foreach ($profStatuus as $profStatus) {
            $profSelStatus[] = [
                'uid' => $profStatus->getUid(),
                'kurzTranslated' => LocalizationUtility::translate(
                    'tx_kursanmeldung_domain_model_kursliste.statusprof.' . $profStatus->getKurz(),
                    'kursanmeldung'
                ),
            ];
        }

        ksort($kursanmeldungenGrouped, SORT_NUMERIC);
        $this->view->assign('kursanmeldungen', $kursanmeldungen);
        $this->view->assign('kursanmeldungenGrouped', $kursanmeldungenGrouped);
        $this->view->assign('profSelStatus', $profSelStatus);

        return $this->htmlResponse();
    }

    /**
     * action updatestatus
     *
     * @return void
     */
    public function updatestatusAction(): ResponseInterface
    {
        // Unterstützt zwei Varianten der Übergabe:
        // 1) uid + status als einzelne Argumente (empfohlen für AJAX)
        // 2) anmeldestatus[<uid>] => <statusUid> (aus dem Fluid-Formular)
        $uid = null;
        $statusUid = null;

        if ($this->request->hasArgument('uid')) {
            $uid = (int)$this->request->getArgument('uid');
        }
        if ($this->request->hasArgument('status')) {
            $statusUid = $this->request->getArgument('status') !== 'NULL'
                ? (int)$this->request->getArgument('status')
                : null;
        }

        if ($this->request->hasArgument('anmeldestatus')) {
            $arr = (array)$this->request->getArgument('anmeldestatus');
            // Erwartet genau einen Eintrag: [<uid>] => <statusUid|NULL>
            foreach ($arr as $key => $val) {
                $uid = (int)$key;
                $statusUid = $val !== 'NULL' ? (int)$val : null;
                break;
            }
        }

        if (empty($uid)) {
            return $this->jsonResponse(
                json_encode([
                    'success' => false,
                    'message' => 'Missing uid',
                ])
            )->withStatus(400);
        }

        // Kursanmeldung laden
        $kursanmeldung = $this->kursanmeldungRepository->findByUid($uid);
        if ($kursanmeldung === null) {
            return $this->jsonResponse(
                json_encode([
                    'success' => false,
                    'message' => 'Kursanmeldung not found',
                ])
            )->withStatus(404);
        }
        try {
            // Prof-Status aktualisieren (als Einzelwert in ObjectStorage)
            $storage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
            if (!empty($statusUid)) {
                $status = $this->anmeldestatusRepository->findByUid((int)$statusUid);
                if ($status !== null) {
                    $storage->attach($status);
                }
            }
            $kursanmeldung->setProfstatus($storage);

            // Persistieren
            $this->kursanmeldungRepository->update($kursanmeldung);
            GeneralUtility::makeInstance(
                PersistenceManager::class
            )->persistAll();
        }catch (\Exception $e){
            if (empty($uid)) {
                return $this->jsonResponse(
                    json_encode([
                        'success' => false,
                        'message' => 'Could not persist',
                    ])
                )->withStatus(400);
            }
        }

        return $this->jsonResponse(json_encode([
            'success' => true,
            'uid' => $uid,
            'statusUid' => $statusUid,
        ]))->withStatus(200);
    }

    /**
     * action list
     *
     * @return void
     */
    public function exportkursAction()
    {
        $kursid = 0;
        $type = 'PDF';
        $fileExt = 'pdf';
        $kursidAllowed = explode(',', $this->settings['kursids']);
        $typeAllowed = array('pdf', 'Excel5', 'Excel');

        if ($this->request->hasArgument('kursid') && in_array($this->request->getArgument('kursid'), $kursidAllowed)) {
            $kursid = $this->request->getArgument('kursid');
        }
        if ($this->request->hasArgument('type') && in_array($this->request->getArgument('type'), $typeAllowed)) {
            $type = $this->request->getArgument('type');
        }

        switch ($type) {
            case 'Excel5':
            case 'Excel':
                $fileExt = 'xls';
                break;
            case 'pdf':
                $type = 'PDF';
                $fileExt = 'pdf';
                break;
        }

        $tempData = $this->kursanmeldungRepository->findByKurs($kursid);

        $set = 0;
        $settings['data'] = $this->buildData($tempData, $set);

        $settings['filename'] = date('Y-m-d') . "_kursanmeldung." . $fileExt;
        $settings['version'] = $type;
        $settings['size'] = 64;

        $this->Joexport->Joexport($settings, true);
    }

    protected function getStastusProfObj()
    {
        $optionArr = array();
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
            'uid,kurz',
            'tx_jokursanmeldung_domain_model_anmeldestatus',
            'deleted=0 AND hidden=0 AND kurz LIKE "prof\_%"'
        );
        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
            $row['kurzTranslated'] = $this->joTranslate(
                'tx_jokursanmeldung_domain_model_kursanmeldungliste.statusprof.' . $row['kurz']
            );
            array_push($optionArr, $row);
        }
        return $optionArr;
    }

    protected function buildData($repData, $set = 0)
    {
        $data = array();
        $expArrFrm = $this->getExportArrFrames($set);
        if (!empty($repData) && !empty($expArrFrm)) {
            foreach ($repData as $item) {
                if (!empty($item) && get_class($item) == 'Justorange\JoKursanmeldung\Domain\Model\Kursanmeldung') {
                    $temp = array();
                    $status = true;
                    foreach ($expArrFrm['dataKey'] as $key => $value) {
                        // teilweise zweistufiges array
                        $keyArr = explode('.', $value);
                        if (!empty($keyArr)) {
                            $method_name = 'get' . ucfirst($keyArr[0]);
                            $temp[$value] = $this->getValueFromMethodname($method_name, $item);
                            if (isset($keyArr[1])) {
                                $method_name = 'get' . ucfirst($keyArr[1]);
                                if (gettype($temp[$value]) == 'object' && count($temp[$value]) == 0) {
                                    $temp[$value] = $this->getValueFromMethodname($method_name, $temp[$value]);
                                } else {
                                    $tempArr = array();
                                    foreach ($temp[$value] as $obj) {
                                        array_push($tempArr, $this->getValueFromMethodname($method_name, $obj));
                                    }
                                    $temp[$value] = implode($tempArr, ',');
                                }
                            }
                            // bestimmte Werte übersetzen
                            if (in_array($value, $this->translateKeys)) {
                                $temp[$value] = $this->joTranslate($value . '.' . $temp[$value]);
                            }
                            // bestimmte Werte aus DB
                            if ($value == 'tn.land') {
                                switch ($GLOBALS['TSFE']->sys_language_isocode) {
                                    case "de":
                                        $cntrName = 'getShortNameDe';
                                        break;
                                    case "en":
                                        $cntrName = 'getShortNameEn';
                                        break;
                                    default:
                                        $cntrName = 'getShortNameLocal';
                                }
                                $countries = array();
                                $country = $this->countryRepository->findByUid($temp[$value]);
                                $temp[$value] = $country->$cntrName();
                            }
                            if ($value == 'anmeldestatus') {
                                $retObj = null;
                                if (is_object($temp[$value])) {
                                    if ($temp[$value]->current() != null) {
                                        $retObj = $temp[$value]->current();
                                        $temp[$value] = $retObj->getAnmeldestatus();
                                        if (in_array($retObj->getUid(), $this->hideIfClassMemberArr)) {
                                            $status = false;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($status) {
                        array_push($data, $temp);
                    }
                }
            }
            if (isset($expArrFrm['header'])) {
                $tempArr = array();
                foreach ($expArrFrm['header'] as $key => $value) {
                    $transl = $this->joTranslate($value['translate']);
                    $transl = ($transl == null) ? $value['default'] : $transl;
                    array_push($tempArr, $transl);
                }
                array_unshift($data, $tempArr);
            }
        }
        return $data;
    }

    protected function getValueFromMethodname($method_name, $obj)
    {
        $methValue = '';
        if (gettype($obj) == 'object' && method_exists($obj, $method_name)) {
            $methValue = $obj->$method_name();
            if ($methValue instanceof \DateTime) {
                $methValue = $methValue->format('d.m.Y');
            }
        }
        return $methValue;
    }

    protected function getExportArrFrames($set = 0)
    {
        $sets = array(
            0 => array(
                'header' => array(
                    array(
                        'type' => 0,
                        'default' => '',
                        'translate' => 'tx_jokursanmeldung_domain_model_kursanmeldungteilnehmer.datein'
                    ),
                    array('type' => 0, 'default' => '', 'translate' => 'be_export.anmeldestatus'),
                    array(
                        'type' => 0,
                        'default' => '',
                        'translate' => 'tx_jokursanmeldung_domain_model_kursanmeldungteilnehmer.anrede'
                    ),
                    array(
                        'type' => 0,
                        'default' => '',
                        'translate' => 'tx_jokursanmeldung_domain_model_kursanmeldungteilnehmer.vorname'
                    ),
                    array(
                        'type' => 0,
                        'default' => '',
                        'translate' => 'tx_jokursanmeldung_domain_model_kursanmeldungteilnehmer.nachname'
                    ),
                    array(
                        'type' => 0,
                        'default' => '',
                        'translate' => 'tx_jokursanmeldung_domain_model_kursanmeldungteilnehmer.gebdate'
                    ),
                    array(
                        'type' => 0,
                        'default' => '',
                        'translate' => 'tx_jokursanmeldung_domain_model_kursanmeldungteilnehmer.email'
                    ),
                    array(
                        'type' => 0,
                        'default' => '',
                        'translate' => 'tx_jokursanmeldung_domain_model_kursanmeldungteilnehmer.ort'
                    ),
                    array(
                        'type' => 0,
                        'default' => '',
                        'translate' => 'tx_jokursanmeldung_domain_model_kursanmeldungteilnehmer.land'
                    ),
                    array(
                        'type' => 0,
                        'default' => '',
                        'translate' => 'tx_jokursanmeldung_domain_model_kursanmeldung.step3.programm'
                    ),
                    array(
                        'type' => 0,
                        'default' => '',
                        'translate' => 'tx_jokursanmeldung_domain_model_kursanmeldung.step3.orchesterstudio'
                    ),
                    array(
                        'type' => 0,
                        'default' => '',
                        'translate' => 'tx_jokursanmeldung_domain_model_kursanmeldung.step3.comment'
                    ),
                    array(
                        'type' => 0,
                        'default' => '',
                        'translate' => 'tx_jokursanmeldung_domain_model_kursanmeldung.uploads'
                    ),
                    array(
                        'type' => 0,
                        'default' => '',
                        'translate' => 'tx_jokursanmeldung_domain_model_kursanmeldung.step3.duosel'
                    ),
                    array(
                        'type' => 0,
                        'default' => '',
                        'translate' => 'tx_jokursanmeldung_domain_model_kursanmeldung.step3.duoname'
                    )
                ),
                'dataKey' => array(
                    'datein',
                    'anmeldestatus',
                    'tn.anrede',
                    'tn.vorname',
                    'tn.nachname',
                    'tn.gebdate',
                    'tn.email',
                    'tn.ort',
                    'tn.land',
                    'programm',
                    'orchesterstudio',
                    'comment',
                    'uploads.name',
                    'duosel',
                    'duoname'
                )
            )
        );
        $ret = false;
        if (isset($sets[$set])) {
            $ret = $sets[$set];
        }
        return $ret;
    }

    protected function getAnmeldestatus($kursanmeldung, $type = 'uid')
    {
        $retObj = null;
        $statusObj = $kursanmeldung->getAnmeldestatus();
        switch ($type) {
            case 'uid':
                if (is_object($statusObj)) {
                    if ($statusObj->current() != null) {
                        $retObj = $statusObj->current()->getUid();
                    }
                }
                break;
            default:
                if (is_object($statusObj)) {
                    if ($statusObj->current() != null) {
                        $retObj = $statusObj->current();
                    }
                }
        }
        return $retObj;
    }

    protected function joTranslate()
    {
        $args = func_get_args();
        $key = array_shift($args);
        return \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key, 'jo_kursanmeldung', $args);
    }

    /**
     * returns the kursname of given kursobj
     *
     * @return string
     */
    protected function getKursname($kursActive, $onlyName = false)
    {
        $kursname = '';
        if (!empty($kursActive) && $kursActive != null) {
            $prof = $this->profRepository->findByUid($kursActive->getProfessor());
            // Name für Head
            if (!empty($prof) && $prof != null) {
                $kursname = $prof->getName() . ' ' . $kursActive->getKurszeitstart()->format(
                        'd.m.Y'
                    ) . ' - ' . $kursActive->getKurszeitend()->format('d.m.Y');
                if (!$onlyName) {
                    $kursname = '<span class="joHeadKurs"> | ' . $kursname . '</span>';
                }
            }
        }
        return $kursname;
    }

    private function getProfInfo($kursActive)
    {
        $profViewArr = array();

        if (!empty($kursActive) && $kursActive != null) {
            //Profs
            $prof = $this->profRepository->findByUid($kursActive->getProfessor());
            if (!empty($prof)) {
                $profViewArr['first_name'] = $prof->getName();
                $profViewArr['image'] = $prof->getImage();
                $profViewArr['path'] = '/uploads/pics/';
                $db = $this->getDb('typo3database');
                if ($_SERVER['SERVER_ADDR'] == '141.54.193.16' && $db) {
                    $res = $db->exec_SELECTquery(
                        '*',
                        'tx_jobase_domain_model_personen',
                        "uid= " . (int)$prof->getLink(
                        ) . " AND FIND_IN_SET(job, '0,2') AND sys_language_uid=0 ORDER BY last_name"
                    );
                    while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
                        $profViewArr = $row;
                        $profViewArr['path'] = 'https://www.hfm-weimar.de/uploads/tx_jobase/';
                    }
                }
            }
        }
        return $profViewArr;
    }

    private function getDb($dbname)
    {
        $db = false;
        if ($_SERVER['SERVER_ADDR'] == '141.54.193.16' && $dbname == 'typo3database') {
            $db = new \TYPO3\CMS\Core\Database\DatabaseConnection();
            $db->setDatabaseHost('localhost');
            $db->setDatabaseName('typo3database');
            $db->setDatabaseUsername('typo3-database');
            $db->setDatabasePassword('71.sumulo');
            $db->connectDB();
        }
        return $db;
    }
}

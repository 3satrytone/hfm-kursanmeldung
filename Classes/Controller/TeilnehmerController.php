<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\Controller;

use Hfm\Kursanmeldung\Domain\Model\Kursanmeldung;
use Hfm\Kursanmeldung\Domain\Repository\AnmeldestatusRepository;
use Hfm\Kursanmeldung\Utility\ParticipantUtility;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation\IgnoreValidation;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Hfm\Kursanmeldung\Domain\Repository\KursRepository;
use Hfm\Kursanmeldung\Domain\Repository\KursanmeldungRepository;
use Hfm\Kursanmeldung\Utility\TypeConverter\IntegerConverter;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

#[AsController]
final class TeilnehmerController extends ActionController
{
    /**
     * page there frontentPlugin is used
     * @var int $fePluginPage
     */
    private int $fePluginPage = 3;
    private array $zahlungsartArr = [
        1 => 'banktransfer',
        2 => 'prepayment',
        3 => 'paypal',
        4 => 'onlinetransfer',
        5 => 'giropay',
        6 => 'invoice',
        7 => 'nopayment'
    ];


    public function __construct(
        protected readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly AnmeldestatusRepository $anmeldestatusRepository,
        private readonly KursRepository $kursRepository,
        private readonly KursanmeldungRepository $kursanmeldungRepository,
        private readonly PersistenceManagerInterface $persistenceManager,
        protected UriBuilder $uriBuilder,
        private readonly ParticipantUtility $participantUtility,
    ) {
    }

    public function initializeAction(): void
    {
        // if dbdata distributed over more pages
        if (isset($this->settings['dataPages'])) {
            if (isset($this->kursRepository)) {
                $this->kursRepository->setStoragePageIds($this->settings['dataPages']);
            }
            if (isset($this->anmeldestatusRepository)) {
                $this->anmeldestatusRepository->setStoragePageIds($this->settings['dataPages']);
            }
        }
        if (isset($this->settings['fePluginPage'])) {
            $this->fePluginPage = (int)$this->settings['fePluginPage'];
        }
    }

    public function listAction(): ResponseInterface
    {
        // Filter-Parameter – getrennt für Gesamtliste und Kurslisten
        // Session-Handling: Filter in Session persistieren und bei fehlenden Request-Parametern daraus laden
        $sessionSearchAllKey = 'hfm.kursanmeldung.teilnehmer.searchAll';
        $sessionFieldsAllKey = 'hfm.kursanmeldung.teilnehmer.fieldsAll';
        $sessionSearchKursKey = 'hfm.kursanmeldung.teilnehmer.searchKurs';
        $sessionFieldsKursKey = 'hfm.kursanmeldung.teilnehmer.fieldsKurs';
        $sessionPagination = 'hfm.kursanmeldung.teilnehmer.pagination';

        // Hilfsfunktionen für Session im BE/FE
        $getSession = static function (string $key) {
            // Backend-Session bevorzugen, falls vorhanden
            if (isset($GLOBALS['BE_USER']) && is_object($GLOBALS['BE_USER'])) {
                $data = $GLOBALS['BE_USER']->getSessionData($key);
                return $data ?: null;
            }
            // Fallback: PHP-Session
            if (session_status() !== PHP_SESSION_ACTIVE) {
                @session_start();
            }
            return $_SESSION[$key] ?? null;
        };
        $setSession = static function (string $key, $value): void {
            if (isset($GLOBALS['BE_USER']) && is_object($GLOBALS['BE_USER'])) {
                $GLOBALS['BE_USER']->setAndSaveSessionData($key, $value);
                return;
            }
            if (session_status() !== PHP_SESSION_ACTIVE) {
                @session_start();
            }
            $_SESSION[$key] = $value;
        };
        $unsetSession = static function (string $key): void {
            if (isset($GLOBALS['BE_USER']) && is_object($GLOBALS['BE_USER'])) {
                $GLOBALS['BE_USER']->setAndSaveSessionData($key, null);
                return;
            }
            if (session_status() !== PHP_SESSION_ACTIVE) {
                @session_start();
            }
            unset($_SESSION[$key]);
        };

        // Reset-Handling (optional) – wenn reset=all gesendet, Session-Filter leeren
        if ($this->request->hasArgument('reset')) {
            $reset = (string)$this->request->getArgument('reset');
            if ($reset === 'all') {
                $unsetSession($sessionSearchAllKey);
                $unsetSession($sessionFieldsAllKey);
                $unsetSession($sessionSearchKursKey);
                $unsetSession($sessionFieldsKursKey);
                $unsetSession($sessionPagination);
            }
        }
        // 1) Gesamtliste
        $searchAll = null;
        $fieldsAll = [];
        if ($this->request->hasArgument('searchAll')) {
            $searchAll = (string)$this->request->getArgument('searchAll');
            // in Session speichern
            $setSession($sessionSearchAllKey, $searchAll);
        } else {
            // aus Session laden
            $stored = $getSession($sessionSearchAllKey);
            if (is_string($stored)) {
                $searchAll = $stored;
            }
        }
        $hasFieldsAllArg = $this->request->hasArgument('fieldsAll');
        if ($hasFieldsAllArg) {
            $argAll = $this->request->getArgument('fieldsAll');
            if (is_array($argAll)) {
                $fieldsAll = $argAll;
            } elseif (is_string($argAll) && $argAll !== '') {
                $fieldsAll = array_map('trim', explode(',', $argAll));
            }
            // in Session speichern
            $setSession($sessionFieldsAllKey, $fieldsAll);
        } else {
            // aus Session laden
            $stored = $getSession($sessionFieldsAllKey);
            if (is_array($stored)) {
                $fieldsAll = $stored;
            }
        }
        if (!$hasFieldsAllArg && (is_array($fieldsAll) && count($fieldsAll) === 0)) {
            $fieldsAll = ['tn.vorname', 'tn.nachname'];
        }

        // 2) Kurslisten: Parameter als Arrays je KursUid
        $searchKurs = [];
        $fieldsKurs = [];
        $openKursUid = null; // erste Kurs-UID mit aktiver Suche
        if ($this->request->hasArgument('searchKurs')) {
            $sk = $this->request->getArgument('searchKurs');
            if (is_array($sk)) {
                // Werte zu String normalisieren
                foreach ($sk as $k => $v) {
                    $searchKurs[(int)$k] = (string)$v;
                }
            }
            // in Session speichern
            $setSession($sessionSearchKursKey, $searchKurs);
        } else {
            // aus Session laden
            $stored = $getSession($sessionSearchKursKey);
            if (is_array($stored)) {
                // normalisieren
                foreach ($stored as $k => $v) {
                    $searchKurs[(int)$k] = (string)$v;
                }
            }
        }
        if ($this->request->hasArgument('fieldsKurs')) {
            $fk = $this->request->getArgument('fieldsKurs');
            if (is_array($fk)) {
                foreach ($fk as $k => $v) {
                    if (is_array($v)) {
                        $fieldsKurs[(int)$k] = $v;
                    } elseif (is_string($v) && $v !== '') {
                        $fieldsKurs[(int)$k] = array_map('trim', explode(',', $v));
                    }
                }
            }
            // in Session speichern
            $setSession($sessionFieldsKursKey, $fieldsKurs);
        } else {
            // aus Session laden
            $stored = $getSession($sessionFieldsKursKey);
            if (is_array($stored)) {
                foreach ($stored as $k => $v) {
                    if (is_array($v)) {
                        $fieldsKurs[(int)$k] = $v;
                    } elseif (is_string($v) && $v !== '') {
                        $fieldsKurs[(int)$k] = array_map('trim', explode(',', $v));
                    }
                }
            }
        }

        // Pagination parameters
        $currentPage = 1;
        $itemsPerPage = 25;
        if ($this->request->hasArgument('page')) {
            $currentPage = max(1, (int)$this->request->getArgument('page'));
            $setSession($sessionPagination, $currentPage);
        } else {
            $currentPage = ((int)$getSession($sessionPagination) === 0) ? 1 : (int)$getSession($sessionPagination);
        }
        if (isset($this->settings['itemsPerPage'])) {
            $itemsPerPage = max(1, (int)$this->settings['itemsPerPage']);
        }

        // All participants with optional search + pagination
        if ($searchAll !== null && trim($searchAll) !== '') {
            $allParticipants = $this->kursanmeldungRepository->searchAll($searchAll, $fieldsAll);
        } else {
            $allParticipants = $this->kursanmeldungRepository->findAllSortedByUid();
        }
        $paginator = new QueryResultPaginator($allParticipants, $currentPage, $itemsPerPage);
        $pagination = new SimplePagination($paginator);

        // Participants grouped by course
        $participantsByCourse = [];
        $courses = $this->kursRepository->findAll();

        foreach ($courses as $kurs) {
            $kUid = (int)$kurs->getUid();
            $kSearch = $searchKurs[$kUid] ?? null;
            $kFields = $fieldsKurs[$kUid] ?? ['tn.vorname', 'tn.nachname'];
            if ($kSearch !== null && trim((string)$kSearch) !== '') {
                $registrations = $this->kursanmeldungRepository->getParticipantsByKursFiltered(
                    $kUid,
                    (string)$kSearch,
                    $kFields
                );
            } else {
                $registrations = $this->kursanmeldungRepository->getParticipantsByKurs($kUid);
            }
            // selected map für Kurs
            $selectedMapKurs = [];
            foreach ($kFields as $f) {
                $selectedMapKurs[str_replace('.', '_', $f)] = true;
            }
            $participantsByCourse[] = [
                'kurs' => $kurs,
                'registrations' => $registrations,
                'search' => $kSearch,
                'selectedFields' => $kFields,
                'selectedMap' => $selectedMapKurs,
            ];
        }

        // Map für Template (Gesamtliste)
        $selectedMapAll = [];
        foreach ($fieldsAll as $f) {
            $selectedMapAll[str_replace('.', '_', $f)] = true;
        }

        $this->view->assignMultiple([
            'paginator' => $paginator,
            'pagination' => $pagination,
            'participantsByCourse' => $participantsByCourse,
            'anmeldestatusList' => $this->anmeldestatusRepository->findAll(),
            'searchAll' => $searchAll,
            'selectedFieldsAll' => $fieldsAll,
            'selectedMapAll' => $selectedMapAll,
            'openKursUid' => $openKursUid,
        ]);

        return $this->htmlResponse();
    }

    public function editAction(Kursanmeldung $kursanmeldung): ResponseInterface
    {
        $gender = $this->participantUtility->getOptions([0 => 'f', 1 => 'm'],
            'tx_kursanmeldung_domain_model_kursanmeldung.step1.');
        $zahlungsart = $this->participantUtility->getOptions(
            $this->zahlungsartArr,
            'tx_kursanmeldung_domain_model_kursanmeldung.step2.'
        );

        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        $site = $siteFinder->getSiteByPageId($this->fePluginPage);

        $dateIn = $kursanmeldung->getDatein()?->getTimestamp() ?? 0;
        $arguments = [
            'tx_kursanmeldung_kursanmeldungfe' => [
                'action' => 'paylater',
                'controller' => 'Frontend',
                'st' => $dateIn . '_' . $kursanmeldung->getUid(),
                'pl' => 'ang',
                'hash' => base64_encode($kursanmeldung->getRegistrationkey()),
            ]
        ];
        $paylater['ang'] = (string)$site->getRouter()->generateUri($this->fePluginPage, $arguments);

        $arguments = [
            'tx_kursanmeldung_kursanmeldungfe' => [
                'action' => 'paylater',
                'controller' => 'Frontend',
                'st' => $dateIn . '_' . $kursanmeldung->getUid(),
                'pl' => 'tng',
                'hash' => base64_encode($kursanmeldung->getRegistrationkey()),
            ]
        ];
        $paylater['tng'] = (string)$site->getRouter()->generateUri($this->fePluginPage, $arguments);

        $teilnahmeartOpt = array(
            '' => LocalizationUtility::translate(
                'teilnahmeart.choose',
                'kursanmeldung'
            ),
            0 => LocalizationUtility::translate(
                'teilnahmeart.0',
                'kursanmeldung'
            ),
            1 => LocalizationUtility::translate(
                'teilnahmeart.1',
                'kursanmeldung'
            )
        );

        $deflangOpt = array(
            0 => 'deutsch',
            1 => 'englisch'
        );

        $statuus = $this->anmeldestatusRepository->findAll();
        $newkurs = $this->getKursOptions();
        $tnaction = $this->participantUtility->getOptions(
            [0,1],
            'tx_kursanmeldung_domain_model_teilnehmer.tnart.'
        );

        $hotel = $this->participantUtility->splitHotel($kursanmeldung->getKurs()->getHotel());

        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);

        $moduleTemplate->assign('teilnahmeartOpt', $teilnahmeartOpt);
        $moduleTemplate->assign('deflangOpt', $deflangOpt);
        $moduleTemplate->assign('kursanmeldung', $kursanmeldung);
        $moduleTemplate->assign('statuus', $statuus);
        $moduleTemplate->assign('newkurs', $newkurs);
        $moduleTemplate->assign('gender', $gender);
        $moduleTemplate->assign('zahlungsart', $zahlungsart);
        $moduleTemplate->assign('paylater', $paylater);
        $moduleTemplate->assign('hotels', $hotel);
        $moduleTemplate->assign('tnaction', $tnaction);

        return $moduleTemplate->renderResponse('Teilnehmer/Edit');
    }

    /**
     * initialize update action
     *
     * @return void
     */
    public function initializeUpdateAction(): void
    {
        try {
            if ($this->arguments->hasArgument('kursanmeldung')) {
                $pmc = $this->arguments
                    ->getArgument('kursanmeldung')
                    ->getPropertyMappingConfiguration();
                $pmc->allowAllProperties();
                $pmc->forProperty('studystat')
                    ->setTypeConverter(GeneralUtility::makeInstance(IntegerConverter::class));
                $pmc->forProperty('studentship')
                    ->setTypeConverter(GeneralUtility::makeInstance(IntegerConverter::class));
                $pmc->forProperty('stipendiat')
                    ->setTypeConverter(GeneralUtility::makeInstance(IntegerConverter::class));
                $pmc->forProperty('bezahlt')
                    ->setTypeConverter(GeneralUtility::makeInstance(IntegerConverter::class));
                $pmc->forProperty('duo')
                    ->setTypeConverter(GeneralUtility::makeInstance(IntegerConverter::class));
                $pmc->forProperty('tn.0.gebdate')
                    ->setTypeConverter(
                        GeneralUtility::makeInstance(DateTimeConverter::class)
                    )
                    ->setTypeConverterOption(
                        DateTimeConverter::class,
                        DateTimeConverter::CONFIGURATION_DATE_FORMAT,
                        'Y-m-d'
                    );
                $pmc->forProperty('zahltbis')
                    ->setTypeConverter(
                        GeneralUtility::makeInstance(DateTimeConverter::class)
                    )
                    ->setTypeConverterOption(
                        DateTimeConverter::class,
                        DateTimeConverter::CONFIGURATION_DATE_FORMAT,
                        'Y-m-d\TH:i'
                    );
                $pmc->forProperty('doitime')
                    ->setTypeConverter(
                        GeneralUtility::makeInstance(DateTimeConverter::class)
                    )
                    ->setTypeConverterOption(
                        DateTimeConverter::class,
                        DateTimeConverter::CONFIGURATION_DATE_FORMAT,
                        'Y-m-d\TH:i'
                    );
                $pmc->forProperty('datein')
                    ->setTypeConverter(
                        GeneralUtility::makeInstance(DateTimeConverter::class)
                    )
                    ->setTypeConverterOption(
                        DateTimeConverter::class,
                        DateTimeConverter::CONFIGURATION_DATE_FORMAT,
                        'Y-m-d\TH:i'
                    );
                $pmc->forProperty('gebuehrdat')
                    ->setTypeConverter(
                        GeneralUtility::makeInstance(DateTimeConverter::class)
                    )
                    ->setTypeConverterOption(
                        DateTimeConverter::class,
                        DateTimeConverter::CONFIGURATION_DATE_FORMAT,
                        'Y-m-d\TH:i'
                    );
            }

            // Für skalare Action-Argumente ist i. d. R. keine spezielle
            // Konfiguration nötig; wir prüfen dennoch auf Existenz, damit
            // Extbase den Request-Wert (z. B. newkursuid=123) sauber mappen kann.
            if ($this->arguments->hasArgument('newkursuid')) {
                $this->arguments
                    ->getArgument('newkursuid')
                    ->getPropertyMappingConfiguration()
                    ->allowAllProperties();
            }
        } catch (\Throwable $e) {
            // still: keine harte Ausnahme im Initializer auslösen
        }
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Kursanmeldung $kursanmeldung
     * @param int $newkursuid
     * @return \Psr\Http\Message\ResponseInterface
     */
    #[IgnoreValidation(['argumentName' => 'kursanmeldung'])]
    public function updateAction(Kursanmeldung $kursanmeldung, int $newkursuid = 0): ResponseInterface
    {
        $redirect = 'list';

        try {
            if (!empty($kursanmeldung)) {
                if ($newkursuid && $newkursuid !== $kursanmeldung->getKurs()->getUid()) {
                    $kurs = $this->kursRepository->findByUid($newkursuid);
                    if ($kurs !== null) {
                        $kursanmeldung->setKurs($kurs);
                    }
                }

                $kursanmeldungArg = $this->request->getArgument('kursanmeldung');
                if(isset($kursanmeldungArg['anmeldestatus']) && (int)$kursanmeldungArg['anmeldestatus'] > 0){
                    $status = $this->anmeldestatusRepository->findByUid((int)$kursanmeldungArg['anmeldestatus']);
                    if($status !== null) {
                        $objStorage = new ObjectStorage();
                        $objStorage->attach($status);
                        $kursanmeldung->setAnmeldestatus($objStorage);
                    }
                }

                if(isset($kursanmeldungArg['profstatus']) && (int)$kursanmeldungArg['profstatus'] > 0){
                    $profstatus = $this->anmeldestatusRepository->findByUid((int)$kursanmeldungArg['profstatus']);
                    if($profstatus !== null) {
                        $objStorage = new ObjectStorage();
                        $objStorage->attach($profstatus);
                        $kursanmeldung->setProfstatus($objStorage);
                    }
                }

                $this->kursanmeldungRepository->update($kursanmeldung);
                $this->persistenceManager->persistAll();
            }

            $this->addFlashMessage(
                $this->participantUtility->translateFromXlf('tx_kursanmeldung_domain_model_kursanmeldung.ok001_body'),
                $this->participantUtility->translateFromXlf('tx_kursanmeldung_domain_model_kursanmeldung.ok001_title'),
                ContextualFeedbackSeverity::OK
            );
        } catch (\Exception $e) {
            $this->addFlashMessage(
                $e->getMessage(),
                $this->participantUtility->translateFromXlf('tx_kursanmeldung_domain_model_kursanmeldung.err003'),
                ContextualFeedbackSeverity::ERROR
            );
        }

        return $this->redirect('list');
    }

    public function updateAnmeldestatusAction(): ResponseInterface
    {
        try {
            $kaUid = (int)($this->request->hasArgument('kursanmeldung') ? $this->request->getArgument(
                'kursanmeldung'
            ) : 0);
            $astUid = (int)($this->request->hasArgument('anmeldestatus') ? $this->request->getArgument(
                'anmeldestatus'
            ) : 0);

            if ($kaUid <= 0 || $astUid < 0) {
                $response = $this->htmlResponse(json_encode(['success' => false, 'error' => 'invalid_arguments']));
                return $response->withHeader('Content-Type', 'application/json');
            }

            /** @var \Hfm\Kursanmeldung\Domain\Model\Kursanmeldung|null $ka */
            $ka = $this->kursanmeldungRepository->findByIdentifier($kaUid);
            if ($ka === null) {
                $response = $this->htmlResponse(json_encode(['success' => false, 'error' => 'not_found']));
                return $response->withHeader('Content-Type', 'application/json');
            }

            $storage = new ObjectStorage();
            if ($astUid > 0) {
                /** @var \Hfm\Kursanmeldung\Domain\Model\Anmeldestatus|null $status */
                $status = $this->anmeldestatusRepository->findByIdentifier($astUid);
                if ($status === null) {
                    $response = $this->htmlResponse(json_encode(['success' => false, 'error' => 'status_not_found']));
                    return $response->withHeader('Content-Type', 'application/json');
                }
                $storage->attach($status);
            }

            $ka->setAnmeldestatus($storage);
            $this->kursanmeldungRepository->update($ka);
            $this->persistenceManager->persistAll();

            $response = $this->htmlResponse(json_encode(['success' => true]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Throwable $e) {
            $response = $this->htmlResponse(
                json_encode(['success' => false, 'error' => 'exception', 'message' => $e->getMessage()])
            );
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public function deleteAction(Kursanmeldung $kursanmeldung): ResponseInterface
    {
        try{
            $this->kursanmeldungRepository->remove($kursanmeldung);
            $this->addFlashMessage(
                $this->participantUtility->translateFromXlf('tx_kursanmeldung_domain_model_kursanmeldung.ok002_body'),
                $this->participantUtility->translateFromXlf('tx_kursanmeldung_domain_model_kursanmeldung.ok002_title'),
                ContextualFeedbackSeverity::OK
            );
        } catch (\Exception $e) {
            $this->addFlashMessage(
                $e->getMessage(),
                $this->participantUtility->translateFromXlf('tx_kursanmeldung_domain_model_kursanmeldung.err003'),
                ContextualFeedbackSeverity::ERROR
            );
        }

        return $this->redirect('list');
    }

    protected function getKursOptions(): array
    {
        $kursOpt = array();
        $kurse = $this->kursRepository->findAll();
        if (!empty($kurse) && $kurse->count() > 0) {
            foreach ($kurse as $kurs) {
                $prof = $kurs->getProfessor();
                $name = $kurs->getInstrument();
                if (!empty($prof)) {
                    $name .= ', ' . $prof->getName();
                }
                $kursOpt[] = array('uid' => $kurs->getUid(), 'name' => $name);
            }
        }
        return $kursOpt;
    }
}

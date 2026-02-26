<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\Controller;

use Exception;
use Hfm\Kursanmeldung\App\Dto\MailDto;
use Hfm\Kursanmeldung\App\Mail\Business\MailFacade;
use Hfm\Kursanmeldung\Domain\Model\Kursanmeldung;
use Hfm\Kursanmeldung\Domain\Model\Teilnehmer;
use Hfm\Kursanmeldung\Domain\Repository\AnmeldestatusRepository;
use Hfm\Kursanmeldung\Domain\Repository\ProfStatusRepository;
use Hfm\Kursanmeldung\Utility\ParticipantUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Mail\FluidEmail;
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
final class TeilnehmerController extends ActionController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

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
    protected $nameVeranstaltung = 'Weimarer Meisterkurse';
    protected $emailHostAddress = 'wiebke.eckardt@hfm-weimar.de';
    protected $emailHostAddressAdmin = 'wiebke.eckardt@hfm-weimar.de';
    protected $emailHostAddressCc = 'info@schneider-software-service.de';
    protected $emailHostName = '';
    protected $emailSubject = 'Ihre Kursanmeldung bei der Hochschule für Musik, bitte bestätigen';
    protected $emailSubjectAdmin = 'Admin: Kursanmeldung bei der Hochschule für Musik';
    protected $emailSubjectInfo = 'Ihre Kursanmeldung bei der Hochschule für Musik';
    protected $emailSubjectInvoice = 'Ihre Kursanmeldung bei der Hochschule für Musik, bitte Rechnung begleichen';
    protected $testmode = 1;
    protected $novalnetSecret = '81c2f886f91e18fe16d6f4e865877cb6';


    public function __construct(
        protected readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly AnmeldestatusRepository $anmeldestatusRepository,
        private readonly KursRepository $kursRepository,
        private readonly KursanmeldungRepository $kursanmeldungRepository,
        private readonly ProfStatusRepository $profStatusRepository,
        private readonly PersistenceManagerInterface $persistenceManager,
        protected UriBuilder $uriBuilder,
        private readonly ParticipantUtility $participantUtility,
        private readonly MailFacade $mailFacade,
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

        $this->profStatusRepository->setRespectStoragePage(false);
        $profStatuus = $this->profStatusRepository->findAll();
        foreach ($profStatuus as $profStatus) {
            if (!isset($profStatusExplained[$profStatus->getKursanmeldung()])) {
                $profStatusExplained[$profStatus->getKursanmeldung()][$profStatus->getKurz()] = 0;
            }
            $profStatusExplained[$profStatus->getKursanmeldung()][$profStatus->getKurz()]++;
        }

        $language =
            $this->request->getAttribute('language')
            ?? $this->request->getAttribute('site')->getDefaultLanguage();

        $this->view->assignMultiple([
            'paginator' => $paginator,
            'pagination' => $pagination,
            'participantsByCourse' => $participantsByCourse,
            'anmeldestatusList' => $this->anmeldestatusRepository->findAll(),
            'searchAll' => $searchAll,
            'selectedFieldsAll' => $fieldsAll,
            'selectedMapAll' => $selectedMapAll,
            'openKursUid' => $openKursUid,
            'profStatusSum' => $profStatusExplained,
            'lang' => $language,
        ]);

        return $this->htmlResponse();
    }

    public function exportAction(): ResponseInterface
    {
        $sessionSearchAllKey = 'hfm.kursanmeldung.teilnehmer.searchAll';
        $sessionFieldsAllKey = 'hfm.kursanmeldung.teilnehmer.fieldsAll';
        $sessionSearchKursKey = 'hfm.kursanmeldung.teilnehmer.searchKurs';
        $sessionFieldsKursKey = 'hfm.kursanmeldung.teilnehmer.fieldsKurs';

        $getSession = static function (string $key) {
            if (isset($GLOBALS['BE_USER']) && is_object($GLOBALS['BE_USER'])) {
                $data = $GLOBALS['BE_USER']->getSessionData($key);
                return $data ?: null;
            }
            if (session_status() !== PHP_SESSION_ACTIVE) {
                @session_start();
            }
            return $_SESSION[$key] ?? null;
        };

        $kursUid = 0;

        if($this->request->hasArgument('kurs')){
            $kursUid = (int)$this->request->getArgument('kurs');
        }

        if ($kursUid > 0) {
            $searchKurs = $getSession($sessionSearchKursKey);
            $fieldsKurs = $getSession($sessionFieldsKursKey);
            $kSearch = $searchKurs[$kursUid] ?? null;
            $kFields = $fieldsKurs[$kursUid] ?? ['tn.vorname', 'tn.nachname'];

            if ($kSearch !== null && trim((string)$kSearch) !== '') {
                $participants = $this->kursanmeldungRepository->getParticipantsByKursFiltered($kursUid, (string)$kSearch, $kFields);
            } else {
                $participants = $this->kursanmeldungRepository->getParticipantsByKurs($kursUid);
            }
            $filename = 'kurs_' . $kursUid . '_export_' . date('Y-m-d_H-i') . '.csv';
        } else {
            $searchAll = $getSession($sessionSearchAllKey);
            $fieldsAll = $getSession($sessionFieldsAllKey) ?: ['tn.vorname', 'tn.nachname'];

            if ($searchAll !== null && trim((string)$searchAll) !== '') {
                $participants = $this->kursanmeldungRepository->searchAll((string)$searchAll, $fieldsAll);
            } else {
                $participants = $this->kursanmeldungRepository->findAllSortedByUid();
            }
            $filename = 'alle_teilnehmer_export_' . date('Y-m-d_H-i') . '.csv';
        }

        $handle = fopen('php://temp', 'r+');
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

        $header = [
            'Lfd. Nr.',
            'Reg. Nr.',
            'Anmeldedatum',
            'Teilnahmeart',
            'Status',
            'Stipendiat',
            'Bezahlt ANG',
            'Anrede',
            'Vorname',
            'Nachname',
            'Titel',
            'Geb. Datum',
            'Matrikel',
            'Nationalität',
            'Strasse',
            'Adresszusatz',
            'PLZ',
            'Ort',
            'Land',
            'Telefon',
            'Mobil',
            'E-Mail',
            'Daten- speicherung zugestimmt',
            'Hotel',
            'Zimmer',
            'Zimmer mit',
            'Zimmer von',
            'Zimmer bis',
            'Duett',
            'Duettart',
            'Duettpartner',
            'ANG Gebühr',
            'ANG Gezahlt',
            'TNG Gezahlt',
            'OG Gezahlt',
            'Kommentar',
            'Kurs Nr',
            'Instrument',
            'Professor',
            'Kursstart',
            'Kursende',
            'Programm',
            'Orchesterstudio',
            'Notiz'
        ];
        fputcsv($handle, $header, ';');

        $i=0;
        foreach ($participants as $reg) {
            $tn = $reg->getTn()->current();
            $row = [
                ++$i,
                $reg->getUid(),
                $reg->getDatein()?->format('d.m.Y H:i') ?? '',
                $reg->getTeilnahmeart() === 0 ? 'PT': 'AT',
                $reg->getAnmeldestatus()->current()?->getKurz() ?? '',
                $reg->getStipendiat() === 0 ? 'Nein' : 'Ja',
                $reg->getBezahlt() === 0 ? 'Nein' : 'Ja',
                $tn?->getAnrede() === 0 ? 'Frau' : 'Herr',
                $tn?->getVorname() ?? '',
                $tn?->getNachname() ?? '',
                $tn?->getTitel() ?? '',
                $tn?->getGebdate()?->format('d.m.Y') ?? '',
                $tn?->getMatrikel() ?? '',
                $tn?->getNation() ?? '',
                $tn?->getAdresse1() ?? '' . $tn?->getHausnr() ?? '',
                $tn?->getAdresse2() ?? '',
                $tn?->getPlz() ?? '',
                $tn?->getOrt() ?? '',
                $tn?->getLand() ?? '',
                $tn?->getTelefon() ?? '',
                $tn?->getMobil() ?? '',
                $tn?->getEmail() ?? '',
                $reg->getSavedata() === 0 ? 'Datenverarbeitung nicht akzeptiert' : 'Datenverarbeitung akzeptiert',
                $reg->getHotel(),
                LocalizationUtility::translate(
                    'be_export.room.' . $reg->getRoom(),
                    'kursanmeldung'
                ),
                $reg->getRoomwith(),
                $reg->getRoomfrom(),
                $reg->getRoomto(),
                $reg->getDuo(),
                $reg->getDuosel(),
                $reg->getDuoname(),
                (string)$reg->getGebuehr(),
                $reg->getGezahlt(),
                (string)$reg->getGezahltag(),
                (string)$reg->getGezahltos(),
                $reg->getComment(),
                $reg->getKurs()?->getKursnr() ?? '',
                $reg->getKurs()?->getInstrument() ?? '',
                $reg->getKurs()?->getProfessor()->getName() ?? '',
                $reg->getKurs()?->getKurszeitstart()?->format('d.m.Y') ?? '',
                $reg->getKurs()?->getKurszeitend()?->format('d.m.Y') ?? '',
                $reg->getProgramm(),
                $reg->getOrchesterstudio(),
                $reg->getNotice(),
            ];
            fputcsv($handle, $row, ';');
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'text/csv; charset=utf-8')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->withHeader('Pragma', 'no-cache')
            ->withHeader('Expires', '0');

        $response->getBody()->write($csvContent);
        return $response;
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
            [0, 1],
            'tx_kursanmeldung_domain_model_teilnehmer.tnart.'
        );

        $hotel = $this->participantUtility->splitHotel($kursanmeldung->getKurs()->getHotel());

        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);

        $this->profStatusRepository->setRespectStoragePage(false);
        $profStatuus = $this->profStatusRepository->findByKursanmeldung($kursanmeldung->getUid());
        $profStatusExplained = [];
        foreach ($profStatuus as $profStatus) {
            if (!isset($profStatusExplained[$profStatus->getKursanmeldung()])) {
                $profStatusExplained[$profStatus->getKursanmeldung()][$profStatus->getKurz()] = 0;
            }
            $profStatusExplained[$profStatus->getKursanmeldung()][$profStatus->getKurz()]++;
        }

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
        $moduleTemplate->assign('profStatusSum', $profStatusExplained);

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
                $pmc->forProperty('bezahltag')
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
                if (isset($kursanmeldungArg['anmeldestatus']) && (int)$kursanmeldungArg['anmeldestatus'] > 0) {
                    $status = $this->anmeldestatusRepository->findByUid((int)$kursanmeldungArg['anmeldestatus']);
                    if ($status !== null) {
                        $objStorage = new ObjectStorage();
                        $objStorage->attach($status);
                        $kursanmeldung->setAnmeldestatus($objStorage);
                    }
                }

                if (isset($kursanmeldungArg['profstatus']) && (int)$kursanmeldungArg['profstatus'] > 0) {
                    $profstatus = $this->anmeldestatusRepository->findByUid((int)$kursanmeldungArg['profstatus']);
                    if ($profstatus !== null) {
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
        try {
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

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    public function paybyadminAction(): ResponseInterface
    {
        $this->logger->info('paybyadminAction arguments:' . print_r($this->request->getArguments(), true));

        if ($this->request->hasArgument('payment') && $this->request->hasArgument('reguid')) {
            $args = $this->request->getArguments();

            $competition = $this->kursanmeldungRepository->findByUid($args['reguid']);

            if (!empty($competition)) {
                if ($competition->getBezahlt() === 0 && empty($competition->getNovalnettid())) {
                    switch (intval($args['payment'])) {
                        case 1:
                            $payment = 'banktransfer';
                            $competition->setZahlart("1");
                            $banktransfer['tid'] = $this->paymentReason($competition);
                            $banktransfer['invoice_account_name'] = $this->translate(
                                'tx_kursanmeldung.complete.invoicemail.invoice_account_name'
                            );
                            if($banktransfer['invoice_account_name'] === 'tx_kursanmeldung.complete.invoicemail.invoice_account_name'){
                                $banktransfer['invoice_account_name'] = '';
                            }
                            $banktransfer['invoice_bankcode'] = $this->translate(
                                'tx_kursanmeldung.complete.invoicemail.invoice_bankcode'
                            );
                            if($banktransfer['invoice_bankcode'] === 'tx_kursanmeldung.complete.invoicemail.invoice_bankcode'){
                                $banktransfer['invoice_bankcode'] = '';
                            }
                            $banktransfer['invoice_iban'] = $this->translate(
                                'tx_kursanmeldung.complete.invoicemail.invoice_iban'
                            );
                            if($banktransfer['invoice_iban'] === 'tx_kursanmeldung.complete.invoicemail.invoice_iban'){
                                $banktransfer['invoice_iban'] = '';
                            }
                            $banktransfer['invoice_bic'] = $this->translate(
                                'tx_kursanmeldung.complete.invoicemail.invoice_bic'
                            );
                            if($banktransfer['invoice_bic'] === 'tx_kursanmeldung.complete.invoicemail.invoicemail'){
                                $banktransfer['invoice_bic'] = '';
                            }
                            $banktransfer['invoice_bankname'] = $this->translate(
                                'tx_kursanmeldung.complete.invoicemail.invoice_bankname'
                            );
                            if($banktransfer['invoice_bankname'] === 'tx_kursanmeldung.complete.invoicemail.invoice_bankname'){
                                $banktransfer['invoice_bankname'] = '';
                            }
                            $banktransfer['invoice_bankplace'] = $this->translate(
                                'tx_kursanmeldung.complete.invoicemail.invoice_bankplace'
                            );
                            if($banktransfer['invoice_bankplace'] === 'tx_kursanmeldung.complete.invoicemail.invoice_bankplace'){
                                $banktransfer['invoice_bankplace'] = '';
                            }
                            $this->logger->info('banktransfer suc CURL:' . print_r($banktransfer, true));
                            $this->kursanmeldungRepository->update($competition);
                            $this->persistenceManager->persistAll();

                            // emails versenden
                            $this->sendInvoiceMail($competition, $banktransfer);
                            $this->addFlashMessage(
                                '',
                                'Daten erfolgreich versendet.',
                                ContextualFeedbackSeverity::OK
                            );

                            return $this->redirect('edit', null, null, ['kursanmeldung' => $competition]);
                        case 6:
                            $competition->setZahlart('6');
                            $novalnetArr = $this->novalnetArray($competition);
                            $novalnetXML[1]['url'] = 'https://payport.novalnet.de/payport.xml';
                            $novalnetXML[1]['path'] = 'Novalnet/VorkassePayport.html';
                            $extConf = $this->configurationManager->getConfiguration(
                                \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
                            );
                            $templPath = str_replace(
                                'Templates/Backend/',
                                'Templates/',
                                $extConf['view']['templateRootPath']
                            );
                            $novalnetXML[1]['doc'] = $this->getContent(
                                $templPath . $novalnetXML[1]['path'],
                                $novalnetArr
                            );
                            $request = $novalnetXML[1];

                            $xml_response = $this->curl_xml_post(
                                $request
                            ); // Die Variable $request enthält den XML-Aufruf. Sehen Sie sich dazu das Aufrufbeispiel oben an

                            $response = new \SimpleXMLElement($xml_response);
                            $this->logger->info('novalnetredirect suc CURL:' . print_r($response, true));

                            $novalnet['status'] = (string)$response->{transaction_response}->status;
                            $novalnet['tid'] = (string)$response->{transaction_response}->tid;
                            $novalnet['amount'] = (string)$response->{transaction_response}->amount;
                            $novalnet['invoice_account_name'] = 'NOVALNET AG';
                            $novalnet['customer_no'] = (string)$response->{transaction_response}->{customer_no};
                            $novalnet['invoice_account'] = (string)$response->{transaction_response}->{invoice_account};
                            $novalnet['invoice_bankcode'] = (string)$response->{transaction_response}->{invoice_bankcode};
                            $novalnet['invoice_iban'] = (string)$response->{transaction_response}->{invoice_iban};
                            $novalnet['invoice_bic'] = (string)$response->{transaction_response}->{invoice_bic};
                            $novalnet['invoice_bankname'] = (string)$response->{transaction_response}->{invoice_bankname};
                            $novalnet['invoice_bankplace'] = (string)$response->{transaction_response}->{invoice_bankplace};

                            $competition->setNovalnettid($novalnet['tid']);
                            $competition->setNovalnetcno($novalnet['customer_no']);

                            $this->logger->info('novalnetcurl suc POST:' . print_r($novalnet, true));

                            $this->kursanmeldungRepository->update($competition);
                            $this->persistenceManager->persistAll();

                            // emails versenden
                            $this->sendInvoiceMail($competition, $novalnet);
                            $this->addFlashMessage(
                                '',
                                'Daten erfolgreich versendet.',
                                ContextualFeedbackSeverity::OK
                            );
                            return $this->redirect('edit', null, null, ['kursanmeldung' => $competition]);
                            break;
                        default:
                            $this->addFlashMessage(
                                'Zahlungsart nicht erlaubt.',
                                'Es ist ein Fehler aufgetreten!',
                                ContextualFeedbackSeverity::ERROR
                            );
                            return $this->redirect('edit', null, null, ['kursanmeldung' => $competition]);
                    }
                } else {
                    $this->addFlashMessage(
                        'Zahlungsvorgang bereits eingeleitet oder abgeschlossen',
                        'Es ist ein Fehler aufgetreten!',
                        ContextualFeedbackSeverity::ERROR
                    );
                    return $this->redirect('edit', null, null, ['kursanmeldung' => $competition]);
                }
            } else {
                $this->addFlashMessage(
                    'Registrierung oder Teinehmer nicht gefunden',
                    'Es ist ein Fehler aufgetreten!',
                    ContextualFeedbackSeverity::ERROR
                );
                return $this->redirect('list');
            }
        } else {
            $this->addFlashMessage(
                'Zahlungsart und/oder Teinehmerid nicht übergeben',
                'Es ist ein Fehler aufgetreten!',
                ContextualFeedbackSeverity::ERROR
            );
            return $this->redirect('list');
        }
    }

    /**
     * @param string $key
     * @param array $arguments
     * @return string
     */
    protected function translate(string $key, array $arguments = []): string
    {
        return LocalizationUtility::translate($key, 'kursanmeldung', $arguments) ?? $key;
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Kursanmeldung|null $kursanmeldung
     * @param array $opt
     * @return array
     */
    public function novalnetArray(?Kursanmeldung $kursanmeldung, array $opt = []): array
    {
        $lang = 'DE';
        $formVars = [];
        $test_mode = $this->testmode;
        $password = $this->novalnetSecret;

        $language = $this->request->getAttribute('language') ?? $this->request->getAttribute(
            'site'
        )->getDefaultLanguage();
        $lang = $language->getLocale()->getCountryCode();

        if ($kursanmeldung !== null) {
            $tn = null;
            if (count($kursanmeldung->getTn()) > 0) {
                $tnArr = $kursanmeldung->getTn();
                $tnArr->rewind();
                $tn = $tnArr->current();
            }
            $header['authCode'] = 'VgiolIhucYBzcHLszD0p9XmBHf57AU';    // Ja	Authentifizierungscode
            $header['HaendlerID'] = 2704;                            // Ja	Ihre Händler-ID
            $header['ProductID'] = 3861;                            // Ja	Ihre Projekt-ID
            $header['TarifID'] = 6511;

            $customer['customer_id'] = '';                                    // Nein	Novalnet-Kundennummer für die Rechnung
            $customer['customer_no'] = '';                                    // Nein Kundennummer aus dem Shop
            if ($tn != null) {
                $customer['customer_no'] = $tn->getUid();
            }
            $customer['language'] = $lang;                            // Ja 	Sprachcode aus 2 Buchstaben DE,EN
            $customer['company'] = '';                                // Nein	Name des Unternehmens
            $customer['tax_id'] = '';                                // Nein	USt-IdNr.
            $customer['tax_no'] = '';                                // Nein	Steuernummer
            $customer['gender'] = 'u';                                // Ja	Geschlecht des Endkunden m=männlich, f=weiblich, u=unbekannt
            if ($tn != null) {
                switch ($tn->getAnrede()) {
                    case 1:
                        $customer['gender'] = 'm';
                        break;
                    case 0:
                        $customer['gender'] = 'f';
                        break;
                }
            }
            $customer['title'] = '';                                // Nein	Titel des Endkunden Dr.,Prof.
            if ($tn != null) {
                $customer['title'] = $tn->getTitel();
            }
            $customer['first_name'] = '';                            // Ja	Vorname des Endkunden
            if ($tn != null) {
                $customer['first_name'] = $tn->getVorname();
            }
            $customer['last_name'] = '';                            // Ja	Nachname des Endkunden
            if ($tn != null) {
                $customer['last_name'] = $tn->getNachname();
            }
            $customer['tel'] = '';                                    // Nein	Telefonnummer des Endkunden
            if ($tn != null) {
                $customer['tel'] = $tn->getTelefon();
            }
            $customer['fax'] = '';                                    // Nein	Faxnummer des Endkunden
            $customer['mobile'] = '';                                // Nein	Mobiltelefonnummer des Endkunden
            if ($tn != null) {
                $customer['mobile'] = $tn->getMobil();
            }
            $customer['email'] = '';                                // Ja	E-Mail-Adresse des Endkunden
            if ($tn != null) {
                $customer['email'] = $tn->getEmail();
            }
            $customer['street'] = '';                                // Ja	Straße des Endkunden
            if ($tn != null) {
                $customer['street'] = $tn->getAdresse1();
            }
            $customer['house_no'] = '';                                // Nein	Hausnummer des Endkunden
            if ($tn != null) {
                $customer['house_no'] = $tn->getHausnr();
            }
            $customer['postbox'] = '';                                // Nein	Postfach
            $customer['zip'] = '';                                    // Ja	Postleitzahl des Endkunden
            if ($tn != null) {
                $customer['zip'] = $tn->getPlz();
            }
            $customer['city'] = '';                                    // Ja	Stadt bzw. Wohnort des Endkunden
            if ($tn != null) {
                $customer['city'] = $tn->getOrt();
            }
            $customer['country_code'] = 'DE';                        // Ja	Ländercode des Endkunden als ISO-3166-Code mit 2 Buchstaben (z.B. DE für Deutschland)
            if ($tn != null) {
                $country = $this->countryProvider->getByIsoCode($tn->getLand());
                $customer['country_code'] = $country->getAlpha2IsoCode();
            }
            $customer['birthday'] = '';                                    // Ja	Stadt bzw. Wohnort des Endkunden
            if ($tn != null) {
                $customer['birthday'] = $tn->getGebdate()->format('Y-m-d');
            }

            $invoice['remote_ip'] = $_SERVER['REMOTE_ADDR'];
            $invoice['nc_no'] = '';                                    // Ja	Von Novalnet bei der Rückmeldung zur Zahlungstransaktion zurückgegebene Novalcard-Nummer
            $invoice['product_url'] = '';                            // Nein	Ihr Projekt-URL
            $invoice['product_url'] = (isset($_SERVER['HTTPS'])) ? 'https://' . $_SERVER['HTTP_HOST'] : 'http://' . $_SERVER['HTTP_HOST'];
            $invoice['month'] = '';                                    // Ja	Aktueller Monat im Format “YYYY-MM”
            $invoice['month'] = $kursanmeldung->getDatein()->format('Y-m');
            $invoice['invoice_date'] = '';                            // Ja	Rechnungsdatum im Format “YYYY-MM-DD”
            $invoice['invoice_date'] = $kursanmeldung->getDatein()->format('Y-m-d');
            $invoice['tid'] = '';                                    // Ja	17-stellige Novalnet-Transaktionsnummer
            $invoice['reference'] = '';                                // Nein	Rechnungsnummer
            $invoice['type'] = 'DEBIT';                                // Ja	Rechnungstyp CREDIT,DEBIT
            $invoice['order_no'] = '';                                // Nein	Bestellnummer aus dem Shop
            $invoice['order_no'] = base64_encode($kursanmeldung->getRegistrationkey()) . '_' . $kursanmeldung->getUid();
            $invoice['order_uid'] = '';                                // Nein	Bestellnummer aus dem Shop
            $invoice['order_uid'] = $kursanmeldung->getUid();
            $invoice['currency'] = 'EUR';                            // Ja	Währung
            $invoice['net_sum'] = 0;                                // Ja	Nettobetrag insgesamt in der kleinsten Währungseinheit
            $gebuehr = (isset($opt['geb']) && !empty($opt['geb'])) ? $opt['geb'] : $kursanmeldung->getGebuehr();
            $invoice['net_sum'] = $gebuehr * 100;
            $invoice['coupon_percent'] = 0;                            // Nein	Ermäßigung in Prozent
            $invoice['coupon_amount'] = '';                            // Nein	Betrag der Ermäßigung in der kleinsten Währungseinheit
            $invoice['tax_percentage'] = 0;                            // Nein	Mehrwertsteuer in Prozent
            $invoice['tax_sum'] = 0;                                // Nein	Betrag der Mehrwertsteuer in der kleinsten Währungseinheit
            $invoice['gross_sum'] = 0;                                // Ja	Bruttobetrag in der kleinsten Währungseinheit
            $invoice['gross_sum'] = $gebuehr * 100;
            $invoice['notice_line1'] = '';                            // Nein	Benutzerdefiniertes Rechnungsfeld 1
            $invoice['notice_line1'] = 'Umsatzsteuerbefreit aufgrund § 4 Nr. 22b UStG';
            $invoice['notice_line2'] = '';                            // Nein	Benutzerdefiniertes Rechnungsfeld 2
            $invoice['notice_line3'] = '';                            // Nein	Benutzerdefiniertes Rechnungsfeld 3
            $invoice['due_date'] = '';                                // Nein	Fälligkeitsdatum der Rechnung im Format “YYYY-MM-DD”. Nur für Zahlungen auf Rechnung
            $invoice['payment'] = 0;                                // Ja	ID der Novalnet-Zahlungsart 6 = Kreditkarte 27 = Kauf auf Rechnung und Vorkasse 33 = Onlineüberweisung 34 = PayPal 37 = SEPA-Lastschrift 49 = iDEAL 55 = SEPA-Lastschrift mit unterschriebenem Mandat
            //array(1=>'banktransfer', 2=>'prepayment', 3=>'paypal', 4=>'onlinetransfer', 5=>'giropay', 6=>'invoice');
            switch ($kursanmeldung->getZahlart()) {
                case 2:
                    $invoice['payment'] = 27;
                    break;
                case 3:
                    $invoice['payment'] = 34;
                    break;
                case 4:
                    $invoice['payment'] = 33;
                    break;
                case 5:
                    $invoice['payment'] = 69;
                    break;
                case 6:
                    $invoice['payment'] = 27;
                    $invoice['due_date'] = $kursanmeldung->getZahltbis()->format('Y-m-d');
                    break;
            }

            $invoice['payment_ref'] = '';                            // Nein	Zahlungsreferenz für die Rechnung
            $invoice['payment_ref_notice'] = '';                    // Nein	Anzeige zur Zahlungsreferenz
            $invoice['paid_on'] = '';                                // Nein	Zahlungsdatum der Transaktion im Format “YYYY-MM-DD”
            $invoice['accounting_no'] = '';                            // Nein	Nummer des Buchhaltungskontos (für die Buchhaltungs-Abteilung)
            $invoice['show_py_details'] = 1;                        // Nein	1 = Zahlungsdetails (Kreditkarte/Bankkonto) in der PDF-Datei anzeigen 0 = Zahlungsdetails (Kreditkarte/Bankkonto) in der PDF-Datei verbergen
            $invoice['status'] = 'OPEN';                            // Nein	Status der Rechnung. Wird kein Wert für den Status übergeben, wird der Default-Status ‘OPEN’ verwendet. OPEN, DUE, PAID, CANCELLED, DEBT-COLLECTION, LOSS
            $invoice['sub'] = 0;                                    // Nein	Abrechnung mit oder ohne Abonnementsdetails
            $invoice['accounting_start_date'] = '';                    // Nein	Anfangsdatum für die Buchhaltung
            $invoice['accounting_stop_date'] = '';                    // Nein	Enddatum für die Buchhaltung
            $invoice_details['total_entries'] = 1;                    // Ja	Gesamtanzahl der Rechnungsposten
            $invoice_detail['product_code'] = '';                    // Nein	Code für das Produkt
            $invoice_detail['product_group'] = '';                    // Nein	Produktgruppe
            $invoice_detail['product_name'] = '';                    // Ja	Name des Produkts
            $invoice_detail['product_name'] = 'Anmeldegebühr / registration fee';
            $invoice_detail['description'] = '';                    // Nein	Beschreibung jedes Rechnungspostens
            $invoice_detail['unit'] = 'ST';                            // Ja	Mengeneinheit
            $invoice_detail['quantity'] = 1;                        // Ja	Anzahl
            $invoice_detail['price'] = 0;                            // Ja	Preis eines einzelnen Rechnungspostens in der kleinsten Währungseinheit
            $invoice_detail['price'] = $gebuehr;
            $invoice_detail['total_price'] = 0;                        // Ja	Preis insgesamt (price*quantity) in der kleinsten Währungseinheit
            $invoice_detail['price'] = $gebuehr;
            $invoice_detail['tax_amount'] = 0;                        // Ja	Betrag der Mehrwertsteuer in der kleinsten Währungseinheit
            $invoice_detail['tax_percentage'] = 0;                    // Ja	Mehrwertsteuersatz in Prozent
            $invoice_detail['discount'] = 0;                        // Nein	Kennzeichnung von ermäßigten und normalen Rechnungsposten 1 = der angegebene Rechnungsposten wird als ermäßigter Eintrag angezeigt, 0 = der angegebene Rechnungsposten wird
            $invoice_detail['add_note'] = '';                        // Nein	Zusätzliche Anmerkung zu jedem Rechnungsposten

            $uniqid = $invoice['order_uid'];
            $encodedVars = self::encodeParams(
                $header['authCode'],
                $header['ProductID'],
                $header['TarifID'],
                $invoice['gross_sum'],
                $test_mode,
                $uniqid,
                $password
            );

            $formVars = [
                'header' => $header,
                'customer' => $customer,
                'invoice' => $invoice,
                'invoice_details' => $invoice_details,
                'invoice_detail' => $invoice_detail,
                'encodeVars' => $encodedVars
            ];
        }

        return $formVars;
    }

    /**
     * @param $auth_code
     * @param $product_id
     * @param $tariff_id
     * @param $amount
     * @param $test_mode
     * @param $uniqid
     * @param $password
     * @return array
     */
    public function encodeParams($auth_code, $product_id, $tariff_id, $amount, $test_mode, $uniqid, $password): array
    {
        $auth_code = self::encode($auth_code, $password);
        $product_id = self::encode($product_id, $password);
        $tariff_id = self::encode($tariff_id, $password);
        $amount = self::encode($amount, $password);
        $test_mode = self::encode($test_mode, $password);
        $uniqid = self::encode($uniqid, $password);
        $hash = self::hash1(
            array(
                'auth_code' => $auth_code,
                'product_id' => $product_id,
                'tariff' => $tariff_id,
                'amount' => $amount,
                'test_mode' => $test_mode,
                'uniqid' => $uniqid
            ),
            $password
        );

        return array($auth_code, $product_id, $tariff_id, $amount, $test_mode, $uniqid, $hash);
    }

    /**
     * @param $data
     * @param $password
     * @return string
     */
    public function encode($data, $password): string
    {
        $data = trim($data);
        if ($data == '') {
            return 'Error: no data';
        }
        if (!function_exists('base64_encode') or !function_exists('pack') or !function_exists('crc32')) {
            return 'Error: func n/a';
        }
        try {
            $crc = sprintf('%u', crc32($data));# %u is a must for ccrc32 returns a signed value
            $data = $crc . "|" . $data;
            $data = bin2hex($data . $password);
            $data = strrev(base64_encode($data));
        } catch (Exception $e) {
            echo('Error: ' . $e);
        }

        return $data;
    }

    #$h contains encoded data

    /**
     * @param $h
     * @param $key
     * @return string
     */
    function hash1($h, $key): string
    {
        if (!$h) {
            return 'Error: no data';
        }
        if (!function_exists('md5')) {
            return 'Error: func n/a';
        }

        return md5(
            $h['auth_code'] . $h['product_id'] . $h['tariff'] . $h['amount'] . $h['test_mode'] . $h['uniqid'] . strrev(
                $key
            )
        );
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Kursanmeldung $newKursanmeldung
     * @param \Hfm\Kursanmeldung\Domain\Model\Teilnehmer $newTn
     * @param array $banktransfer
     * @return void
     */
    private function sendInvoiceMail(Kursanmeldung $newKursanmeldung, array $banktransfer = []): void
    {
        $assignments = $this->participantUtility->getFluidAssignments($newKursanmeldung);
        $assignments['kurs'] = $this->nameVeranstaltung . '<br />' . $this->participantUtility->getProfInstrument(
                $newKursanmeldung->getKurs()
            );
        $assignments['novalnet'] = $banktransfer;
        $assignments['embedLogo'] = GeneralUtility::getFileAbsFileName(
            'EXT:kursanmeldung/Resources/Public/Images/logo_wba_112x25px.png'
        );

        $newTn = $newKursanmeldung->getTn()->current();

        // TeilnehmerEmail
        $mailDto = new MailDto();
        $mailDto->setSendTo($newTn->getEmail());
        $mailDto->setSendFrom(new Address($this->emailHostAddress, $this->emailHostName));
        $mailDto->setSubject($this->emailSubjectInvoice);
        $mailDto->setRequest($this->request);
        $mailDto->setTemplate('InvoiceHtmlBe');
        $mailDto->setFormat(FluidEmail::FORMAT_HTML);
        $mailDto->setKursanmeldung($newKursanmeldung);
        $mailDto->setAssignments($assignments);
        $this->mailFacade->sendFluidEmail($mailDto);

        //AdminEmail
        $mailDto = new MailDto();
        $mailDto->setSendTo($this->emailHostAddress);
        $mailDto->setSendFrom(
            new Address($newTn->getEmail(), ucfirst($newTn->getVorname()) . ' ' . ucfirst($newTn->getNachname()))
        );
        $mailDto->setSubject('Kursanmeldung Administrator Rechnung');
        $mailDto->setRequest($this->request);
        $mailDto->setTemplate('InvoiceHtmlBe');
        $mailDto->setFormat(FluidEmail::FORMAT_HTML);
        $mailDto->setKursanmeldung($newKursanmeldung);
        $mailDto->setAssignments($assignments);
        $this->mailFacade->sendFluidEmail($mailDto);
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Kursanmeldung $competition
     * @param int $type
     * @return string
     */
    protected function paymentReason(Kursanmeldung $competition, int $type = 0): string
    {
        $uid = $competition->getUid();
        switch ($type) {
            case 1:
                $ensName = '';
                $compEnsem = $competition->getEnsemble();
                if ($compEnsem->count() > 0) {
                    $compEnsem->rewind();
                    $ensName = $compEnsem->current()->getEnname();
                }
                $paymentReason = trim($uid . ' ' . $ensName);
                break;
            default:
                $tnName = '';
                $compTn = $competition->getTn();
                if ($compTn->count() > 0) {
                    $compTn->rewind();
                    $tnName = $compTn->current()->getNachname();
                }
                $paymentReason = trim($uid . ' ' . $tnName);
        }

        return $paymentReason;
    }
}

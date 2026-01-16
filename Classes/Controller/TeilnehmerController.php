<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Controller;

use Hfm\Kursanmeldung\Domain\Model\Kursanmeldung;
use Hfm\Kursanmeldung\Domain\Repository\AnmeldestatusRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Hfm\Kursanmeldung\Domain\Repository\KursRepository;
use Hfm\Kursanmeldung\Domain\Repository\KursanmeldungRepository;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

class TeilnehmerController extends ActionController
{
    public function __construct(
        private readonly AnmeldestatusRepository $anmeldestatusRepository,
        private readonly KursRepository $kursRepository,
        private readonly KursanmeldungRepository $kursanmeldungRepository,
        private readonly PersistenceManagerInterface $persistenceManager,
    ) {
    }

    public function initializeAction(): void
    {
        // if dbdata distributed over more pages
        if(isset($this->settings['dataPages'])){
            if(isset($this->kursRepository))$this->kursRepository->setStoragePageIds($this->settings['dataPages']);
            if(isset($this->anmeldestatusRepository))$this->anmeldestatusRepository->setStoragePageIds($this->settings['dataPages']);
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
                $registrations = $this->kursanmeldungRepository->getParticipantsByKursFiltered($kUid, (string)$kSearch, $kFields);
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
        // Simple edit view (template handles rendering); saving is not part of this task
        $this->view->assign('kursanmeldung', $kursanmeldung);

        return $this->htmlResponse();
    }

    public function updateAnmeldestatusAction(): ResponseInterface
    {
        try {
            $kaUid = (int)($this->request->hasArgument('kursanmeldung') ? $this->request->getArgument('kursanmeldung') : 0);
            $astUid = (int)($this->request->hasArgument('anmeldestatus') ? $this->request->getArgument('anmeldestatus') : 0);

            if ($kaUid <= 0 || $astUid <= 0) {
                $response = $this->htmlResponse(json_encode(['success' => false, 'error' => 'invalid_arguments']));
                return $response->withHeader('Content-Type', 'application/json');
            }

            /** @var \Hfm\Kursanmeldung\Domain\Model\Kursanmeldung|null $ka */
            $ka = $this->kursanmeldungRepository->findByIdentifier($kaUid);
            if ($ka === null) {
                $response = $this->htmlResponse(json_encode(['success' => false, 'error' => 'not_found']));
                return $response->withHeader('Content-Type', 'application/json');
            }

            /** @var \Hfm\Kursanmeldung\Domain\Model\Anmeldestatus|null $status */
            $status = $this->anmeldestatusRepository->findByIdentifier($astUid);
            if ($status === null) {
                $response = $this->htmlResponse(json_encode(['success' => false, 'error' => 'status_not_found']));
                return $response->withHeader('Content-Type', 'application/json');
            }

            $storage = new ObjectStorage();
            $storage->attach($status);
            $ka->setAnmeldestatus($storage);
            $this->kursanmeldungRepository->update($ka);
            $this->persistenceManager->persistAll();

            $response = $this->htmlResponse(json_encode(['success' => true]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Throwable $e) {
            $response = $this->htmlResponse(json_encode(['success' => false, 'error' => 'exception', 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json');
        }
    }
}

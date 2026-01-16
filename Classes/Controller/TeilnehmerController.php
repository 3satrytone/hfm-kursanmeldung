<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Controller;

use Hfm\Kursanmeldung\Domain\Model\Kursanmeldung;
use Hfm\Kursanmeldung\Domain\Repository\AnmeldestatusRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Hfm\Kursanmeldung\Domain\Repository\TeilnehmerRepository;
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

        // Pagination parameters
        $currentPage = 1;
        $itemsPerPage = 25;
        if ($this->request->hasArgument('page')) {
            $currentPage = max(1, (int)$this->request->getArgument('page'));
        }
        if (isset($this->settings['itemsPerPage'])) {
            $itemsPerPage = max(1, (int)$this->settings['itemsPerPage']);
        }

        // All participants with pagination
        $allParticipants = $this->kursanmeldungRepository->findAllSortedByUid();
        $paginator = new QueryResultPaginator($allParticipants, $currentPage, $itemsPerPage);
        $pagination = new SimplePagination($paginator);

        // Participants grouped by course
        $participantsByCourse = [];
        $courses = $this->kursRepository->findAll();
        foreach ($courses as $kurs) {
            $registrations = $this->kursanmeldungRepository->getParticipantsByKurs($kurs->getUid());
            $participantsByCourse[] = [
                'kurs' => $kurs,
                'registrations' => $registrations,
            ];
        }

        $this->view->assignMultiple([
            'paginator' => $paginator,
            'pagination' => $pagination,
            'participantsByCourse' => $participantsByCourse,
            'anmeldestatusList' => $this->anmeldestatusRepository->findAll(),
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

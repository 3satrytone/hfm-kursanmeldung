<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Hfm\Kursanmeldung\Domain\Model\Teilnehmer;
use Hfm\Kursanmeldung\Domain\Repository\TeilnehmerRepository;
use Hfm\Kursanmeldung\Domain\Repository\KursRepository;
use Hfm\Kursanmeldung\Domain\Repository\KursanmeldungRepository;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Core\Pagination\SimplePagination;

class TeilnehmerController extends ActionController
{
    public function __construct(
        private readonly TeilnehmerRepository $teilnehmerRepository,
        private readonly KursRepository $kursRepository,
        private readonly KursanmeldungRepository $kursanmeldungRepository
    ) {
    }

    protected function addBackendAssets(): void
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->addJsFile('EXT:kursanmeldung/Resources/Public/JavaScript/Teilnehmer.js');
    }

    public function listAction(): ResponseInterface
    {
        $this->addBackendAssets();

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
        $allParticipants = $this->kursanmeldungRepository->findAll();
        $paginator = new QueryResultPaginator($allParticipants, $currentPage, $itemsPerPage);
        $pagination = new SimplePagination($paginator);

        // Participants grouped by course
        $participantsByCourse = [];
        $courses = $this->kursRepository->findAll();
        foreach ($courses as $kurs) {
            $registrations = $this->kursanmeldungRepository->getParticipantsByKurs($kurs->getUid());
            $participants = [];
            foreach ($registrations as $registration) {
                foreach ($registration->getTn() as $tn) {
                    $participants[] = $tn;
                }
            }
            $participantsByCourse[] = [
                'kurs' => $kurs,
                'teilnehmer' => $participants,
            ];
        }

        $this->view->assignMultiple([
            'paginator' => $paginator,
            'pagination' => $pagination,
            'participantsByCourse' => $participantsByCourse,
        ]);

        return $this->htmlResponse();
    }

    public function showAction(Teilnehmer $teilnehmer): void
    {
        // Implement showing a single Teilnehmer record when templates are available.
        $this->view->assign('teilnehmer', $teilnehmer);
    }

    public function editAction(Teilnehmer $teilnehmer): ResponseInterface
    {
        // Simple edit view (template handles rendering); saving is not part of this task
        $this->view->assign('teilnehmer', $teilnehmer);

        return $this->htmlResponse();
    }
}

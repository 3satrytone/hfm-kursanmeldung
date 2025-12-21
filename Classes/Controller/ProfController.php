<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\Controller;

use Hfm\Kursanmeldung\Domain\Model\Prof;
use Hfm\Kursanmeldung\Domain\Repository\ProfRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\Resource\Enum\DuplicationBehavior;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Annotation\IgnoreValidation;

class ProfController extends ActionController
{
    /**
     * @param \TYPO3\CMS\Core\Resource\ResourceFactory $resourceFactory
     * @param \Hfm\Kursanmeldung\Domain\Repository\ProfRepository $profRepository
     */
    public function __construct(
        protected ResourceFactory $resourceFactory,
        private ProfRepository $profRepository
    ) {
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAction(): ResponseInterface
    {
        $this->view->assign('profs', $this->profRepository->findAll());

        return $this->htmlResponse();
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Prof $prof
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function showAction(Prof $prof): ResponseInterface
    {
        $this->view->assign('prof', $prof);

        return $this->htmlResponse();
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    #[IgnoreValidation(['argumentName' => 'prof'])]
    public function newAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }

    /**
     * @param Prof $prof
     * @return ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function createAction(Prof $prof): ResponseInterface
    {
        $this->addFlashMessage('Datensatz wurde erstellt.', 'Created', ContextualFeedbackSeverity::OK);
        $this->profRepository->add($prof);

        return $this->redirect('list');
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Prof $prof
     * @IgnoreValidation $prof
     * @return \Psr\Http\Message\ResponseInterface
     */
    #[IgnoreValidation(['argumentName' => 'prof'])]
    public function editAction(Prof $prof): ResponseInterface
    {
        $this->view->assign('prof', $prof);

        return $this->htmlResponse();
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Prof $prof
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    public function updateAction(Prof $prof): ResponseInterface
    {
        $this->addFlashMessage('Datensatz wurde gespeichert.', 'Saved', ContextualFeedbackSeverity::OK);
        $this->profRepository->update($prof);

        return $this->redirect('list');
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Prof $prof
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function deleteAction(Prof $prof): ResponseInterface
    {
        $this->addFlashMessage('Datensatz wurde gelöscht.', 'Deleted', ContextualFeedbackSeverity::OK);
        $this->profRepository->remove($prof);

        return $this->redirect('list');
    }
}

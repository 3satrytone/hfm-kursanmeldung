<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Controller;

use Hfm\Kursanmeldung\Constants\Constants;
use Hfm\Kursanmeldung\Domain\Repository\OrteRepository;
use Hfm\Kursanmeldung\Utility\PropertyConverterUtility;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Hfm\Kursanmeldung\Domain\Model\Orte;

class OrteController extends ActionController
{
    /**
     * @param \Hfm\Kursanmeldung\Domain\Repository\OrteRepository $orteRepository
     */
    public function __construct(
        private OrteRepository $orteRepository,
        private readonly PropertyConverterUtility $propertyConverterUtility
    ) {
    }

    /**
     * @return void
     */
    public function initializeCreateAction()
    {
        if ($this->request->hasArgument(Constants::ORTE)) {
            $this->propertyConverterUtility->convertArgumentsOrte($this->arguments);
        }
    }

    /**
     * @return void
     */
    public function initializeUpdateAction()
    {
        if ($this->request->hasArgument(Constants::ORTE)) {
            $this->propertyConverterUtility->convertArgumentsOrte($this->arguments);
        }
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAction(): ResponseInterface
    {
        $this->view->assign(Constants::ORTE, $this->orteRepository->findAll());

        return $this->htmlResponse();
    }

    /**
     * @param Orte $orte
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function showAction(Orte $orte): ResponseInterface
    {
        $this->view->assign(Constants::ORTE, $orte);

        return $this->htmlResponse();
    }

    /**
     * @return ResponseInterface
     */
    public function newAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }

    /**
     * @param Orte $orte
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function createAction(Orte $orte): ResponseInterface
    {
        $this->addFlashMessage('Datensatz wurde erstellt.', 'Created', ContextualFeedbackSeverity::OK);
        $this->orteRepository->add($orte);

        return $this->redirect('list');
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Orte $orte
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function editAction(Orte $orte): ResponseInterface
    {
        $this->view->assign(Constants::ORTE, $orte);

        return $this->htmlResponse();
    }

    /**
     * @param Orte $orte
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function updateAction(Orte $orte): ResponseInterface
    {
        $this->addFlashMessage(
            'Datensatz wurde gespeichert.',
            'Saved',
            ContextualFeedbackSeverity::OK
        );
        $this->orteRepository->add($orte);

        return $this->redirect('list');
    }

    /**
     * @param Orte $orte
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function deleteAction(Orte $orte): ResponseInterface
    {
        $this->addFlashMessage(
            'Datensatz wurde gelöscht.',
            'Deleted',
            ContextualFeedbackSeverity::OK
        );
        $this->orteRepository->remove($orte);

        return $this->redirect('list');
    }
}

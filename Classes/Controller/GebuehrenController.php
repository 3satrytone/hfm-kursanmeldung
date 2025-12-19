<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\Controller;

use Hfm\Kursanmeldung\Constants\Constants;
use Hfm\Kursanmeldung\Domain\Repository\GebuehrenRepository;
use Hfm\Kursanmeldung\Utility\PropertyConverterUtility;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Hfm\Kursanmeldung\Domain\Model\Gebuehren;

class GebuehrenController extends ActionController
{

    /**
     * @param \Hfm\Kursanmeldung\Domain\Repository\GebuehrenRepository $gebuehrenRepository
     * @param \Hfm\Kursanmeldung\Utility\PropertyConverterUtility $propertyConverterUtility
     */
    public function __construct(
        private GebuehrenRepository $gebuehrenRepository,
        private readonly PropertyConverterUtility $propertyConverterUtility
    ) {
    }

    /**
     * @return void
     */
    public function initializeCreateAction()
    {
        if ($this->request->hasArgument(Constants::GEBUEHREN)) {
            $this->propertyConverterUtility->convertArgumentsGebuehren($this->arguments);
        }
    }

    /**
     * @return void
     */
    public function initializeUpdateAction()
    {
        if ($this->request->hasArgument(Constants::GEBUEHREN)) {
            $this->propertyConverterUtility->convertArgumentsGebuehren($this->arguments);
        }
    }

    /**
     * @return ResponseInterface
     */
    public function listAction(): ResponseInterface
    {
        $this->view->assign(Constants::GEBUEHREN, $this->gebuehrenRepository->findAll());

        return $this->htmlResponse();
    }

    /**
     * @param Gebuehren $gebuehren
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function showAction(Gebuehren $gebuehren): ResponseInterface
    {
        // Implement showing a single Gebuehren record when templates are available.
        $this->view->assign(Constants::GEBUEHREN, $gebuehren);

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
     * @param Gebuehren $gebuehren
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function createAction(Gebuehren $gebuehren): ResponseInterface
    {
        $this->addFlashMessage('Datensatz wurde erstellt.', 'Created', ContextualFeedbackSeverity::OK);
        $this->gebuehrenRepository->add($gebuehren);

        return $this->redirect('list');
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Gebuehren $gebuehren
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function editAction(Gebuehren $gebuehren): ResponseInterface
    {
        $this->view->assign(Constants::GEBUEHREN, $gebuehren);

        return $this->htmlResponse();
    }

    /**
     * @param Gebuehren $gebuehren
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function updateAction(Gebuehren $gebuehren): ResponseInterface
    {
        $this->addFlashMessage(
            'Datensatz wurde gespeichert.',
            'Saved',
            ContextualFeedbackSeverity::OK
        );
        $this->gebuehrenRepository->add($gebuehren);

        return $this->redirect('list');
    }

    /**
     * @param Gebuehren $gebuehren
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function deleteAction(Gebuehren $gebuehren): ResponseInterface
    {
        $this->addFlashMessage(
            'Datensatz wurde gelöscht.',
            'Deleted',
            ContextualFeedbackSeverity::OK
        );
        $this->gebuehrenRepository->remove($gebuehren);

        return $this->redirect('list');
    }
}

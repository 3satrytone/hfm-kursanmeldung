<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Controller;

use Hfm\Kursanmeldung\Constants\Constants;
use Hfm\Kursanmeldung\Domain\Repository\HotelRepository;
use Hfm\Kursanmeldung\Utility\PropertyConverterUtility;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Hfm\Kursanmeldung\Domain\Model\Hotel;

class HotelController extends ActionController
{

    /**
     * @param \Hfm\Kursanmeldung\Domain\Repository\HotelRepository $hotelRepository
     * @param \Hfm\Kursanmeldung\Utility\PropertyConverterUtility $propertyConverterUtility
     */
    public function __construct(
        private HotelRepository $hotelRepository,
        private readonly PropertyConverterUtility $propertyConverterUtility
    ) {
    }

    /**
     * @return void
     */
    public function initializeCreateAction(): void
    {
        if ($this->request->hasArgument(Constants::HOTEL)) {
            $this->propertyConverterUtility->convertArgumentsHotel($this->arguments);
        }
    }

    /**
     * @return void
     */
    public function initializeUpdateAction(): void
    {
        if ($this->request->hasArgument(Constants::HOTEL)) {
            $this->propertyConverterUtility->convertArgumentsHotel($this->arguments);
        }
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAction(): ResponseInterface
    {
        $this->view->assign(Constants::HOTELS, $this->hotelRepository->findAll());

        return $this->htmlResponse();
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Hotel $hotel
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function showAction(Hotel $hotel): ResponseInterface
    {
        $this->view->assign(Constants::HOTEL, $hotel);

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
     * @param Hotel $hotel
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function createAction(Hotel $hotel): ResponseInterface
    {
        $this->addFlashMessage('Datensatz wurde erstellt.', 'Created', ContextualFeedbackSeverity::OK);
        $this->hotelRepository->add($hotel);

        return $this->redirect('list');
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Hotel $hotel
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function editAction(Hotel $hotel): ResponseInterface
    {
        $this->view->assign(Constants::HOTEL, $hotel);

        return $this->htmlResponse();
    }

    /**
     * @param Hotel $hotel
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function updateAction(Hotel $hotel): ResponseInterface
    {
        $this->addFlashMessage(
            'Datensatz wurde gespeichert.',
            'Saved',
            ContextualFeedbackSeverity::OK
        );
        $this->hotelRepository->add($hotel);

        return $this->redirect('list');
    }

    /**
     * @param Hotel $hotel
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function deleteAction(Hotel $hotel): ResponseInterface
    {
        $this->addFlashMessage(
            'Datensatz wurde gelöscht.',
            'Deleted',
            ContextualFeedbackSeverity::OK
        );
        $this->hotelRepository->remove($hotel);

        return $this->redirect('list');
    }
}

<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\Controller;

use Hfm\Kursanmeldung\Constants\Constants;
use Hfm\Kursanmeldung\Domain\Repository\GebuehrenRepository;
use Hfm\Kursanmeldung\Domain\Repository\HotelRepository;
use Hfm\Kursanmeldung\Domain\Repository\KursRepository;
use Hfm\Kursanmeldung\Domain\Repository\OrteRepository;
use Hfm\Kursanmeldung\Domain\Repository\ProfRepository;
use Hfm\Kursanmeldung\Utility\FormatUtility;
use Hfm\Kursanmeldung\Utility\PropertyConverterUtility;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Hfm\Kursanmeldung\Domain\Model\Kurs;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class KursController extends ActionController
{
    /**
     * @param \Hfm\Kursanmeldung\Domain\Repository\KursRepository $kursRepository
     * @param \Hfm\Kursanmeldung\Utility\PropertyConverterUtility $propertyConverterUtility
     */
    public function __construct(
        private KursRepository $kursRepository,
        private readonly PropertyConverterUtility $propertyConverterUtility,
        private readonly FormatUtility $formatUtility,
        private readonly OrteRepository $orteRepository,
        private readonly GebuehrenRepository $gebuehrenRepository,
        private readonly ProfRepository $profRepository,
        private readonly HotelRepository $hotelRepository
    ) {
    }

    public function initializeCreateAction(): void
    {
        if (isset($this->arguments[Constants::KURS])) {
            $requestArguments = $this->request->getArguments();
            $propertyMappingConfiguration = $this->arguments[Constants::KURS]->getPropertyMappingConfiguration();

            if (!strtotime($requestArguments[Constants::KURS]['kurszeitstart'])
                || !strtotime($requestArguments[Constants::KURS]['kurszeitend'])
                || !strtotime($requestArguments[Constants::KURS]['anreisedate'])) {
                $message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                    'TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
                    'Bitte Datum im Format "d.m.Y H:i" angeben.',
                    'Abweichendes Datumsformat!',
                    ContextualFeedbackSeverity::WARNING,
                    false
                );
                $this->addFlashMessage($message);

                $this->redirect('new', null, null, $requestArguments);
            }
            $propertyMappingConfiguration->forProperty(
                'kurszeitstart'
            ) // this line can be skipped in order to specify the format for all properties
            ->setTypeConverterOption(
                \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::class,
                \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT,
                'd.m.Y H:i'
            );
            $propertyMappingConfiguration->forProperty(
                'kurszeitend'
            ) // this line can be skipped in order to specify the format for all properties
            ->setTypeConverterOption(
                \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::class,
                \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT,
                'd.m.Y H:i'
            );
            $propertyMappingConfiguration->forProperty(
                'anreisedate'
            ) // this line can be skipped in order to specify the format for all properties
            ->setTypeConverterOption(
                \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::class,
                \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT,
                'd.m.Y H:i'
            );
        }
    }


    public function initializeUpdateAction(): void
    {
        if (isset($this->arguments[Constants::KURS])) {
            $requestArguments = $this->request->getArguments();
            $propertyMappingConfiguration = $this->arguments[Constants::KURS]->getPropertyMappingConfiguration();

            if (!strtotime($requestArguments[Constants::KURS]['kurszeitstart'])
                || !strtotime($requestArguments[Constants::KURS]['kurszeitend'])
                || !strtotime($requestArguments[Constants::KURS]['anreisedate'])) {
                $message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                    'TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
                    'Bitte Datum im Format "d.m.Y H:i" angeben.',
                    'Abweichendes Datumsformat!',
                    ContextualFeedbackSeverity::WARNING,
                    false
                );
                $this->addFlashMessage($message);

                $this->redirect('edit', null, null, $requestArguments);
            }
            $propertyMappingConfiguration->forProperty(
                'kurszeitstart'
            ) // this line can be skipped in order to specify the format for all properties
            ->setTypeConverterOption(
                \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::class,
                \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT,
                'd.m.Y H:i'
            );
            $propertyMappingConfiguration->forProperty(
                'kurszeitend'
            ) // this line can be skipped in order to specify the format for all properties
            ->setTypeConverterOption(
                \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::class,
                \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT,
                'd.m.Y H:i'
            );
            $propertyMappingConfiguration->forProperty(
                'anreisedate'
            ) // this line can be skipped in order to specify the format for all properties
            ->setTypeConverterOption(
                \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::class,
                \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT,
                'd.m.Y H:i'
            );
        }
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAction(): ResponseInterface
    {
        $this->view->assign(Constants::KURSE, $this->kursRepository->findAll());

        return $this->htmlResponse();
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Kurs $kurs
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function showAction(Kurs $kurs): ResponseInterface
    {
        $this->view->assign(Constants::KURS, $kurs);

        return $this->htmlResponse();
    }

    /**
     * @return ResponseInterface
     */
    public function newAction(): ResponseInterface
    {
        $gebuehren = $this->gebuehrenRepository->findAll()->toArray();

        $gebOpt = array();
        if (!empty($gebuehren)) {
            foreach ($gebuehren as $key => $value) {
                $gebOpt[$key]['anmeldung'] = $value->getAnmeldung() . ',' . $value->getAnmeldungerm(
                    ) . ',' . $value->getAktivengeb() . ',' . $value->getAktivengeberm() . ',' . $value->getPassivgeb(
                    ) . ',' . $value->getPassivgeberm();
                $gebOpt[$key]['uid'] = $value->getUid();
            }
        }

        $this->view->assign('ensembleCheckbox', $this->formatUtility->buildCBFromTCA());
        $this->view->assign('gebuehrenOptions', $gebOpt);
        $this->view->assign('orte', $this->orteRepository->findAll()->toArray());
        $this->view->assign('gebuehren', $gebuehren);
        $this->view->assign('profs', $this->profRepository->findAll()->toArray());
        $this->view->assign('hotels', $this->hotelRepository->findAll()->toArray());

        return $this->htmlResponse();
    }

    /**
     * @param Kurs $kurs
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function createAction(Kurs $kurs): ResponseInterface
    {
        $this->addFlashMessage('Datensatz wurde erstellt.', 'Created', ContextualFeedbackSeverity::OK);

        $kurs->setEnsemble((string)$this->formatUtility->getBitmaskFromRequest($this->request));
        $this->kursRepository->add($kurs);

        return $this->redirect('list');
    }

    /**
     * @param Kurs $kurs
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function editAction(Kurs $kurs): ResponseInterface
    {
        $gebuehren = $this->gebuehrenRepository->findAll()->toArray();

        $gebOpt = array();
        if (!empty($gebuehren)) {
            foreach ($gebuehren as $key => $value) {
                $gebOpt[$key]['anmeldung'] = $value->getAnmeldung() . ',' . $value->getAnmeldungerm(
                    ) . ',' . $value->getAktivengeb() . ',' . $value->getAktivengeberm() . ',' . $value->getPassivgeb(
                    ) . ',' . $value->getPassivgeberm();
                $gebOpt[$key]['uid'] = $value->getUid();
            }
        }

        $this->view->assign('ensembleCheckbox', $this->formatUtility->buildCBFromTCA($kurs));
        $this->view->assign('gebuehrenOptions', $gebOpt);
        $this->view->assign('orte', $this->orteRepository->findAll()->toArray());
        $this->view->assign('gebuehren', $gebuehren);
        $this->view->assign('profs', $this->profRepository->findAll()->toArray());
        $this->view->assign('hotels', $this->hotelRepository->findAll()->toArray());
        $this->view->assign(Constants::KURS, $kurs);

        return $this->htmlResponse();
    }

    /**
     * @param Kurs $kurs
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function updateAction(Kurs $kurs): ResponseInterface
    {
        $kurs->setEnsemble((string)$this->formatUtility->getBitmaskFromRequest($this->request));

        $this->addFlashMessage(
            'Datensatz wurde gespeichert.',
            'Saved',
            ContextualFeedbackSeverity::OK
        );

        $this->kursRepository->add($kurs);

        return $this->redirect('list');
    }

    /**
     * @param Kurs $kurs
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function deleteAction(Kurs $kurs): ResponseInterface
    {
        $this->addFlashMessage(
            'Datensatz wurde gelöscht.',
            'Deleted',
            ContextualFeedbackSeverity::OK
        );
        $this->kursRepository->remove($kurs);

        return $this->redirect('list');
    }
}

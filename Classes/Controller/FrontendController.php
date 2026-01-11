<?php

namespace Hfm\Kursanmeldung\Controller;

use Exception;
use Hfm\Kursanmeldung\App\Dto\MailDto;
use Hfm\Kursanmeldung\App\Dto\NovalnetResponseDto;
use Hfm\Kursanmeldung\App\Dto\StepDataParticipantDto;
use Hfm\Kursanmeldung\App\Mail\Business\MailFacade;
use Hfm\Kursanmeldung\App\Novalnet\Business\NovalnetFacade;
use Hfm\Kursanmeldung\App\Participant\Business\ParticipantFacade;
use Hfm\Kursanmeldung\Constants\Constants;
use Hfm\Kursanmeldung\Domain\Model\Ensemble;
use Hfm\Kursanmeldung\Domain\Model\Kursanmeldung;
use Hfm\Kursanmeldung\Domain\Model\Step1Data;
use Hfm\Kursanmeldung\Domain\Model\Step2Data;
use Hfm\Kursanmeldung\Domain\Model\Step3Data;
use Hfm\Kursanmeldung\Domain\Model\Step4Data;
use Hfm\Kursanmeldung\Domain\Model\Teilnehmer;
use Hfm\Kursanmeldung\Domain\Model\Uploads;
use Hfm\Kursanmeldung\Domain\Repository\GebuehrenRepository;
use Hfm\Kursanmeldung\Domain\Repository\HotelRepository;
use Hfm\Kursanmeldung\Domain\Repository\KursanmeldungRepository;
use Hfm\Kursanmeldung\Domain\Repository\KursRepository;
use Hfm\Kursanmeldung\Domain\Repository\ProfRepository;
use Hfm\Kursanmeldung\Domain\Validator\Step1DataValidator;
use Hfm\Kursanmeldung\Domain\Validator\Step2DataValidator;
use Hfm\Kursanmeldung\Utility\FormatUtility;
use Hfm\Kursanmeldung\Utility\PropertyConverterUtility;
use Hfm\Kursanmeldung\Utility\ParticipantUtility;
use Hfm\Kursanmeldung\Utility\SessionUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Country\CountryProvider;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation\IgnoreValidation;
use TYPO3\CMS\Extbase\Annotation\Validate;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

class FrontendController extends ActionController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected $zahlungsartArr = [
        9 => 'standard',
        1 => 'banktransfer',
        2 => 'prepayment',
        3 => 'paypal',
        4 => 'onlinetransfer',
        5 => 'giropay',
        6 => 'invoice',
        7 => 'nopayment'
    ];
    protected $zahlungsartNovalnetArr = [
        3 => 'paypal',
        4 => 'onlinetransfer',
        5 => 'giropay',
        6 => 'invoice'
    ];
    protected $novalnetSecret = '81c2f886f91e18fe16d6f4e865877cb6';
    protected $emailHostAddress = 'wiebke.eckardt@hfm-weimar.de';
    protected $emailHostAddressAdmin = 'wiebke.eckardt@hfm-weimar.de';
    protected $emailHostAddressCc = 'info@schneider-software-service.de';
    protected $emailHostName = '';
    protected $emailSubject = 'Ihre Kursanmeldung bei der Hochschule für Musik, bitte bestätigen';
    protected $emailSubjectAdmin = 'Admin: Kursanmeldung bei der Hochschule für Musik';
    protected $emailSubjectInfo = 'Ihre Kursanmeldung bei der Hochschule für Musik';
    protected $emailSubjectInvoice = 'Ihre Kursanmeldung bei der Hochschule für Musik, bitte Rechnung begleichen';
    protected $setup = array();
    protected $userMailId = 28;
    protected $infoMailId = 128;
    protected $adminMailId = 29;
    protected $nameVeranstaltung = 'Weimarer Meisterkurse';
    protected $testmode = 1;

    public function __construct(
        protected readonly KursRepository $kursRepository,
        protected readonly ProfRepository $profRepository,
        protected readonly KursanmeldungRepository $kursanmeldungRepository,
        protected readonly GebuehrenRepository $gebuehrenRepository,
        protected readonly HotelRepository $hotelRepository,
        protected readonly SessionUtility $sessionUtility,
        protected readonly ParticipantUtility $participantUtility,
        protected readonly FormatUtility $formatUtility,
        protected readonly PropertyConverterUtility $propertyConverterUtility,
        protected readonly ParticipantFacade $participantFacade,
        protected readonly NovalnetFacade $novalnetFacade,
        protected readonly MailFacade $mailFacade,
        private readonly PersistenceManager $persistenceManager,
        private readonly CountryProvider $countryProvider,
        protected UriBuilder $uriBuilder
    ) {
    }

    public function initializeAction(): void
    {
        $this->zahlungsartArr = [9 => 'standard'];
        if (isset($this->settings)) {
            if (isset($this->settings['payment'])) {
                if (isset($this->settings['payment']['banktransfer']) && $this->settings['payment']['banktransfer'] == 1) {
                    $this->zahlungsartArr[1] = 'banktransfer';
                }
                if (isset($this->settings['payment']['prepayment']) && $this->settings['payment']['prepayment'] == 1) {
                    $this->zahlungsartArr[2] = 'prepayment';
                }
                if (isset($this->settings['payment']['paypal']) && $this->settings['payment']['paypal'] == 1) {
                    $this->zahlungsartArr[3] = 'paypal';
                }
                if (isset($this->settings['payment']['onlinetransfer']) && $this->settings['payment']['onlinetransfer'] == 1) {
                    $this->zahlungsartArr[4] = 'onlinetransfer';
                }
                if (isset($this->settings['payment']['giropay']) && $this->settings['payment']['giropay'] == 1) {
                    $this->zahlungsartArr[5] = 'giropay';
                }
                if (isset($this->settings['payment']['invoice']) && $this->settings['payment']['invoice'] == 1) {
                    $this->zahlungsartArr[6] = 'invoice';
                }
                if (isset($this->settings['payment']['nopayment']) && $this->settings['payment']['nopayment'] == 1) {
                    $this->zahlungsartArr[7] = 'nopayment';
                }
            }
            if (isset($this->settings['invoicedata'])) {
                if (isset($this->settings['invoicedata']['accountname'])) {
                    $this->setup['invoicedata_accountname'] = $this->settings['invoicedata']['accountname'];
                }
                if (isset($this->settings['invoicedata']['bankcode'])) {
                    $this->setup['invoice_bankcode'] = $this->settings['invoicedata']['bankcode'];
                }
                if (isset($this->settings['invoicedata']['iban'])) {
                    $this->setup['invoicedata_iban'] = $this->settings['invoicedata']['iban'];
                }
                if (isset($this->settings['invoicedata']['bic'])) {
                    $this->setup['invoicedata_bic'] = $this->settings['invoicedata']['bic'];
                }
                if (isset($this->settings['invoicedata']['bankplace'])) {
                    $this->setup['invoicedata_bankplace'] = $this->settings['invoicedata']['bankplace'];
                }
                if (isset($this->settings['invoicedata']['event'])) {
                    $this->setup['invoicedata_event'] = $this->settings['invoicedata']['event'];
                }
                if (isset($this->settings['invoicedata']['date'])) {
                    $this->setup['invoicedata_date'] = $this->settings['invoicedata']['date'];
                }
                if (isset($this->settings['invoicedata']['text1'])) {
                    $this->setup['invoicedata_text1'] = $this->settings['invoicedata']['text1'];
                }
                if (isset($this->settings['invoicedata']['text2'])) {
                    $this->setup['invoicedata_text2'] = $this->settings['invoicedata']['text2'];
                }
                if (isset($this->settings['invoicedata']['subjectadmin'])) {
                    $this->setup['invoicedata_subjectadmin'] = $this->settings['invoicedata']['subjectadmin'];
                }
                if (isset($this->settings['invoicedata']['subjectadmin_en'])) {
                    $this->setup['invoicedata_subjectadmin_en'] = $this->settings['invoicedata']['subjectadmin_en'];
                }
                if (isset($this->settings['invoicedata']['subjectuser'])) {
                    $this->setup['invoicedata_subjectuser'] = $this->settings['invoicedata']['subjectuser'];
                }
                if (isset($this->settings['invoicedata']['subjectuser_en'])) {
                    $this->setup['invoicedata_subjectuser_en'] = $this->settings['invoicedata']['subjectuser_en'];
                }
            }
            if (isset($this->settings['email'])) {
                $this->emailHostAddress = $this->settings['email'];
            }
            if (isset($this->settings['emailtoadmin'])) {
                $this->emailHostAddressAdmin = $this->settings['emailtoadmin'];
            }
            if (isset($this->settings['emailccuser'])) {
                $this->emailHostAddressCc = $this->settings['emailccuser'];
            }
            if (isset($this->settings['emailhost'])) {
                $this->emailHostName = $this->settings['emailhost'];
            }
            if (isset($this->settings['emailsubject'])) {
                $this->emailSubject = $this->settings['emailsubject'];
            }
            if (isset($this->settings['emailsubjectinfo'])) {
                $this->emailSubjectInfo = $this->settings['emailsubjectinfo'];
            }
            if (isset($this->settings['emailsubjectadmin'])) {
                $this->emailSubjectAdmin = $this->settings['emailsubjectadmin'];
            }
            if (isset($this->settings['emailadminpid'])) {
                $this->adminMailId = $this->settings['emailadminpid'];
            }
            if (isset($this->settings['emailuserpid'])) {
                $this->userMailId = $this->settings['emailuserpid'];
            }
            if (isset($this->settings['emailinfopid'])) {
                $this->infoMailId = $this->settings['emailinfopid'];
            }
            if (isset($this->settings['testmode'])) {
                $this->testmode = $this->settings['testmode'];
            }
        }
    }

    /**
     * kurswahl action
     * @return ResponseInterface
     */
    public function kurswahlAction(): ResponseInterface
    {
        $this->sessionUtility->cleanSession($this->getUser());
        $this->kursRepository->setStoragePageIds([$this->settings['records']['kurs']]);
        $kurse = $this->kursRepository->findAll();
        $kurseActive = [];
        $tnStatus = [];
        // Professor zuordnen
        if (!empty($kurse)) {
            foreach ($kurse as $kurs) {
                if ($kurs->getAktiv()) {
                    $this->kursanmeldungRepository->setStoragePageIds([$this->settings['records']['tn']]);
                    $kursTn = $this->kursanmeldungRepository->getParticipantsByKurs($kurs->getUid());
                    $activePassiveTn = $this->participantUtility->checkKursParticipant(
                        $kurs,
                        $kursTn->toArray()
                    );
                    $onlyPassive = 0;
                    if (isset($activePassiveTn['aktiveTn']) && $activePassiveTn['aktiveTn'] < 1) {
                        $onlyPassive = 1;
                    };
                    $tnStatus[] = [
                        'kursId' => $kurs->getUid(),
                        'onlyPassive' => $onlyPassive,
                        'activePassiveTn' => $activePassiveTn,
                    ];
                    $kurseActive[] = $kurs;
                }
            }
        }

        $this->view->assign('kurse', $kurseActive);
        $this->view->assign('tnStatus', $tnStatus);

        return $this->htmlResponse();
    }

    /**
     * step1 action
     * @param Step1Data|null $step1data
     * @return ResponseInterface
     */
    #[IgnoreValidation(['argumentName' => 'step1data'])]
    public function step1Action(?Step1Data $step1data = null): ResponseInterface
    {
        // check if step3 completed no backwards functions
        $this->sessionUtility->setFrontendUser($this->getUser());
        if ($this->sessionUtility->isCompletedRegistration()) {
            return $this->redirect(Constants::ACTION_KURS_WAHL);
        }
        // check Browser for reload
        $this->forceHeader();

        // get Kurs from Session
        $kurs = $this->sessionUtility->getData(SessionUtility::FORM_SESSION_KURS);
        if (!$this->request->hasArgument(Constants::KURS)) {
            if (empty($kurs)) {
                return $this->redirect(Constants::ACTION_KURS_WAHL);
            }
        }

        // get kurs from redirect
        if ($this->request->hasArgument(Constants::KURS)) {
            $kursUid = (int)$this->request->getArgument(Constants::KURS);
            $kurs = $this->kursRepository->findByUid($kursUid);
        }

        if (!empty($kurs)) {
            $this->sessionUtility->setData(SessionUtility::FORM_SESSION_KURS, $kurs);
        }

        $step1data = $this->sessionUtility->getData(SessionUtility::FORM_SESSION_STEP1_DATA);
        if (empty($step1data)) {
            $step1data = null;
        }

        $kursTn = $this->kursanmeldungRepository->getParticipantsByKurs($kurs->getUid());
        $tnactionArr = $this->participantUtility->checkKursParticipant(
            $kurs,
            $kursTn->toArray()
        );
        $entries = array(0 => 'f', 1 => 'm');
        $gender = $this->participantUtility->getOptions($entries, 'tx_kursanmeldung_domain_model_kursanmeldung.step1.');

        $kursname = $this->participantUtility->getKursname($kurs);
        $duo = 0;
        $duoselArr = array();

        if (!empty($kurs) && $kurs != null) {
            // duo Setup auslesen
            if ($kurs->getDuo()) {
                $duo = $kurs->getDuo();
                $duoselExpl = explode(',', $kurs->getDuosel());
                $duoselArr = array();
                if (!empty($duoselExpl)) {
                    foreach ($duoselExpl as $duoOpt) {
                        $duoOpt = trim($duoOpt);
                        $duoselArr[$duoOpt] = $duoOpt;
                    }
                }
            }
        }

        // checkItems aus TCA auslesen
        $ensembleCheckbox = $this->formatUtility->buildCBFromTCA($kurs);

        $this->view->assign('ensembleCheckbox', $ensembleCheckbox);
        $this->view->assign('kursname', $kursname);
        $this->view->assign('genders', $gender);
        $this->view->assign(Constants::ACTION_STEP_1_DATA, $step1data);
        $this->view->assign(Constants::KURS, $kurs);
        $this->view->assign('duo', $duo);
        $this->view->assign('duoselArr', $duoselArr);

        return $this->htmlResponse();
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Step1Data $step1data
     * @return \Psr\Http\Message\ResponseInterface
     */
    #[Validate([
        'param' => 'step1data',
        'validator' => Step1DataValidator::class,
    ])]
    public function step1redirectAction(Step1Data $step1data): ResponseInterface
    {
        $this->sessionUtility->setFrontendUser($this->getUser());
        $this->sessionUtility->setData(SessionUtility::FORM_SESSION_STEP1_DATA, $step1data);

        return $this->redirect(Constants::ACTION_STEP_2);
    }

    /**
     * initialize step2 action
     *
     * @return void
     */
    public function initializeStep2Action(): void
    {
        if ($this->arguments->hasArgument(Constants::ACTION_STEP_2_DATA)) {
            $this->propertyConverterUtility->convertArgumentsStep2Data($this->arguments);
        }
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Step2Data|null $step2data
     * @return \Psr\Http\Message\ResponseInterface
     */
    #[IgnoreValidation(['argumentName' => 'step2data'])]
    public function step2Action(?Step2Data $step2data = null): ResponseInterface
    {
        // check if step3 completed no backwards functions
        $this->sessionUtility->setFrontendUser($this->getUser());
        if ($this->sessionUtility->isCompletedRegistration()) {
            return $this->redirect(Constants::ACTION_KURS_WAHL);
        }
        // check Browser for reload
        $this->forceHeader();

        // get Kurs from Session
        $kurs = $this->sessionUtility->getData(SessionUtility::FORM_SESSION_KURS);
        if (!$this->request->hasArgument(Constants::KURS)) {
            if (empty($kurs)) {
                return $this->redirect(Constants::ACTION_KURS_WAHL);
            }
        }

        $step2data = $this->sessionUtility->getData(SessionUtility::FORM_SESSION_STEP2_DATA);
        if (empty($step2data)) {
            $step2data = null;
        }

        $kursTn = $this->kursanmeldungRepository->getParticipantsByKurs($kurs->getUid());
        $tnactions = $this->participantUtility->checkKursParticipant(
            $kurs,
            $kursTn->toArray()
        );
        $tnactionOptions = [];
        if (is_array($tnactions)) {
            $tnaction = $this->participantUtility->getOptions(
                array_keys($tnactions),
                'tx_kursanmeldung_domain_model_kursanmeldung.step2.'
            );
        }

        $enrollmentFee = 0;
        $additionalFee = 0;

        $gebuehr = $this->gebuehrenRepository->findByUid($kurs->getGebuehr());
        if (!empty($gebuehr)) {
            $enrollmentFee = $gebuehr->getAnmeldung();
            $additionalFee = $gebuehr->getAktivengeb();
        }
        // hotel aufsplitten für selectbox Hotel->Zimmer->Beischläfer
        $hotel = $this->participantUtility->splitHotel($kurs->getHotel());

        //zahlungsart todo:variable gestalten
        $zahlungsart = $this->participantUtility->getOptions(
            $this->zahlungsartArr,
            'tx_kursanmeldung_domain_model_kursanmeldung.step2.'
        );
        $zahlungstermin = new \DateTime('NOW');
        $zahlungstermin->add(new \DateInterval('P10D'));

        // downloads src und name teilen
        if ($step2data != null) {
            $downloads = array();
            $downloadData = $step2data->getDownload();
            if (!empty($downloadData)) {
                foreach ($downloadData as $key => $value) {
                    $downloads[$key]['src'] = $value;
                    $downloads[$key]['name'] = basename($value);
                }
            }

            // downloads src und name teilen
            $vita = array('name' => '');
            $vita['src'] = $step2data->getVita();
            if (!empty($vita['src'])) {
                $vita['name'] = basename($vita['src']);
            }
        } else {
            $step2data = new Step2Data();
        }

        if ($step2data->getRoomfrom() === '' && !empty($kurs)) {
            $roomFrom = $kurs->getAnreisedate();
            $step2data->setRoomfrom($roomFrom->format('Y-m-d'));
        }
        if ($step2data->getRoomto() === '' && !empty($kurs)) {
            $roomto = $kurs->getKurszeitend();
            $roomto->add(new \DateInterval('P1D'));
            $step2data->setRoomto($roomto->format('Y-m-d'));
        }

        // look for already registered user by email and kurs id
        $alreadyParticipant = $this->participantUtility->checkForParticipant(
            $this->sessionUtility->getData(SessionUtility::FORM_SESSION_STEP1_DATA),
            $kurs
        );

        // if enrollmentfee 0 or 0,00 or empty no Paymentselectfield
        if (empty($enrollmentFee)) {
            $this->zahlungsartArr[9] = 'standard';
            $zahlungsart = 9;
        }

        $kursname = $this->participantUtility->getKursname($kurs);
        $showUploadHint = ($kurs->getYoutube() > 0 || $kurs->getWeblink() > 0 || $kurs->getMaxupload() > 0) ? 1 : 0;

        $this->view->assign('alreadyParticipant', $alreadyParticipant);
        $this->view->assign('tnaction', $tnaction);
        $this->view->assign('kursname', $kursname);
        $this->view->assign('zahlungsart', $zahlungsart);
        $this->view->assign('zahlungstermin', $zahlungstermin->format('d.m.Y'));
        $this->view->assign('enrollmentfee', $enrollmentFee);
        $this->view->assign('additionalfee', $additionalFee);
        $this->view->assign('showUploadHint', $showUploadHint);
        $this->view->assign(Constants::KURS, $kurs);
        $this->view->assign('gebuehr', $gebuehr);
        $this->view->assign('hotel', $hotel);
        $this->view->assign('step2data', $step2data);

        return $this->htmlResponse();
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Step2Data $step2data
     * @return \Psr\Http\Message\ResponseInterface
     */
    #[Validate([
        'param' => 'step2data',
        'validator' => Step2DataValidator::class,
    ])]
    public function step2redirectAction(Step2Data $step2data): ResponseInterface
    {
        $this->sessionUtility->setFrontendUser($this->getUser());
        // hydrate downloads from session if needed to persist uploads across redirects
        $sessionStep2 = $this->sessionUtility->getData(SessionUtility::FORM_SESSION_STEP2_DATA);
        if ($sessionStep2 instanceof Step2Data) {
            //hydrate downloads
            $incomingDownloads = $step2data->getDownload();
            $sessionDownloads = $sessionStep2->getDownload();

            // If session has downloads and incoming has none, keep the session downloads
            $incomingHasDownloads = !empty($incomingDownloads) && count($incomingDownloads) > 0;
            $sessionHasDownloads = !empty($sessionDownloads) && count($sessionDownloads) > 0;
            if ($sessionHasDownloads && !$incomingHasDownloads) {
                $step2data->setDownload($sessionDownloads);
            }

            //hydrate vita
            $incomingVitas = $step2data->getVita();
            $sessionVitas = $sessionStep2->getVita();

            // If session has downloads and incoming has none, keep the session downloads
            $incomingHasVitas = !empty($incomingVitas);
            $sessionHasVitas = !empty($sessionVitas);
            if ($sessionHasVitas && !$incomingHasVitas) {
                $step2data->setVita($sessionVitas);
            }
        }

        // store the hydrated Step2Data into session
        $this->sessionUtility->setData(SessionUtility::FORM_SESSION_STEP2_DATA, $step2data);

        return $this->redirect(Constants::ACTION_STEP_3);
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Step3Data|null $step3data
     * @return \Psr\Http\Message\ResponseInterface
     */
    #[IgnoreValidation(['argumentName' => 'step3data'])]
    public function step3Action(?Step3Data $step3data = null): ResponseInterface
    {
        // check if step3 completed no backwards functions
        $this->sessionUtility->setFrontendUser($this->getUser());
        if ($this->sessionUtility->isCompletedRegistration()) {
            return $this->redirect(Constants::ACTION_KURS_WAHL);
        }
        // check Browser for reload
        $this->forceHeader();

        // get Kurs from Session
        $kurs = $this->sessionUtility->getData(SessionUtility::FORM_SESSION_KURS);
        if (!$this->request->hasArgument(Constants::KURS)) {
            if (empty($kurs)) {
                return $this->redirect(Constants::ACTION_KURS_WAHL);
            }
        }

        $step1data = $this->sessionUtility->getData(SessionUtility::FORM_SESSION_STEP1_DATA);
        if (empty($step1data)) {
            $step1data = null;
        }

        $step2data = $this->sessionUtility->getData(SessionUtility::FORM_SESSION_STEP2_DATA);
        if (empty($step2data)) {
            $step2data = null;
        }

        $step3data = $this->sessionUtility->getData(SessionUtility::FORM_SESSION_STEP3_DATA);
        if (empty($step3data)) {
            $step3data = null;
        }

        $kursname = $this->participantUtility->getKursname($kurs);

        // hotel
        $hotel = !empty($step2data->getHotel()) ? $this->hotelRepository->findByUid($step2data->getHotel()) : 0;
        $fee = 0;
        $room = $step2data->getRoom();
        if (!empty($hotel)) {
            $uroom = 'get' . ucfirst($room);
            if (method_exists($hotel, $uroom)) {
                $fee = $hotel->$uroom();
            }

            $hotel = array(
                'name' => $hotel->getHotel(),
                'room' => LocalizationUtility::translate(
                    'tx_kursanmeldung_domain_model_kursanmeldung.step2.val' . $room,
                    'kursanmeldung'
                ),
                'fee' => $fee
            );
        }

        // gebühren
        $gebuehr = null;
        if (!empty($kurs) && $kurs != null) {
            $gebuehr = $this->gebuehrenRepository->findByUid($kurs->getGebuehr());
        }
        $additionalfee = $enrollmentfee = 0;

        // wenn studentship != null alles 0
        if (!$step2data->getStudentship() || $gebuehr === null) {
            $enrollmentfee = ($step2data->getTnaction() === 1) ? $gebuehr->getPassivgeb() : $gebuehr->getAnmeldung();
            $additionalfee = ($step2data->getTnaction() === 1) ? 0 : $gebuehr->getAktivengeb();
            // wenn studystat != null halber preis
            if ($step2data->getStudystat()) {
                $enrollmentfee = ($step2data->getTnaction() === 1) ? $gebuehr->getPassivgeberm(
                ) : $gebuehr->getAnmeldungerm();
                $additionalfee = ($step2data->getTnaction() === 1) ? 0 : $gebuehr->getAktivengeberm();
            }
        }

        $vita = $step2data->getVita() ? 1 : 0;
        $downloads = $step2data->getDownload() ? count($step2data->getDownload()) : 0;
        $hidedl = 0;
        if (empty($vita) && empty($downloads) && empty($step2data->getLink()) && empty($step2data->getYoutube())) {
            $hidedl = 1;
        }

        $this->view->assign(Constants::KURS, $kurs);
        $this->view->assign('kursname', $kursname);
        $this->view->assign('zahlungsart', $this->zahlungsartArr[$step2data->getZahlungsart()]);
        $this->view->assign('hotel', $hotel);
        $this->view->assign('additionalfee', $additionalfee);
        $this->view->assign('enrollmentfee', $enrollmentfee);
        $this->view->assign('hidedl', $hidedl);
        $this->view->assign('step1data', $step1data);
        $this->view->assign('step2data', $step2data);
        $this->view->assign('step3data', $step3data);

        return $this->htmlResponse();
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Step3Data $step3data
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function step3redirectAction(Step3Data $step3data): ResponseInterface
    {
        $this->sessionUtility->setFrontendUser($this->getUser());
        $this->sessionUtility->setData(SessionUtility::FORM_SESSION_STEP3_DATA, $step3data);

        return $this->redirect(Constants::ACTION_STEP_4);
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Step4Data|null $step4data
     * @return \Psr\Http\Message\ResponseInterface
     */
    #[IgnoreValidation(['argumentName' => 'step4data'])]
    public function step4Action(?Step4Data $step4data = null): ResponseInterface
    {
        $this->sessionUtility->setFrontendUser($this->getUser());
        // get Kurs from Session
        $kurs = $this->sessionUtility->getData(SessionUtility::FORM_SESSION_KURS);
        if (!$this->request->hasArgument(Constants::KURS)) {
            if (empty($kurs)) {
                return $this->redirect(Constants::ACTION_KURS_WAHL);
            }
        }

        $step1data = $this->sessionUtility->getData(SessionUtility::FORM_SESSION_STEP1_DATA);
        if (empty($step1data)) {
            $step1data = null;
        }

        $step2data = $this->sessionUtility->getData(SessionUtility::FORM_SESSION_STEP2_DATA);
        if (empty($step2data)) {
            $step2data = null;
        }

        $step3data = $this->sessionUtility->getData(SessionUtility::FORM_SESSION_STEP3_DATA);
        if (empty($step3data)) {
            $step3data = null;
        }

        $step4data = $this->sessionUtility->getData(SessionUtility::FORM_SESSION_STEP4_DATA);
        if (empty($step4data)) {
            $step4data = null;
        }

        $zahlart = 0;
        if ($step2data != null) {
            $zahlart = $step2data->getZahlungsart();
            if ($zahlart === 9 || !in_array(
                    $this->zahlungsartArr[$zahlart],
                    $this->zahlungsartNovalnetArr
                )
            ) {
                return $this->redirect('step5');
            }
        }

        $kursanmeldungUid = 0;
        if (!empty($this->sessionUtility->getData(SessionUtility::FORM_SESSION_ANMELDUNG_UID))) {
            $kursanmeldungUid = $this->sessionUtility->getData(SessionUtility::FORM_SESSION_ANMELDUNG_UID);
        }

        $select = [
            'payment' => $this->participantUtility->getOptions(
                $this->zahlungsartArr,
                'tx_kursanmeldung_domain_model_kursanmeldung.step2.'
            ),
            'act' => $zahlart
        ];

        // if payment error or not send
        $p = '';    // err / suc for payment answer
        if ($this->request->hasArgument('p')) {
            $p = $this->request->getArgument('p');
        }

        $zahlungstermin = new \DateTime('NOW');
        $zahlungstermin->add(new \DateInterval('P10D'));
        $form = '';
        $payment = '';
        $addTN = false;

        if ($kursanmeldungUid === 0) {
            $newKursanmeldung = new Kursanmeldung();
            $newKursanmeldung->setPid($this->settings['records']['tn']);
            $newTn = new Teilnehmer();
            $newTn->setPid($this->settings['records']['tn']);
            $addTN = true;
        } else {
            $newKursanmeldung = $this->kursanmeldungRepository->findByUid($kursanmeldungUid);
            $newTn = $newKursanmeldung->getTn()->current();
            if (empty($newTn)) {
                $newTn = new Teilnehmer();
                $newTn->setPid($this->settings['records']['tn']);
                $addTN = true;
            }
        }
        /* Gebühr berechnen */
        $gebuehr = $this->gebuehrenRepository->findByUid($kurs->getGebuehr());
        $enrollmentfee = 0;

        // wenn studentship != null alles 0
        if (!$step2data->getStudentship()) {
            $enrollmentfee = ($step2data->getTnaction() === 1) ? $gebuehr->getPassivgeb() : $gebuehr->getAnmeldung();
            // wenn studystat != null halber preis
            if ($step2data->getStudystat()) {
                $enrollmentfee = ($step2data->getTnaction() === 1) ? $gebuehr->getPassivgeberm(
                ) : $gebuehr->getAnmeldungerm();
            }
        }

        $language = $this->request->getAttribute('language') ?? $this->request->getAttribute(
            'site'
        )->getDefaultLanguage();
        $newTn->setSprache($language->getTitle());

        $stepDataDto = new StepDataParticipantDto(
            $step1data,
            $step2data,
            $newTn
        );

        $this->participantFacade->hydrateParticipantFromStepData($stepDataDto);
        if ($addTN) {
            $newKursanmeldung->addTn($stepDataDto->getTeilnehmer());
        }

        $newKursanmeldung->setKurs($kurs);
        $newKursanmeldung->setStudentship($step2data->getStudentship() ?? 0);
        $newKursanmeldung->setStudystat((int)$step2data->getStudystat());
        $newKursanmeldung->setZahlart($step2data->getZahlungsart());
        $newKursanmeldung->setZahltbis($zahlungstermin);
        $newKursanmeldung->setHotel((int)$step2data->getHotel());
        $newKursanmeldung->setRoom($step2data->getRoom());
        if ($step2data->getHotel() !== '') {
            $newKursanmeldung->setRoomwith($step2data->getRoomwith());
            $newKursanmeldung->setRoomfrom($step2data->getRoomfrom());
            $newKursanmeldung->setRoomto($step2data->getRoomto());
        }
        $newKursanmeldung->setGebuehr($enrollmentfee);
        $newKursanmeldung->setDatein(new \DateTime('NOW'));
        $newKursanmeldung->setTeilnahmeart($step2data->getTnaction());
        $newKursanmeldung->setProgramm($step2data->getProgramm());
        $newKursanmeldung->setOrchesterstudio($step2data->getOrchesterstudio() ?? '');
        $newKursanmeldung->setComment($step2data->getComment());
        $newKursanmeldung->setAgb($step3data->getTnb());
        $newKursanmeldung->setDatenschutz($step3data->getPrivacy());
        $newKursanmeldung->setDeflang((int)$language->getLanguageId());

        // duodaten speichern
        if((int)$step1data->getDuo() === 1){
            $newKursanmeldung->setDuo((int)$step1data->getDuo());
            $newKursanmeldung->setDuosel($step1data->getDuosel());
            $newKursanmeldung->setDuoname($step1data->getDuoname());
        }

        // Ensemble speichern
        if (!empty($step1data->getEnconf())) {
            if (!empty($step1data->getEnuid())) {
                $uidArr = $step1data->getEnuid();
                $enfirstnArr = $step1data->getEnfirstn();
                $enlastnArr = $step1data->getEnlastn();
                $eninstruArr = $step1data->getEninstru();
                $engebdateArr = $step1data->getEngebdate();
                $ennatioArr = $step1data->getEnnatio();

                foreach ($uidArr as $key => $uid) {
                    $newensemble = new Ensemble();
                    $newensemble->setPid($this->settings['records']['tn']);
                    $newensemble->setEnconf($step1data->getEnconf());
                    $newensemble->setEntn($step1data->getEntn());
                    $newensemble->setEnfirstn($enfirstnArr[$key]);
                    $newensemble->setEnlastn($enlastnArr[$key]);
                    $newensemble->setEninstru($eninstruArr[$key]);

                    $date = \DateTime::createFromFormat('d.m.Y', $engebdateArr[$key]);
                    if ($date) {
                        $engebdateArr[$key] = $date->format('Y-m-d');
                    }

                    $newensemble->setEngebdate($engebdateArr[$key]);
                    $newensemble->setEnnatio($ennatioArr[$key]);

                    $date = \DateTime::createFromFormat('d.m.Y', $step1data->getEngrdate());
                    if ($date) {
                        $step1data->setEngrdate($date->format('Y-m-d'));
                    }

                    $newensemble->setEngrdate($step1data->getEngrdate());
                    $newensemble->setEnname($step1data->getEnname());
                    $newensemble->setEntype($step1data->getEntype());
                    $newensemble->setEngrplace($step1data->getEngrplace());
                    $newKursanmeldung->addEnsemble($newensemble);
                }
            } else {
                $key = 0;
                $enfirstnArr = $step1data->getEnfirstn();
                $enlastnArr = $step1data->getEnlastn();
                $eninstruArr = $step1data->getEninstru();
                $engebdateArr = $step1data->getEngebdate();
                $ennatioArr = $step1data->getEnnatio();

                $newensemble = new Ensemble();
                $newensemble->setPid($this->settings['records']['tn']);
                $newensemble->setEnconf($step1data->getEnconf());
                $newensemble->setEntn($step1data->getEntn());
                $newensemble->setEnfirstn($enfirstnArr[$key]);
                $newensemble->setEnlastn($enlastnArr[$key]);
                $newensemble->setEninstru($eninstruArr[$key]);

                $date = \DateTime::createFromFormat('d.m.Y', $engebdateArr[$key]);
                if ($date) {
                    $engebdateArr[$key] = $date->format('Y-m-d');
                }

                $newensemble->setEngebdate($engebdateArr[$key]);
                $newensemble->setEnnatio($ennatioArr[$key]);

                $date = \DateTime::createFromFormat('d.m.Y', $step1data->getEngrdate());
                if ($date) {
                    $step1data->setEngrdate($date->format('Y-m-d'));
                }

                $newensemble->setEngrdate($step1data->getEngrdate());
                $newensemble->setEnname($step1data->getEnname());
                $newensemble->setEntype($step1data->getEntype());
                $newensemble->setEngrplace($step1data->getEngrplace());
                $newKursanmeldung->addEnsemble($newensemble);
            }
        }

        if (!empty($step2data->getDownload())) {
            $downloads = $step2data->getDownload();
            if (!empty($downloads)) {
                foreach ($downloads as $fileReference) {
                    if (!empty($fileReference)) {
                        $newDl = new Uploads();
                        $newDl->setPid($this->settings['records']['tn']);
                        $newDl->setKurs($kurs);
                        $newDl->setKat('download');
                        $newDl->setName($fileReference->getOriginalResource()?->getName() ?? '');
                        $newDl->setPfad($fileReference->getOriginalResource()?->getIdentifier() ?? '');
                        $newDl->setDatein(new \DateTime('NOW'));
                        $newDl->setFileref($fileReference);
                        $newKursanmeldung->addUploads($newDl);
                    }
                }
            }
        }

        if (!empty($step2data->getYoutube())) {
            $src = trim($step2data->getYoutube());
            if (!empty($src)) {
                $srcBn = pathinfo($src);
                $newDl = new Uploads();
                $newDl->setPid($this->settings['records']['tn']);
                $newDl->setKurs($kurs);
                $newDl->setKat('youtube');
                $newDl->setName($srcBn['basename']);
                $newDl->setPfad($src);
                $newDl->setDatein(new \DateTime('NOW'));
                $newKursanmeldung->addUploads($newDl);
            }
        }

        if (!empty($step2data->getVita())) {
            $fileReference = $step2data->getVita();
            if (!empty($fileReference->getOriginalResource())) {
                $newDl = new Uploads();
                $newDl->setPid($this->settings['records']['tn']);
                $newDl->setKurs($kurs);
                $newDl->setKat('vita');
                $newDl->setName($fileReference->getOriginalResource()?->getName() ?? '');
                $newDl->setPfad($fileReference->getOriginalResource()?->getIdentifier() ?? '');
                $newDl->setDatein(new \DateTime('NOW'));
                $newDl->setFileref($fileReference);
                $newKursanmeldung->addUploads($newDl);
            }
        }

        if (!empty($step2data->getLink())) {
            $src = trim($step2data->getLink());
            $srcBn = pathinfo($src);
            $newDl = new Uploads();
            $newDl->setPid($this->settings['records']['tn']);
            $newDl->setKurs($kurs);
            $newDl->setKat('link');
            $newDl->setName($srcBn['basename']);
            $newDl->setPfad($src);
            $newDl->setDatein(new \DateTime('NOW'));
            $newKursanmeldung->addUploads($newDl);
        }

        $newKursanmeldung->setSavedata((int)$step3data->getSavedata());

        if ($kursanmeldungUid === 0) {
            $hash = $this->participantUtility->getHashedPasswordFromPassword($newTn->getEmail());
            $newKursanmeldung->setRegistrationkey($hash);

            $this->kursanmeldungRepository->add($newKursanmeldung);
        } else {
            $this->kursanmeldungRepository->update($newKursanmeldung);
        }

        $this->persistenceManager->persistAll();

        if ($newKursanmeldung->getUid() > 0) {
            $this->sessionUtility->setData(
                SessionUtility::FORM_SESSION_ANMELDUNG_UID,
                $newKursanmeldung->getUid()
            );

            if (!$this->sessionUtility->getData(SessionUtility::FORM_SESSION_SEND_MAIL)) {
                $this->sendInfoMail($newTn, $newKursanmeldung);

                $this->sessionUtility->setData(
                    SessionUtility::FORM_SESSION_SEND_MAIL,
                    $newKursanmeldung->getUid()
                );
            }

            $banktransfer = $this->getBanktransferData($newKursanmeldung);
            $this->sendInvoiceMail($newKursanmeldung, $newTn, $banktransfer);

            $formVars = $this->novalnetArray($newKursanmeldung);

            // payment anstoßen 1=>'banktransfer', 2=>'prepayment', 3=>'paypal', 4=>'onlinetransfer', 5=>'giropay', 6=>'invoice'
            switch ($newKursanmeldung->getZahlart()) {
                case 1:
                    $payment = 'banktransfer';
                    $this->logger->info('banktransfer suc:');
                    $banktransfer = $this->getBanktransferData($newKursanmeldung);
                    $this->logger->info('banktransfer suc POST:' . print_r($banktransfer, true));

                    // emails versenden
                    $this->sendInvoiceMail($newKursanmeldung, $newTn, $banktransfer);
                    $this->sendRegisterMail($newKursanmeldung, $newTn);
                    $this->sessionUtility->cleanSession($this->getUser());
                    break;
                case 2:
                case 6:
                    $payment = 'invoice';
                    $novalnetXML[1]['url'] = 'https://payport.novalnet.de/payport.xml';
                    $novalnetXML[1]['path'] = 'Novalnet/VorkassePayport.html';
                    //$novalnetXML[1]['doc'] = $this->getMailBody($novalnetXML[1]['path'], $formVars);
                    $request = $novalnetXML[1];

                    $xml_response = $this->curl_xml_post(
                        $request
                    );
                    // Die Variable $request enthält den XML-Aufruf. Sehen Sie sich dazu das Aufrufbeispiel oben an
                    $novalnetReposeDto = new NovalnetResponseDto(
                        $xml_response,
                        [],
                        false,
                        ''
                    );

                    $novalnetReposeDto = $this->novalnetFacade->getNovalnetResponse($novalnetReposeDto);

                    $response = new \SimpleXMLElement($xml_response);

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

                    $newKursanmeldung->setNovalnettid($novalnet['tid']);
                    $newKursanmeldung->setNovalnetcno($novalnet['customer_no']);
                    $this->kursanmeldungRepository->update($newKursanmeldung);
                    $this->persistenceManager->persistAll();

                    // emails versenden
                    //$this->sendInvoiceMail($newKursanmeldung, $newTn, $novalnet);
                    $this->sessionUtility->cleanSession($this->getUser());
                    break;
                case 3:
                    $payment = 'paypal';
                    $novalnetXML[3]['url'] = 'https://payport.novalnet.de/paypal_payport';
                    $novalnetXML[3]['vars'] = $formVars;
                    $form = $novalnetXML[3];
                    break;
                case 4:
                    $payment = 'onlinetransfer';
                    $novalnetXML[2]['url'] = 'https://payport.novalnet.de/online_transfer_payport';
                    $novalnetXML[2]['vars'] = $formVars;
                    $form = $novalnetXML[2];
                    break;
                case 5:
                    $payment = 'giropay';
                    $novalnetXML[2]['url'] = 'https://payport.novalnet.de/giropay';
                    $novalnetXML[2]['vars'] = $formVars;
                    $form = $novalnetXML[2];
                    break;
            }
        } else {
            // fehler redirect btstep1
            $this->addFlashMessage(
                $this->participantUtility->translateFromXlf('tx_kursanmeldung_domain_model_kursanmeldung.err001_body'),
                $this->participantUtility->translateFromXlf('tx_kursanmeldung_domain_model_kursanmeldung.err001_title'),
                ContextualFeedbackSeverity::ERROR
            );
            //$this->redirect('step1');
        }

        $site = $this->request->getAttribute('site');
        $baseUrl = (string)$site->getBase();
        $kursname = $this->participantUtility->getKursname($kurs);

        $this->view->assign('kursname', $kursname);
        $this->view->assign('baseURL', $baseUrl);
        $this->view->assign('returnUrlSuccess', $this->getStep4RedirectUrl('suc'));
        $this->view->assign('returnUrlError', $this->getStep4RedirectUrl('err'));
        $this->view->assign('newKursanmeldung', $newKursanmeldung);
        $this->view->assign('payment', $payment);
        $this->view->assign('p', $p);
        $this->view->assign('form', $form);
        $this->view->assign('select', $select);
        $this->view->assign(Constants::KURS, $kurs);

        return $this->htmlResponse();
    }


    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    public function step4redirectAction(): ResponseInterface
    {
        $this->sessionUtility->setFrontendUser($this->getUser());
        $error = [];

        $paylaterType = '';
        $kursanmeldungUid = 0;
        if (!empty($this->sessionUtility->getData(SessionUtility::FORM_SESSION_ANMELDUNG_UID))) {
            $kursanmeldungUid = $this->sessionUtility->getData(SessionUtility::FORM_SESSION_ANMELDUNG_UID);
        }

        // if payment error or not send
        $kursAnmeldung = $this->kursanmeldungRepository->findByUid($kursanmeldungUid);

        $p = 'err';        // err / suc for payment answer
        if ($this->request->hasArgument('p')) {
            $p = $this->request->getArgument('p');
        }

        // checken ob aufruf durch paylater initialisiert
        $paylaterSrc = false;
        if ((isset($_POST['input2']) && $_POST['input2'] == 'pl') || (isset($_POST['pl']) && $_POST['pl'] == 'ang') || (isset($_POST['pl']) && $_POST['pl'] == 'tng')) {
            $paylaterSrc = true;
            $this->sessionUtility->setData(SessionUtility::FORM_SESSION_PL, '');
        }

        if ((isset($_POST['input2']) && $_POST['input2'] == 'pl') && isset($_POST['inputval2'])) {
            $paylaterType = $_POST['inputval2'];
        }

        if (isset($_POST['pl'])) {
            $paylaterType = $_POST['pl'];
        }

        if ($p == 'suc') {
            // if payment successfully
            // save paymentstatus
            $this->logger->info('Step4 redirect POST:' . print_r($_POST, true));

            $kurs = (empty($kursAnmeldung)) ? 0 : $kursAnmeldung->getKurs()->current()->getUid();
            $this->logger->info('Step 4 redirect Kurs:' . $kurs);

            $kursActive = $this->kursRepository->findByUid($kurs);
            if ($kursActive === null) {
                array_push($error, 'no Kurs found');
            }
            if (empty($error)) {
                if ($paylaterSrc && $paylaterType != 'ang') {
                    $kursAnmeldung->setNovalnettidag($_POST['tid']);
                    // wenn direkt bezahlt wurde (rechnung == 27 nicht direkt)
                    if ($_POST['payment_id'] != 27) {
                        $kursAnmeldung->setBezahltag(1);
                        $kursAnmeldung->setGezahltag(number_format($kursAnmeldung->getGebuehrag(), 2, ',', '.'));
                    }
                } else {
                    $kursAnmeldung->setNovalnettid($_POST['tid']);
                    // wenn direkt bezahlt wurde (rechnung == 27 nicht direkt)
                    if ($_POST['payment_id'] != 27) {
                        $kursAnmeldung->setBezahlt(1);
                        $kursAnmeldung->setGezahlt(number_format($kursAnmeldung->getGebuehr(), 2, ',', '.'));
                    }
                    $kursAnmeldung->setDoitime(new \DateTime('NOW'));
                }

                $this->kursanmeldungRepository->update($kursAnmeldung);
                $this->persistenceManager->persistAll();

                $this->logger->info('Step 4 redirect SUCCESS:' . $kurs);
            } else {
                $this->logger->info('Step 4 redirect ERROR:' . $kurs . ' : ' . $_POST['tid']);
            }
            if ($paylaterSrc) {
                return $this->redirect('paylater', null, null, array('p' => $p));
            } else {
                return $this->redirect('step5novalnet', null, null, array('p' => $p));
            }
        } else {
            $this->logger->info('Step 4 redirect ERROR p:' . print_r($_POST, true));
            if ($paylaterSrc) {
                return $this->redirect('paylater', null, null, array('p' => $p));
            } else {
                return $this->redirect('step4', null, null, array('p' => $p));
            }
        }
    }


    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Core\Crypto\PasswordHashing\InvalidPasswordHashException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    public function step5Action(): ResponseInterface
    {
        $this->sessionUtility->setFrontendUser($this->getUser());
        $kurs = null;
        if ($this->sessionUtility->getData(SessionUtility::FORM_SESSION_KURS)) {
            $kurs = $this->sessionUtility->getData(SessionUtility::FORM_SESSION_KURS);
        }

        if ($kurs === null) {
            return $this->redirect(Constants::ACTION_KURS_WAHL);
        }

        // check free places again
        $error = [];

        /**
         * @var Step1Data|null $step1data
         */
        $step1data = null;
        if ($this->sessionUtility->getData(SessionUtility::FORM_SESSION_STEP1_DATA)) {
            $step1data = $this->sessionUtility->getData(SessionUtility::FORM_SESSION_STEP1_DATA);
        } else {
            array_push($error, 'step1data');
        }

        /**
         * @var Step2Data|null $step2data
         */
        $step2data = null;
        if ($this->sessionUtility->getData(SessionUtility::FORM_SESSION_STEP2_DATA)) {
            $step2data = $this->sessionUtility->getData(SessionUtility::FORM_SESSION_STEP2_DATA);
            if ($step2data->getZahlungsart() === 9) {
                $this->zahlungsartArr[9] = 'standard';
            }
        } else {
            array_push($error, 'step2data');
        }

        $step3data = null;
        if ($this->sessionUtility->getData(SessionUtility::FORM_SESSION_STEP3_DATA)) {
            $step3data = $this->sessionUtility->getData(SessionUtility::FORM_SESSION_STEP3_DATA);
        } else {
            array_push($error, 'step3data');
        }

        $step4data = null;
        if (empty($error) && $step2data != null) {
            $zahlart = $step2data->getZahlungsart();
            if (in_array($this->zahlungsartArr[$zahlart], $this->zahlungsartNovalnetArr)) {
                if ($this->sessionUtility->getData(SessionUtility::FORM_SESSION_STEP4_DATA)) {
                    $step4data = $this->sessionUtility->getData(SessionUtility::FORM_SESSION_STEP4_DATA);
                } else {
                    array_push($error, 'step4data');
                }
            }
        }

        $kursTn = $this->kursanmeldungRepository->getParticipantsByKurs($kurs->getUid());
        $tnactionArr = $this->participantUtility->checkKursParticipant($kurs, $kursTn->toArray());

        if ($kurs === null) {
            array_push($error, 'no Kurs found');
        }


        $zahlungstermin = new \DateTime('NOW');
        $zahlungstermin->add(new \DateInterval('P10D'));

        if (empty($error)) {
            /* Gebühr berechnen */
            $gebuehr = $this->gebuehrenRepository->findByUid($kurs->getGebuehr());
            $enrollmentfee = 0;

            // wenn studentship != null alles 0
            if (!$step2data->getStudentship()) {
                $enrollmentfee = ($step2data->getTnaction() == 1) ? $gebuehr->getPassivgeb() : $gebuehr->getAnmeldung();
                // wenn studystat != null halber preis
                if ($step2data->getStudystat()) {
                    $enrollmentfee = ($step2data->getTnaction() == 1) ? $gebuehr->getPassivgeberm(
                    ) : $gebuehr->getAnmeldungerm();
                }
            }


            /* Teilnehmer speichern */
            $newTn = new Teilnehmer();
            $newTn->setPid($this->settings['records']['tn']);
            $language = $this->request->getAttribute('language') ?? $this->request->getAttribute(
                'site'
            )->getDefaultLanguage();
            $newTn->setSprache($language->getTitle());

            $stepDataDto = new StepDataParticipantDto(
                $step1data,
                $step2data,
                $newTn
            );
            $this->participantFacade->hydrateParticipantFromStepData($stepDataDto);

            $newKursanmeldung = new Kursanmeldung();
            $newKursanmeldung->setPid($this->settings['records']['tn']);
            $newKursanmeldung->addTn($stepDataDto->getTeilnehmer());
            $newKursanmeldung->setKurs($kurs);
            $newKursanmeldung->setStudentship($step2data->getStudentship() ?? 0);
            $newKursanmeldung->setStudystat((int)$step2data->getStudystat());
            $newKursanmeldung->setZahlart($step2data->getZahlungsart());
            $newKursanmeldung->setZahltbis($zahlungstermin);
            $newKursanmeldung->setHotel((int)$step2data->getHotel());
            $newKursanmeldung->setRoom($step2data->getRoom());
            if ($step2data->getHotel() != '') {
                $newKursanmeldung->setRoomwith($step2data->getRoomwith());
                $newKursanmeldung->setRoomfrom($step2data->getRoomfrom());
                $newKursanmeldung->setRoomto($step2data->getRoomto());
            }
            $newKursanmeldung->setGebuehr($enrollmentfee);
            $newKursanmeldung->setDatein(new \DateTime('NOW'));
            $newKursanmeldung->setTeilnahmeart($step2data->getTnaction());
            $newKursanmeldung->setProgramm($step2data->getProgramm());
            $newKursanmeldung->setOrchesterstudio($step2data->getOrchesterstudio() ?? '');
            $newKursanmeldung->setComment($step2data->getComment());
            $newKursanmeldung->setAgb($step3data->getTnb());
            $newKursanmeldung->setDatenschutz($step3data->getPrivacy());
            $newKursanmeldung->setDeflang((int)$language->getLanguageId());

            // duodaten speichern
            if ($step1data->getDuo() == 1) {
                $newKursanmeldung->setDuo($step1data->getDuo());
                $newKursanmeldung->setDuosel($step1data->getDuosel());
                $newKursanmeldung->setDuoname($step1data->getDuoname());
            }

            // Ensemble speichern			
            if (!empty($step1data->getEnconf())) {
                if (!empty($step1data->getEnuid())) {
                    $uidArr = $step1data->getEnuid();
                    $enfirstnArr = $step1data->getEnfirstn();
                    $enlastnArr = $step1data->getEnlastn();
                    $eninstruArr = $step1data->getEninstru();
                    $engebdateArr = $step1data->getEngebdate();
                    $ennatioArr = $step1data->getEnnatio();

                    foreach ($uidArr as $key => $uid) {
                        $newensemble = new Ensemble();
                        $newensemble->setPid($this->settings['records']['tn']);
                        $newensemble->setEnconf($step1data->getEnconf());
                        $newensemble->setEntn($step1data->getEntn());
                        $newensemble->setEnfirstn($enfirstnArr[$key]);
                        $newensemble->setEnlastn($enlastnArr[$key]);
                        $newensemble->setEninstru($eninstruArr[$key]);

                        $date = \DateTime::createFromFormat('d.m.Y', $engebdateArr[$key]);
                        if ($date) {
                            $engebdateArr[$key] = $date->format('Y-m-d');
                        }

                        $newensemble->setEngebdate($engebdateArr[$key]);
                        $newensemble->setEnnatio($ennatioArr[$key]);

                        $date = \DateTime::createFromFormat('d.m.Y', $step1data->getEngrdate());
                        if ($date) {
                            $step1data->setEngrdate($date->format('Y-m-d'));
                        }

                        $newensemble->setEngrdate($step1data->getEngrdate());
                        $newensemble->setEnname($step1data->getEnname());
                        $newensemble->setEntype($step1data->getEntype());
                        $newensemble->setEngrplace($step1data->getEngrplace());
                        $newKursanmeldung->addEnsemble($newensemble);
                    }
                } else {
                    $key = 0;
                    $enfirstnArr = $step1data->getEnfirstn();
                    $enlastnArr = $step1data->getEnlastn();
                    $eninstruArr = $step1data->getEninstru();
                    $engebdateArr = $step1data->getEngebdate();
                    $ennatioArr = $step1data->getEnnatio();

                    $newensemble = new Ensemble();
                    $newensemble->setPid($this->settings['records']['tn']);
                    $newensemble->setEnconf($step1data->getEnconf());
                    $newensemble->setEntn($step1data->getEntn());
                    $newensemble->setEnfirstn($enfirstnArr[$key]);
                    $newensemble->setEnlastn($enlastnArr[$key]);
                    $newensemble->setEninstru($eninstruArr[$key]);

                    $date = \DateTime::createFromFormat('d.m.Y', $engebdateArr[$key]);
                    if ($date) {
                        $engebdateArr[$key] = $date->format('Y-m-d');
                    }

                    $newensemble->setEngebdate($engebdateArr[$key]);
                    $newensemble->setEnnatio($ennatioArr[$key]);

                    $date = \DateTime::createFromFormat('d.m.Y', $step1data->getEngrdate());
                    if ($date) {
                        $step1data->setEngrdate($date->format('Y-m-d'));
                    }

                    $newensemble->setEngrdate($step1data->getEngrdate());
                    $newensemble->setEnname($step1data->getEnname());
                    $newensemble->setEntype($step1data->getEntype());
                    $newensemble->setEngrplace($step1data->getEngrplace());
                    $newKursanmeldung->addEnsemble($newensemble);
                }
            }


            if (!empty($step2data->getDownload())) {
                $downloads = $step2data->getDownload();
                if (!empty($downloads)) {
                    foreach ($downloads as $fileReference) {
                        if (!empty($fileReference)) {
                            $newDl = new Uploads();
                            $newDl->setPid($this->settings['records']['tn']);
                            $newDl->setKurs($kurs);
                            $newDl->setKat('download');
                            $newDl->setName($fileReference->getOriginalResource()?->getName() ?? '');
                            $newDl->setPfad($fileReference->getOriginalResource()?->getIdentifier() ?? '');
                            $newDl->setDatein(new \DateTime('NOW'));
                            $newDl->setFileref($fileReference);
                            $newKursanmeldung->addUploads($newDl);
                        }
                    }
                }
            }

            if (!empty($step2data->getYoutube())) {
                $src = trim($step2data->getYoutube());
                if (!empty($src)) {
                    $srcBn = pathinfo($src);
                    $newDl = new Uploads();
                    $newDl->setPid($this->settings['records']['tn']);
                    $newDl->setKurs($kurs);
                    $newDl->setKat('youtube');
                    $newDl->setName($srcBn['basename']);
                    $newDl->setPfad($src);
                    $newDl->setDatein(new \DateTime('NOW'));
                    $newKursanmeldung->addUploads($newDl);
                }
            }

            if (!empty($step2data->getVita())) {
                $fileReference = $step2data->getVita();
                if (!empty($fileReference->getOriginalResource())) {
                    $newDl = new Uploads();
                    $newDl->setPid($this->settings['records']['tn']);
                    $newDl->setKurs($kurs);
                    $newDl->setKat('vita');
                    $newDl->setName($fileReference->getOriginalResource()?->getName() ?? '');
                    $newDl->setPfad($fileReference->getOriginalResource()?->getIdentifier() ?? '');
                    $newDl->setDatein(new \DateTime('NOW'));
                    $newDl->setFileref($fileReference);
                    $newKursanmeldung->addUploads($newDl);
                }
            }

            if (!empty($step2data->getLink())) {
                $src = trim($step2data->getLink());
                $srcBn = pathinfo($src);
                $newDl = new Uploads();
                $newDl->setPid($this->settings['records']['tn']);
                $newDl->setKurs($kurs);
                $newDl->setKat('link');
                $newDl->setName($srcBn['basename']);
                $newDl->setPfad($src);
                $newDl->setDatein(new \DateTime('NOW'));
                $newKursanmeldung->addUploads($newDl);
            }

            $newKursanmeldung->setSavedata((int)$step3data->getSavedata());

            $hash = $this->participantUtility->getHashedPasswordFromPassword($newTn->getEmail());
            $newKursanmeldung->setRegistrationkey($hash);
            $this->kursanmeldungRepository->add($newKursanmeldung);
            $this->persistenceManager->persistAll();

            if ($newKursanmeldung->getUid() > 0) {
                if (!$this->sessionUtility->getData(SessionUtility::FORM_SESSION_SEND_MAIL)) {
                    $this->sendInfoMail($newTn, $newKursanmeldung);
                    $this->sessionUtility->setData(SessionUtility::FORM_SESSION_SEND_MAIL, $newKursanmeldung->getUid());
                }
                // emails versenden
                switch ($newKursanmeldung->getZahlart()) {
                    case 1:
                        $payment = 'banktransfer';
                        $this->logger->info('banktransfer suc:');

                        $banktransfer = $this->getBanktransferData($newKursanmeldung);
                        $this->logger->info('banktransfer suc POST:' . print_r($banktransfer, true));

                        $this->kursanmeldungRepository->update($newKursanmeldung);
                        $this->persistenceManager->persistAll();

                        // emails versenden
                        $this->sendInvoiceMail($newKursanmeldung, $newTn, $banktransfer);
                        $this->sendRegisterMail($newKursanmeldung, $newTn);
                        $this->sessionUtility->setData(
                            SessionUtility::FORM_SESSION_SEND_MAIL,
                            $newKursanmeldung->getUid()
                        );
                        $this->sessionUtility->cleanSession($this->getUser());
                        break;
                    default:
                        $this->sendRegisterMail($newKursanmeldung, $newTn);
                        $this->sessionUtility->cleanSession($this->getUser());
                }
            } else {
                // fehler redirect btstep1
                $this->addFlashMessage(
                    $this->participantUtility->translateFromXlf('tx_kursanmeldung_domain_model_type.err001_body'),
                    $this->participantUtility->translateFromXlf('tx_kursanmeldung_domain_model_type.err001_title'),
                    ContextualFeedbackSeverity::ERROR
                );

                return $this->redirect(Constants::ACTION_STEP_1);
            }
        } else {
            // fehler redirect btstep1
            $this->addFlashMessage(
                $this->participantUtility->translateFromXlf('tx_kursanmeldung_domain_model_type.err004_body'),
                $this->participantUtility->translateFromXlf('tx_kursanmeldung_domain_model_type.err004_title'),
                ContextualFeedbackSeverity::ERROR
            );

            return $this->redirect(Constants::ACTION_STEP_1);
        }

        $kursname = $this->participantUtility->getKursname($kurs);

        $this->view->assign('kursname', $kursname);
        $this->view->assign(Constants::KURS, $kurs);

        return $this->htmlResponse();
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function step5novalnetAction(): ResponseInterface
    {
        $this->sessionUtility->setFrontendUser($this->getUser());

        $kursanmeldungUid = 0;
        if ($this->sessionUtility->getData(SessionUtility::FORM_SESSION_ANMELDUNG_UID)) {
            $kursanmeldungUid = $this->sessionUtility->getData(SessionUtility::FORM_SESSION_ANMELDUNG_UID);
        }

        // if payment error or not send
        $kursAnmeldung = $this->kursanmeldungRepository->findByUid($kursanmeldungUid);

        if ($kursAnmeldung && $kursAnmeldung->getUid() > 0) {
            $kursname = $this->participantUtility->getKursname($kursAnmeldung->getKurs());

            $this->view->assign('kursname', $kursname);
            $this->view->assign(Constants::KURS, $kursAnmeldung->getKurs());

            // emails versenden
            $address = $kursAnmeldung->getTn()->current();
            $this->sessionUtility->cleanSession($this->getUser());

            return $this->htmlResponse();
        } else {
            // fehler redirect btstep1
            $this->addFlashMessage(
                $this->participantUtility->translateFromXlf('tx_kursanmeldung_domain_model_type.err001_body'),
                $this->participantUtility->translateFromXlf('tx_kursanmeldung_domain_model_type.err001_title'),
                ContextualFeedbackSeverity::ERROR
            );

            return $this->redirect(Constants::ACTION_STEP_1);
        }
    }


    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function paylaterAction(): ResponseInterface
    {
        $this->sessionUtility->setFrontendUser($this->getUser());
        $pl = null; // typ der bei Auswahl Erstanmeldung oder Aktivengebühr abrechnet
        $p = null;
        $error = [];
        $kursActive = null;
        $registration = null;
        $opt = [];

        if ($this->request->hasArgument('p')) {
            $p = $this->request->getArgument('p');
        }

        if ($this->sessionUtility->getData(SessionUtility::FORM_SESSION_PL)) {
            $pl = $this->sessionUtility->getData(SessionUtility::FORM_SESSION_PL);
        }

        if ($this->request->hasArgument('pl')) {
            $pl = $this->request->getArgument('pl');
            $this->sessionUtility->setData(SessionUtility::FORM_SESSION_PL, $pl);
        }

        // wenn hash übergeben
        if ($this->request->hasArgument('hash')) {
            $this->sessionUtility->setData(SessionUtility::FORM_SESSION_KURS_UID, '');
            $args = $this->request->getArguments();
            $registration = $this->getRegistrationByHashAndSt($args['hash'], $args['st']);
        }

        // wenn aus session
        if ($this->sessionUtility->getData(SessionUtility::FORM_SESSION_KURS_UID)) {
            $kursuid = $this->sessionUtility->getData(SessionUtility::FORM_SESSION_KURS_UID);
            $registration = $this->kursanmeldungRepository->findByUid($kursuid);
        }

        // formular erstellen
        if ($registration !== null) {
            $zahlungsartArr = [];

            if (isset($this->settings['payment']['paypal'])) {
                $zahlungsartArr[3] = 'paypal';
            }
            if (isset($this->settings['payment']['onlinetransfer'])) {
                $zahlungsartArr[4] = 'onlinetransfer';
            }
            $zahlart = (isset($args['zahlart']) && isset($zahlungsartArr[$args['zahlart']])) ? $args['zahlart'] : '';

            $select = array(
                'payment' => $this->participantUtility->getOptions(
                    $zahlungsartArr,
                    'tx_kursanmeldung_domain_model_kursanmeldung.step2.'
                ),
                'act' => $zahlart
            );
            $this->view->assign('st', $args['st']);
            $this->view->assign('hash', $args['hash']);
            $this->view->assign('select', $select);

            if (!empty($zahlart)) {
                $this->view->assign('payment', $zahlungsartArr[$zahlart]);
            }

            $this->sessionUtility->setData(SessionUtility::FORM_SESSION_KURS_UID, $registration->getUid());

            if (count($registration->getKurs()) === 1) {
                $kursActive = $this->kursRepository->findByUid(
                    $registration->getKurs()->getUid()
                );
            }
            if ($kursActive === null) {
                array_push($error, 'no Kurs found');
            }

            $form = '';
            $payment = '';
            if (empty($error)) {
                /* Gebühr berechnen */
                $opt['geb'] = $registration->getGebuehrag();
                if ($pl != null && $pl == 'ang') {
                    $opt = array();
                }
                $formVars = $this->novalnetArray($registration, $opt);
                // payment anstoßen 1=>'banktransfer', 2=>'prepayment', 3=>'paypal', 4=>'onlinetransfer', 5=>'giropay', 6=>'invoice'
                switch ($zahlart) {
                    case 3:
                        $payment = 'paypal';
                        $formVars['invoice']['payment'] = 34;
                        $novalnetXML[3]['url'] = 'https://payport.novalnet.de/paypal_payport';
                        $novalnetXML[3]['vars'] = $formVars;
                        $form = $novalnetXML[3];
                        break;
                    case 4:
                        $payment = 'onlinetransfer';
                        $formVars['invoice']['payment'] = 33;
                        $novalnetXML[2]['url'] = 'https://payport.novalnet.de/online_transfer_payport';
                        $novalnetXML[2]['vars'] = $formVars;
                        $form = $novalnetXML[2];
                        break;
                    case 5:
                        $payment = 'giropay';
                        $formVars['invoice']['payment'] = 69;
                        $novalnetXML[2]['url'] = 'https://payport.novalnet.de/giropay';
                        $novalnetXML[2]['vars'] = $formVars;
                        $form = $novalnetXML[2];
                        break;
                }
            } else {
                // fehler redirect btstep1
                $p = 'err';
            }

            $site = $this->request->getAttribute('site');
            $baseUrl = (string)$site->getBase();

            $this->view->assign('baseURL', $baseUrl);
            $this->view->assign('returnUrlSuccess', $this->getStep4RedirectUrl('suc'));
            $this->view->assign('returnUrlError', $this->getStep4RedirectUrl('err'));
            $this->view->assign('payment', $payment);
            $this->view->assign('form', $form);
        }

        $this->view->assign(Constants::KURS, $kursActive);
        $this->view->assign('pl', $pl);
        $this->view->assign('p', $p);

        return $this->htmlResponse();
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    public function doiconfirmAction(): ResponseInterface
    {
        // confirmed by timestamp_uid and hash
        $hash = '';
        $ts = '';
        $id = '';

        if ($this->request->hasArgument('hash')) {
            $hash = base64_decode($this->request->getArgument('hash'));
        }

        if ($this->request->hasArgument('st')) {
            $st = $this->request->getArgument('st');
            $stArr = explode('_', $st);
            if (count($stArr) === 2) {
                $ts = intval($stArr[0]);
                $id = intval($stArr[1]);
            }
        }
        // go on if values filled
        if (!empty($hash) && !empty($ts) && !empty($id)) {
            $this->kursanmeldungRepository->setStoragePageIds([$this->settings['records']['tn']]);
            $regTup = $this->kursanmeldungRepository->getRegistration($hash, $id, $ts);
            if ($regTup->count() === 1) {
                $register = $regTup->current();
                $register->setDoitime(new \DateTime('NOW'));
                $this->kursanmeldungRepository->update($register);
            } else {
                $this->view->assign('error', 1);
            }
        } else {
            $this->view->assign('error', 1);
        }

        return $this->htmlResponse();
    }


    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function closeAction(): ResponseInterface
    {
        $this->sessionUtility->cleanSession($this->getUser());

        return $this->redirect(Constants::ACTION_KURS_WAHL);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function zahlartAction(): ResponseInterface
    {
        $this->sessionUtility->setFrontendUser($this->getUser());

        if ($this->sessionUtility->getData(SessionUtility::FORM_SESSION_STEP2_DATA)) {
            $step2data = $this->sessionUtility->getData(SessionUtility::FORM_SESSION_STEP2_DATA);
            $zahlart = $step2data->getZahlungsart();
        }

        if ($this->request->hasArgument('zahlart')) {
            $zahlart = intval($this->request->getArgument('zahlart'));
        }

        if (isset($step2data) && !empty($step2data)) {
            $step2data->setZahlungsart($zahlart);
            $this->sessionUtility->setData(SessionUtility::FORM_SESSION_STEP2_DATA, $step2data);
        }

        return $this->redirect(Constants::ACTION_STEP_4);
    }

    /**
     * @return \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication
     */
    protected function getUser(): FrontendUserAuthentication
    {
        return $this->request->getAttribute('frontend.user');
    }

    /**
     * @return void
     */
    protected function forceHeader(): void
    {
        // force javascript back / browser back to relaod page
        // attention by flush cache events
        header('Pragma: no-cache');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Kursanmeldung $competition
     * @param int $type
     * @return string
     */
    protected function paymentReason(Kursanmeldung $competition, int $type = 0): string
    {
        $paymentReason = '';
        switch ($type) {
            case 1:
                $uid = $competition->getUid();
                $ensName = '';
                $compEnsem = $competition->getEnsemble();
                if ($compEnsem->count() > 0) {
                    $compEnsem->rewind();
                    $ensName = $compEnsem->current()->getEnname();
                }
                $paymentReason = trim($uid . ' ' . $ensName);
                break;
            default:
                $uid = $competition->getUid();
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

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Kursanmeldung $competition
     * @return array
     */
    protected function getBanktransferData(Kursanmeldung $competition): array
    {
        // get invoice data from setup
        $banktransfer['tid'] = $this->paymentReason($competition);

        $banktransfer['invoice_account_name'] = $this->participantUtility->translateFromXlf(
            'tx_kursanmeldung.complete.invoicemail.invoice_account_name'
        );
        if (isset($this->setup['invoicedata_accountname']) && !empty($this->setup['invoicedata_accountname'])) {
            $banktransfer['invoice_account_name'] = $this->setup['invoicedata_accountname'];
        }

        $banktransfer['invoice_bankcode'] = $this->participantUtility->translateFromXlf(
            'tx_kursanmeldung.complete.invoicemail.invoice_bankcode'
        );

        $banktransfer['invoice_iban'] = $this->participantUtility->translateFromXlf(
            'tx_kursanmeldung.complete.invoicemail.invoice_iban'
        );
        if (isset($this->setup['invoicedata_iban']) && !empty($this->setup['invoicedata_iban'])) {
            $banktransfer['invoice_iban'] = $this->setup['invoicedata_iban'];
        }

        $banktransfer['invoice_bic'] = $this->participantUtility->translateFromXlf(
            'tx_kursanmeldung.complete.invoicemail.invoice_bic'
        );
        if (isset($this->setup['invoicedata_bic']) && !empty($this->setup['invoicedata_bic'])) {
            $banktransfer['invoice_bic'] = $this->setup['invoicedata_bic'];
        }

        $banktransfer['invoice_bankname'] = $this->participantUtility->translateFromXlf(
            'tx_kursanmeldung.complete.invoicemail.invoice_bankname'
        );
        if (isset($this->setup['invoicedata_bic']) && !empty($this->setup['invoicedata_bankname'])) {
            $banktransfer['invoice_bankname'] = $this->setup['invoicedata_bankname'];
        }

        $banktransfer['invoice_bankplace'] = $this->participantUtility->translateFromXlf(
            'tx_kursanmeldung.complete.invoicemail.invoice_bankplace'
        );
        if (isset($this->setup['invoicedata_bankplace']) && !empty($this->setup['invoicedata_bankplace'])) {
            $banktransfer['invoice_bankplace'] = $this->setup['invoicedata_bankplace'];
        }

        $banktransfer['invoicedata_event'] = $this->setup['invoicedata_event'];
        $banktransfer['invoicedata_date'] = $this->setup['invoicedata_date'];

        $banktransfer['invoicedata_text1'] = $this->participantUtility->translateFromXlf(
            'tx_kursanmeldung.complete.invoicemail.invoice_text1'
        );
        if (isset($this->setup['invoicedata_text1']) && !empty($this->setup['invoicedata_text1'])) {
            $banktransfer['invoicedata_text1'] = $this->setup['invoicedata_text1'];
        }

        $banktransfer['invoicedata_text2'] = $this->participantUtility->translateFromXlf(
            'tx_kursanmeldung.complete.invoicemail.invoice_text2'
        );
        if (isset($this->setup['invoicedata_text2']) && !empty($this->setup['invoicedata_text2'])) {
            $banktransfer['invoicedata_text2'] = $this->setup['invoicedata_text2'];
        }

        // depend on language
        $banktransfer['invoicedata_subjectuser'] = $this->setup['invoicedata_subjectuser'];
        $banktransfer['invoicedata_subjectadmin'] = $this->setup['invoicedata_subjectadmin'];
        if ($this->settings['syslang'] == 1) {
            if (isset($this->setup['invoicedata_subjectuser']) && !empty($this->setup['invoicedata_subjectuser'])) {
                $banktransfer['invoicedata_subjectuser'] = $this->setup['invoicedata_subjectuser_en'];
            }
            if (isset($this->setup['invoicedata_subjectadmin']) && !empty($this->setup['invoicedata_subjectadmin'])) {
                $banktransfer['invoicedata_subjectadmin'] = $this->setup['invoicedata_subjectadmin_en'];
            }
        }

        return $banktransfer;
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

    private function curl_xml_post($request): mixed
    {
        $logFile = Environment::getVarPath() . '/log/kursanmeldung.log';
        $ch = curl_init($request['url']);
        $f = fopen($logFile, 'w');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: close', 'Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_FILE, $f);
        curl_setopt(
            $ch,
            CURLOPT_POST,
            1
        ); // ein Parameterwert ungleich 0 läßt die Programmbibliothek einen normalen HTTP-Post-Aufruf durchführen
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request['doc']); // Hinzufügen von HTTP-POST-Feldern
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0); // läßt keine Umleitungen zu
        curl_setopt(
            $ch,
            CURLOPT_SSL_VERIFYHOST,
            false
        ); // Kommentieren Sie diese Zeile aus, wenn Sie eine effektive SSL-Überprüfung haben wollen.
        curl_setopt(
            $ch,
            CURLOPT_SSL_VERIFYPEER,
            false
        ); // Kommentieren Sie diese Zeile aus, wenn Sie eine effektive SSL-Überprüfung haben wollen.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // geben Sie die Rückmeldung in einer Variable zurück
        curl_setopt(
            $ch,
            CURLOPT_TIMEOUT,
            240
        ); // maximale Zeit in Sekunden welche die cURL-Funktionen für die Ausführung benötigen dürfen.
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_STDERR, $f);
        ## Herstellung einer Verbindung
        $xml_response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        ## Prüfung, ob es bei der Ausführung von cURL zu Problemen kam
        $errno = curl_errno($ch);
        $errmsg = curl_error($ch);
        curl_close($ch);

        return $xml_response;
    }

    public function encode($data, $password)
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
    function hash1($h, $key)
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

    public function encodeParams($auth_code, $product_id, $tariff_id, $amount, $test_mode, $uniqid, $password)
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
     * @param string $hash
     * @param string $st
     * @return \Hfm\Kursanmeldung\Domain\Model\Kursanmeldung|null
     */
    protected function getRegistrationByHashAndSt(string $hash = '', string $st = ''): ?Kursanmeldung
    {
        $ts = '';
        $id = '';
        $register = null;
        $stArr = explode('_', $st);

        if (count($stArr) === 2) {
            $ts = intval($stArr[0]);
            $id = intval($stArr[1]);
        }
        // go on if values filled
        if (!empty($hash) && !empty($ts) && !empty($id)) {
            $regTup = $this->kursanmeldungRepository->getRegistration($hash, $id, $ts);
            if ($regTup->count() === 1) {
                $register = $regTup->current();
            }
        }

        return $register;
    }

    private function getLinkByRegistration(Kursanmeldung $registration): string
    {
        $this->uriBuilder->setRequest($this->request);

        $url = $this->uriBuilder
            ->reset()
            ->setCreateAbsoluteUri(true)
            ->setNoCache(true)
            ->uriFor(
                'doiconfirm', // only action name, not `myAction`
                [
                    'st' => $registration->getDatein()->getTimestamp() . '_' . $registration->getUid(),
                    'hash' => base64_encode($registration->getRegistrationkey())
                ],
                'Frontend', // only controller name, not `MyController`
                'kursanmeldung',
                'KursanmeldungFe',
            );

        return $url;
    }

    private function getStep4RedirectUrl(string $parameter): string
    {
        $this->uriBuilder->setRequest($this->request);

        $url = $this->uriBuilder
            ->reset()
            ->setCreateAbsoluteUri(true)
            ->setNoCache(true)
            ->uriFor(
                'step4redirect', // only action name, not `myAction`
                [
                    'p' => $parameter,
                ],
                'Frontend', // only controller name, not `MyController`
                'kursanmeldung',
                'KursanmeldungFe',
            );

        return $url;
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Teilnehmer $newTn
     * @param \Hfm\Kursanmeldung\Domain\Model\Kursanmeldung|null $newKursanmeldung
     * @return void
     */
    public function sendInfoMail(Teilnehmer $newTn, ?Kursanmeldung $newKursanmeldung): void
    {
        // TeilnehmerEmail
        $mailDto = new MailDto();
        $mailDto->setSendTo($newTn->getEmail());
        $mailDto->setSendFrom(new Address($this->emailHostAddress, $this->emailHostName));
        $mailDto->setSubject($this->emailSubjectInfo);
        $mailDto->setPageUid($this->infoMailId);
        $mailDto->setRequest($this->request);
        $mailDto->setTemplate('RegistrationUserInfoHtml');
        $mailDto->setFormat(FluidEmail::FORMAT_HTML);
        $mailDto->setKursanmeldung($newKursanmeldung);
        $mailDto->setAssignments($this->participantUtility->getFluidAssignments($newKursanmeldung));
        $this->mailFacade->sendFluidMailWithPageContent($mailDto);

        //AdminEmail
        $mailDto = new MailDto();
        $mailDto->setSendTo($this->emailHostAddress);
        $mailDto->setSendFrom(new Address($newTn->getEmail(), ucfirst($newTn->getVorname()) . ' ' . ucfirst($newTn->getNachname())));
        $mailDto->setSubject($this->emailSubjectAdmin);
        $mailDto->setPageUid($this->adminMailId);
        $mailDto->setRequest($this->request);
        $mailDto->setTemplate('RegistrationAdminHtml');
        $mailDto->setFormat(FluidEmail::FORMAT_HTML);
        $mailDto->setKursanmeldung($newKursanmeldung);
        $mailDto->setAssignments($this->participantUtility->getFluidAssignments($newKursanmeldung));
        $this->mailFacade->sendFluidMailWithPageContent($mailDto);
    }

    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Kursanmeldung $newKursanmeldung
     * @param \Hfm\Kursanmeldung\Domain\Model\Teilnehmer $newTn
     * @param array $banktransfer
     * @return void
     */
    private function sendInvoiceMail(Kursanmeldung $newKursanmeldung, Teilnehmer $newTn, array $banktransfer): void
    {
        $assignments = $this->participantUtility->getFluidAssignments($newKursanmeldung);
        $assignments['kurs'] = $this->nameVeranstaltung .'<br />'.$this->participantUtility->getProfInstrument($newKursanmeldung->getKurs());
        $assignments['novalnet'] = $banktransfer;
        $assignments['embedLogo'] = GeneralUtility::getFileAbsFileName(
            'EXT:kursanmeldung/Resources/Public/Images/logo_wba_112x25px.png'
        );

        // TeilnehmerEmail
        $mailDto = new MailDto();
        $mailDto->setSendTo($newTn->getEmail());
        $mailDto->setSendFrom(new Address($this->emailHostAddress, $this->emailHostName));
        $mailDto->setSubject($this->emailSubjectInvoice);
        $mailDto->setPageUid($this->infoMailId);
        $mailDto->setRequest($this->request);
        $mailDto->setTemplate('InvoiceHtml');
        $mailDto->setFormat(FluidEmail::FORMAT_HTML);
        $mailDto->setKursanmeldung($newKursanmeldung);
        $mailDto->setAssignments($assignments);
        $this->mailFacade->sendFluidMailWithPageContent($mailDto);

        //AdminEmail
        $mailDto = new MailDto();
        $mailDto->setSendTo($this->emailHostAddress);
        $mailDto->setSendFrom(new Address($newTn->getEmail(), ucfirst($newTn->getVorname()) . ' ' . ucfirst($newTn->getNachname())));
        $mailDto->setSubject('Kursanmeldung Administrator Rechnung');
        $mailDto->setPageUid($this->infoMailId);
        $mailDto->setRequest($this->request);
        $mailDto->setTemplate('InvoiceHtml');
        $mailDto->setFormat(FluidEmail::FORMAT_HTML);
        $mailDto->setKursanmeldung($newKursanmeldung);
        $mailDto->setAssignments($assignments);
        $this->mailFacade->sendFluidMailWithPageContent($mailDto);
    }


    /**
     * @param \Hfm\Kursanmeldung\Domain\Model\Kursanmeldung $newKursanmeldung
     * @param \Hfm\Kursanmeldung\Domain\Model\Teilnehmer $newTn
     * @param array $banktransfer
     * @return void
     */
    private function sendRegisterMail(Kursanmeldung $newKursanmeldung, Teilnehmer $newTn): void
    {
        $assignments = $this->participantUtility->getFluidAssignments($newKursanmeldung);
        $assignments['kurs'] = $this->nameVeranstaltung .'<br />'.$this->participantUtility->getProfInstrument($newKursanmeldung->getKurs());
        $assignments['link'] = $this->getLinkByRegistration($newKursanmeldung);

        // TeilnehmerEmail
        $mailDto = new MailDto();
        $mailDto->setSendTo($newTn->getEmail());
        $mailDto->setSendFrom(new Address($this->emailHostAddress, $this->emailHostName));
        $mailDto->setSubject($this->emailSubject);
        $mailDto->setPageUid($this->userMailId);
        $mailDto->setRequest($this->request);
        $mailDto->setTemplate('RegistrationUserHtml');
        $mailDto->setFormat(FluidEmail::FORMAT_HTML);
        $mailDto->setKursanmeldung($newKursanmeldung);
        $mailDto->setAssignments($assignments);
        $this->mailFacade->sendFluidMailWithPageContent($mailDto);

        //AdminEmail
        $mailDto = new MailDto();
        $mailDto->setSendTo($this->emailHostAddress);
        $mailDto->setSendFrom(new Address($newTn->getEmail(), ucfirst($newTn->getVorname()) . ' ' . ucfirst($newTn->getNachname())));
        $mailDto->setSubject($this->emailSubjectAdmin);
        $mailDto->setPageUid($this->adminMailId);
        $mailDto->setRequest($this->request);
        $mailDto->setTemplate('RegistrationAdminHtml');
        $mailDto->setFormat(FluidEmail::FORMAT_HTML);
        $mailDto->setKursanmeldung($newKursanmeldung);
        $mailDto->setAssignments($assignments);
        $this->mailFacade->sendFluidMailWithPageContent($mailDto);
    }
}

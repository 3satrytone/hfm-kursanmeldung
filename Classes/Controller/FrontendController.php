<?php

namespace Hfm\Kursanmeldung\Controller;


use Hfm\Kursanmeldung\App\Dto\StepDataParticipantDto;
use Hfm\Kursanmeldung\App\Participant\Business\ParticipantFacade;
use Hfm\Kursanmeldung\Constants\Constants;
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
use TYPO3\CMS\Extbase\Annotation\IgnoreValidation;
use TYPO3\CMS\Extbase\Annotation\Validate;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
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
        //$this->sessionUtility->cleanSession($this->getUser());
        $this->kursRepository->setStoragePageIds([$this->settings['records']['kurs']]);
        $kurse = $this->kursRepository->findAll();
        $kurseActive = array();
        $tnStatus = array();

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
                $this->redirect(Constants::ACTION_KURS_WAHL);
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
            $this->redirect(Constants::ACTION_KURS_WAHL);
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
        $this->view->assign('kurs', $kurs);
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
        if (!$this->sessionUtility->isCompletedRegistration()) {
            $this->redirect(Constants::ACTION_KURS_WAHL);
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
        $hotel = $this->hotelRepository->findByUid($step2data->getHotel());
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

        // wenn studentship != NULL alles 0
        if (!$step2data->getStudentship() || $gebuehr === null) {
            $enrollmentfee = ($step2data->getTnaction() === 1) ? $gebuehr->getPassivgeb() : $gebuehr->getAnmeldung();
            $additionalfee = ($step2data->getTnaction() === 1) ? 0 : $gebuehr->getAktivengeb();
            // wenn studystat != NULL halber preis
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

        $this->view->assign('kurs', $kurs);
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
                )) {
                $this->redirect('step5');
            }
        }

        $kursanmeldungUid = 0;
        if (!empty($this->sessionUtility->getData(SessionUtility::FORM_SESSION_KURS_UID))) {
            $kursanmeldungUid = $this->sessionUtility->getData(SessionUtility::FORM_SESSION_KURS_UID);
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
        $newKursanmeldung = null;
        $addTN = false;

        if ($kursanmeldungUid === 0) {
            $newKursanmeldung = new Kursanmeldung();
            $newTn = new Teilnehmer();
            $addTN = true;
        } else {
            $newKursanmeldung = $this->kursanmeldungRepository->findByUid($kursanmeldungUid);
            $newTn = $newKursanmeldung->getTn()->current();
            if (empty($newTn)) {
                $newTn = new Teilnehmer();
                $addTN = true;
            }
        }
        /* Gebühr berechnen */
        $gebuehr = $this->gebuehrenRepository->findByUid($kurs->getGebuehr());
        $enrollmentfee = 0;

        // wenn studentship != NULL alles 0
        if (!$step2data->getStudentship()) {
            $enrollmentfee = ($step2data->getTnaction() === 1) ? $gebuehr->getPassivgeb() : $gebuehr->getAnmeldung();
            // wenn studystat != NULL halber preis
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
        $newKursanmeldung->setStudystat($step2data->getStudystat());
        $newKursanmeldung->setZahlart($step2data->getZahlungsart());
        $newKursanmeldung->setZahltbis($zahlungstermin);
        $newKursanmeldung->setHotel($step2data->getHotel());
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

        if (!empty($step2data->getDownload())) {
            $downloads = $step2data->getDownload();
            if (!empty($downloads)) {
                foreach ($downloads as $fileReference) {
                    if (!empty($fileReference)) {
                        $newDl = new Uploads();
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
            $src = $step2data->getYoutube();
            if (!empty($src)) {
                $srcBn = pathinfo($src);
                $newDl = new Uploads();
                $newDl->setKurs($kurs);
                $newDl->setKat('youtube');
                $newDl->setName($srcBn['basename']);
                $newDl->setPfad($srcBn['dirname']);
                $newDl->setDatein(new \DateTime('NOW'));
                $newKursanmeldung->addUploads($newDl);
            }
        }

        if (!empty($step2data->getVita())) {
            $fileReference = $step2data->getVita();
            if (!empty($fileReference->getOriginalResource())) {
                $newDl = new Uploads();
                $newDl->setKurs($kurs);
                $newDl->setKat('vita');
                $newDl->setName($fileReference->getOriginalResource()?->getName() ?? '');
                $newDl->setPfad($fileReference->getOriginalResource()?->getIdentifier() ?? '');
                $newDl->setDatein(new \DateTime('NOW'));
                $newDl->setFileref($fileReference);
                $newKursanmeldung->addUploads($newDl);
            }
        }
        $newKursanmeldung->setSavedata($step3data->getSavedata());
        if ($kursanmeldungUid === 0) {
            $hash = $this->participantUtility->getHashedPasswordFromPassword($newTn->getEmail());
            $newKursanmeldung->setRegistrationkey($hash);

            $this->kursanmeldungRepository->add($newKursanmeldung);
        } else {
            $this->kursanmeldungRepository->update($newKursanmeldung);
        }

        if ($newKursanmeldung->getUid() > 0) {
        }

        $site = $this->request->getAttribute('site');
        $baseUrl = (string)$site->getBase();

        $this->view->assign('baseURL', $baseUrl);
        $this->view->assign('newKursanmeldung', $newKursanmeldung);
        $this->view->assign('payment', $payment);
        $this->view->assign('p', $p);
        $this->view->assign('form', $form);
        $this->view->assign('select', $select);

        return $this->htmlResponse();
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
}

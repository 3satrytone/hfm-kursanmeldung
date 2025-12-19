<?php

namespace Hfm\Kursanmeldung\Controller;


use Hfm\Kursanmeldung\Domain\Model\Step1Data;
use Hfm\Kursanmeldung\Domain\Repository\KursanmeldungRepository;
use Hfm\Kursanmeldung\Domain\Repository\KursRepository;
use Hfm\Kursanmeldung\Domain\Repository\ProfRepository;
use Hfm\Kursanmeldung\Utility\ParticipantUtility;
use Hfm\Kursanmeldung\Utility\SessionUtility;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class FrontendController extends ActionController
{

    protected $zahlungsartArr = array(
        9 => 'standard',
    );
    protected $zahlungsartNovalnetArr = array(3 => 'paypal', 4 => 'onlinetransfer', 5 => 'giropay', 6 => 'invoice');
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
        protected readonly SessionUtility $sessionUtility,
        protected readonly ParticipantUtility $participantUtility,
    ) {
    }

    public function initializeAction(): void
    {
        $this->zahlungsartArr = array();
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
        $this->cleanSession();
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
                    if(isset($activePassiveTn['aktiveTn']) && $activePassiveTn['aktiveTn'] < 1) {
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
    public function step1Action(?Step1Data $step1data): ResponseInterface
    {
        return $this->htmlResponse();
    }

    /**
     * clean session from user input
     *
     * @return void
     */
    protected function cleanSession(): void
    {
        $this->sessionUtility->setFrontendUser($this->request->getAttribute('frontend.user'));
        $this->sessionUtility->setData($this->sessionUtility::FORM_SESSION_STEP1_DATA, '');
        $this->sessionUtility->setData($this->sessionUtility::FORM_SESSION_STEP2_DATA, '');
        $this->sessionUtility->setData($this->sessionUtility::FORM_SESSION_STEP3_DATA, '');
        $this->sessionUtility->setData($this->sessionUtility::FORM_SESSION_STEP4_DATA, '');
        $this->sessionUtility->setData($this->sessionUtility::FORM_SESSION_PL, '');
        $this->sessionUtility->setData($this->sessionUtility::FORM_SESSION_KURS, '');
        $this->sessionUtility->setData($this->sessionUtility::FORM_SESSION_KURS_UID, '');
        $this->sessionUtility->setData($this->sessionUtility::FORM_SESSION_SEND_MAIL, '');
    }
}

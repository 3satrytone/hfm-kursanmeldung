<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Controller;

use Hfm\Kursanmeldung\App\Mail\Business\MailFacade;
use Hfm\Kursanmeldung\Domain\Repository\AnmeldestatusRepository;
use Hfm\Kursanmeldung\Domain\Repository\KursanmeldungRepository;
use Hfm\Kursanmeldung\Domain\Repository\KursRepository;
use Hfm\Kursanmeldung\Domain\Repository\ProfRepository;
use Hfm\Kursanmeldung\Domain\Repository\ProfStatusRepository;
use Hfm\Kursanmeldung\Utility\ParticipantUtility;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Hfm\Kursanmeldung\Domain\Model\Mailhist;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

use Hfm\Kursanmeldung\App\Dto\MailDto;
use Symfony\Component\Mime\Address;

class MailingController extends ActionController
{
    protected $zahlungsartArr = array(1=>'banktransfer', 2=>'prepayment', 3=>'paypal', 4=>'onlinetransfer', 5=>'giropay', 6=>'invoice');
    protected $zahlungsartNovalnetArr = array(3=>'paypal', 4=>'onlinetransfer', 5=>'giropay', 6=>'invoice');
    protected $mailprename = 'Hochschule für Musik FRANZ LISZT Weimar';
    protected $mailpremail = 'meisterkurse@hfm-weimar.de';
    protected $adminmail = 'meisterkurse@hfm-weimar.de';
    protected $emailHostAddress = 'meisterkurse@hfm-weimar.de';
    protected $emailHostAddressAdmin = 'wiebke.eckardt@hfm-weimar.de';
    protected $emailHostAddressCc = 'info@schneider-software-service.de';
    protected $emailHostName = '';
    protected $emailSubject = 'Ihre Kursanmeldung bei der Hochschule für Musik, bitte bestätigen';
    protected $emailSubjectAdmin = 'Admin: Kursanmeldung bei der Hochschule für Musik';
    protected $emailSubjectInfo = 'Ihre Kursanmeldung bei der Hochschule für Musik';
    protected $emailSubjectInvoice = 'Ihre Kursanmeldung bei der Hochschule für Musik, bitte Rechnung begleichen';
    protected $nameVeranstaltung = 'Weimarer Meisterkurse';
    protected $anmeldeGebuehrPrefix = 'TG';

    public function __construct(
        protected readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly AnmeldestatusRepository $anmeldestatusRepository,
        private readonly KursanmeldungRepository $kursanmeldungRepository,
        protected readonly ProfRepository $profRepository,
        protected UriBuilder $uriBuilder,
        private readonly KursRepository $kursRepository,
        private readonly ProfStatusRepository $profStatusRepository,
        private readonly PersistenceManagerInterface $persistenceManager,
        private readonly ParticipantUtility $participantUtility,
        private readonly MailFacade $mailFacade,
    ) {
    }

    public function initializeAction(): void
    {
        if(isset($this->settings['dataPages'])){
            if(isset($this->kursanmeldungRepository))$this->kursanmeldungRepository->setStoragePageIds($this->settings['dataPages']);
            if(isset($this->anmeldestatusRepository))$this->anmeldestatusRepository->setStoragePageIds($this->settings['dataPages']);
        }
    }

    public function listAction(): ResponseInterface
    {
        $kursanmeldungen = $this->kursanmeldungRepository->findAll();
        if(!empty($kursanmeldungen)){
            foreach ($kursanmeldungen as $key => $value) {
                $kurs = $value->getKurs();
                if(!empty($kurs)){
                    $profname = '';
                    $prof = $this->profRepository->findByUid($kurs->getProfessor());
                    if(!empty($prof))$kurs->setProf($prof->getName());
                }
            }
        }

        $containsEmailPages = 1;
        if($this->settings != NULL && isset($this->settings['mailpagesparent'])){
            $containsEmailPages = $this->settings['mailpagesparent'];
        }
        $depth = 2;
        if($this->settings != NULL && isset($this->settings['depth'])){
            $depth = $this->settings['depth'];
        }
        $pageIds = $this->getPagesByTree($containsEmailPages,$depth);
        $mailTyps[] = array('uid'=>'Zulassung','title'=>'Zulassung');
        $mailTyps[] = array('uid'=>'ZulassungR','title'=>'Zulassung mit Rechnung');
        $mailTyps[] = array('uid'=>'Absage','title'=>'Absage');
        $mailTyps[] = array('uid'=>'Warteliste','title'=>'Warteliste');

        $statuus = $this->anmeldestatusRepository->findAll();

        $this->view->assign('statuus', $statuus);
        $this->view->assign('PageIds', $pageIds);
        $this->view->assign('mailTyps', $mailTyps);
        $this->view->assign('Kursanmeldungen', $kursanmeldungen);
        $this->view->assign('mailprename', $this->mailprename);
        $this->view->assign('mailpremail', $this->mailpremail);

        return $this->htmlResponse();
    }

    /**
     * @param string $subject
     * @param string $fromemail
     * @param string $fromname
     * @param array $sendmail (kursanmeldung uids)
     * @param string $sendmailto (additional emails)
     * @param string $pagemail (uid or "message")
     * @param string $mailtyp
     * @param string $pagemailinvoice
     * @param string $nachricht
     * @return ResponseInterface
     */
    public function sendmailAction(
        string $subject = '',
        string $fromemail = '',
        string $fromname = '',
        array $sendmail = [],
        string $sendmailto = '',
        string $pagemail = '',
        string $mailtyp = '',
        string $pagemailinvoice = '',
        string $nachricht = ''
    ): ResponseInterface {
        $fromAddress = new Address($fromemail, $fromname);
        $count = 0;
        $errorCount = 0;
        $doubleCount = 0;
        $sendList = [];
        $errorList = [];
        $doubleList = [];

        // Get additional email addresses
        $additionalEmails = [];
        if ($sendmailto !== '') {
            $additionalEmails = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $sendmailto, true);
        }
        if($this->adminmail){
            $additionalEmails[] = $this->adminmail;
        }

        // Process selected participants
        foreach ($sendmail as $uid) {
            /** @var \Hfm\Kursanmeldung\Domain\Model\Kursanmeldung $kursanmeldung */
            $kursanmeldung = $this->kursanmeldungRepository->findByUid((int)$uid);
            if ($kursanmeldung === null) {
                $errorCount++;
                continue;
            }

            $teilnehmer = $kursanmeldung->getTn()->toArray()[0] ?? null;
            if ($teilnehmer === null) {
                $errorCount++;
                continue;
            }

            if(in_array($teilnehmer->getEmail(), $doubleList)){
                $doubleCount++;
                continue;
            }

            $mailDto = new MailDto();
            $mailDto->setRequest($this->request);
            $mailDto->setSendFrom($fromAddress);
            $mailDto->setSendTo($teilnehmer->getEmail());
            $mailDto->setSubject($subject);
            $mailDto->setKursanmeldung($kursanmeldung);
            $mailDto->setFormat(FluidEmail::FORMAT_HTML);
            $assignments = $this->participantUtility->getFluidAssignments($kursanmeldung);
            $assignments = array_merge($assignments, [
                'htmlBody' => $nachricht,
                'nachricht' => $nachricht,
                'mailtyp' => $mailtyp,
                'pagemailinvoice' => $pagemailinvoice
            ]);

            if($mailtyp === 'ZulassungR'){
                $assignments['no'] = $this->anmeldeGebuehrPrefix . '.' . $assignments['no'];
            }

            $mailDto->setAssignments($assignments);

            try {
                $mailDto->setTemplate('MailingHtml');
                if ($pagemail === 'message') {// Or a specific template for messages
                    $this->mailFacade->sendFluidEmail($mailDto);
                } else {
                    $mailDto->setPageUid((int)$pagemail);
                    $this->mailFacade->sendFluidMailWithPageContent($mailDto);
                }

                $sendResponse = $mailDto->getSendResponse();
                if(isset($sendResponse['error'])){
                    $errorCount++;
                    $errorList[] = $uid;
                }

                $count++;
                $doubleList[] = $teilnehmer->getEmail();
                $sendList[] = $uid;

                // Send the exact same mail to additional email addresses (admins)
                foreach ($additionalEmails as $adminEmail) {
                    if (\TYPO3\CMS\Core\Utility\GeneralUtility::validEmail($adminEmail)) {
                        $adminMailDto = clone $mailDto;
                        $adminMailDto->setSendTo($adminEmail);
                        try {
                            if ($pagemail === 'message') {
                                $this->mailFacade->sendFluidEmail($adminMailDto);
                            } else {
                                $this->mailFacade->sendFluidMailWithPageContent($adminMailDto);
                            }
                            $count++;
                        } catch (\Exception $e) {
                            $errorCount++;
                            $errorList[] = $adminEmail . ' (for ' . $uid . ')';
                        }
                    }
                }

                if($mailtyp === 'ZulassungR'){
                    $assignments['amount'] = ($assignments['matrikel'] !== '') ? $assignments['aktivengeberm'] : $assignments['aktivengeb'];
                    $assignments['embedLogo'] = GeneralUtility::getFileAbsFileName(
                        'EXT:kursanmeldung/Resources/Public/Images/logo_wba_112x25px.png'
                    );
                    $mailDto->setAssignments($assignments);
                    $mailDto->setTemplate('InvoiceAktivengebuehrHtml');
                    $mailDto->setPageUid((int)$pagemailinvoice);
                    $this->mailFacade->sendFluidMailWithPageContent($mailDto);

                    if ($this->adminmail){
                        $adminMailDto = clone $mailDto;
                        $adminMailDto->setSendTo($this->adminmail);
                        $this->mailFacade->sendFluidMailWithPageContent($adminMailDto);
                    }
                }
            } catch (\Exception $e) {
                $errorCount++;
                $errorList[] = $uid;
            }
        }

        $this->addFlashMessage('Es wurden ' . $count . ' E-Mails versendet.');

        $this->view->assignMultiple([
            'send' => $sendList,
            'error' => $errorList,
            'double' => $doubleCount,
        ]);

        return $this->htmlResponse();
    }

    public function getPagesByTree($startingPoint = 1, $depth = 2): array
    {
        $pageArr = array();
        // Get page record for tree starting point
        $pageRecord = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecord(
            'pages',
            $startingPoint
        );
        // Create and initialize the tree object
        /** @var $tree \TYPO3\CMS\Backend\Tree\View\PageTreeView */
        $tree = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Backend\\Tree\\View\\PageTreeView');
        $tree->init('AND ' . $GLOBALS['BE_USER']->getPagePermsClause(1));

        // Create the page tree, from the starting point, 2 levels deep
        $tree->getTree(
            $startingPoint,
            $depth,
            ''
        );

        if(!empty($tree->tree)){
            foreach ($tree->tree as $key => $value) {
                $pageArr[$key]['uid'] = $value['row']['uid'];
                $pageArr[$key]['title'] = $value['row']['title'];
            }
        }

        return $pageArr;
    }

    public function showAction(Mailhist $mailhist): void
    {
        // Implement showing a single Mailhist record when templates are available.
        $this->view->assign('mailhist', $mailhist);
    }
}

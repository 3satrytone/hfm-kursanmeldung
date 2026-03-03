<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\App\Mail\Business\Mailer;

use Hfm\Kursanmeldung\App\Dto\MailDto;
use Hfm\Kursanmeldung\App\Mail\Business\Hydrator\MailBodyHydrator;
use Hfm\Kursanmeldung\App\Mail\Business\Reader\ContentReader;
use Hfm\Kursanmeldung\Domain\Model\Mailhist;
use Hfm\Kursanmeldung\Domain\Model\Mailhistrecipients;
use Hfm\Kursanmeldung\Domain\Repository\MailhistrecipientsRepository;
use Hfm\Kursanmeldung\Domain\Repository\MailhistRepository;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Mail\MailerInterface as TypoMailerInterface;

class FluidEmailMailer implements MailerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @param \Hfm\Kursanmeldung\App\Mail\Business\Reader\ContentReader $contentReader
     * @param \Hfm\Kursanmeldung\App\Mail\Business\Hydrator\MailBodyHydrator $mailBodyHydrator
     * @param \Hfm\Kursanmeldung\Domain\Repository\MailhistRepository $mailhistRepository
     * @param \Hfm\Kursanmeldung\Domain\Repository\MailhistrecipientsRepository $mailhistrecipientsRepository
     */
    public function __construct(
        private readonly ContentReader $contentReader,
        private readonly MailBodyHydrator $mailBodyHydrator,
        private readonly MailhistRepository $mailhistRepository,
        private readonly MailhistrecipientsRepository $mailhistrecipientsRepository,
    ) {
        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
    }

    /**
     * @param \Hfm\Kursanmeldung\App\Dto\MailDto $mailDto
     * @return void
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function send(MailDto $mailDto): void
    {
        try {
            $email = $this->setupMail($mailDto);

            GeneralUtility::makeInstance(TypoMailerInterface::class)->send($email);
            $this->persistMail($mailDto, $email->getHtmlBody());
        } catch (\Exception $e) {
            $this->logger?->error('FluidEmailMailer: ', ['message' => $e->getMessage(), 'trace' => $e->getTrace()]);
            $mailDto->setSendResponse(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param \Hfm\Kursanmeldung\App\Dto\MailDto $mailDto
     * @return void
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendWithPageContent(MailDto $mailDto): void
    {
        try {
            $htmlBody = $this->contentReader->getContentFromPid($mailDto->getPageUid(), $mailDto->getRequest());
            $htmlBody = $this->mailBodyHydrator->hydrate($htmlBody, $mailDto);

            $assignments = $mailDto->getAssignments();
            $assignments['htmlBody'] = $htmlBody;
            $assignments['txtBody'] = strip_tags($htmlBody);
            $mailDto->setAssignments($assignments);

            $email = $this->setupMail($mailDto);
            GeneralUtility::makeInstance(TypoMailerInterface::class)->send($email);
            $this->persistMail($mailDto, $htmlBody);
            $mailDto->setSendResponse(['success' => true]);
        } catch (\Exception $e) {
            $this->logger?->error('FluidEmailMailer: ', ['message' => $e->getMessage(), 'trace' => $e->getTrace()]);
            $mailDto->setSendResponse(['error' => $e->getMessage()]);
        }
    }

    private function persistMail(MailDto $mailDto, string $htmlBody): void
    {
        $mailhist = new Mailhist();
        $mailhist->setSubject($mailDto->getSubject());
        $mailhist->setSendername($mailDto->getSendFrom()->getName());
        $mailhist->setSendermail($mailDto->getSendFrom()->getAddress());
        $mailhist->setPageid((string)$mailDto->getPageUid());
        $mailhist->setMailtype((string)($mailDto->getAssignments()['mailtyp'] ?? ''));
        $mailhist->setNachricht($htmlBody);
        $mailhist->setRecipients(1);

        $this->mailhistRepository->add($mailhist);
        // We need to persist to get the UID for the recipient record
        $persistenceManager = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class);
        $persistenceManager->persistAll();

        $recipient = new Mailhistrecipients();
        $recipient->setMailuid($mailhist->getUid());
        $recipient->setRecipient($mailDto->getSendTo());
        $recipient->setDatesend(new \DateTime());
        if ($mailDto->getKursanmeldung() !== null) {
            $recipient->setRegid($mailDto->getKursanmeldung()->getUid());
        }

        $this->mailhistrecipientsRepository->add($recipient);
    }

    private function setupMail(MailDto $mailDto): FluidEmail
    {
        $email = new FluidEmail();
        $email
            ->to($mailDto->getSendTo())
            ->from($mailDto->getSendFrom())
            ->subject($mailDto->getSubject())
            ->format($mailDto->getFormat()) // send HTML and plaintext mail
            ->setTemplate($mailDto->getTemplate());

        if ($mailDto->getRequest()) {
            $email->setRequest($mailDto->getRequest());
        }

        if (!empty($mailDto->getAssignments())) {
            foreach ($mailDto->getAssignments() as $key => $value) {
                if ($key === 'embedLogo') {
                    if (is_string($value) && file_exists($value)) {
                        $email->embedFromPath($value, 'logo_wba_112x25px.png', 'image/png');
                        $email->assign('logoCid', 'cid:logo_wba_112x25px.png');
                    }
                }else{
                    $email->assign($key, $value);
                }
            }
        }

        return $email;
    }
}
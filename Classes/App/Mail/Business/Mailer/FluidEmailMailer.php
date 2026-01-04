<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\App\Mail\Business\Mailer;

use Hfm\Kursanmeldung\App\Dto\MailDto;
use Hfm\Kursanmeldung\App\Mail\Business\Hydrator\MailBodyHydrator;
use Hfm\Kursanmeldung\App\Mail\Business\Reader\ContentReader;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Mail\MailerInterface as TypoMailerInterface;

class FluidEmailMailer implements MailerInterface
{
    use LoggerAwareTrait;

    /**
     * @param \Hfm\Kursanmeldung\App\Mail\Business\Reader\ContentReader $contentReader
     * @param \Hfm\Kursanmeldung\App\Mail\Business\Hydrator\MailBodyHydrator $mailBodyHydrator
     */
    public function __construct(
        private readonly ContentReader $contentReader,
        private readonly MailBodyHydrator $mailBodyHydrator,
    ) {
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
        } catch (\Exception $e) {
            $this->logger->error('FluidEmailMailer: ', ['message' => $e->getMessage(), 'trace' => $e->getTrace()]);
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
        } catch (\Exception $e) {
            $this->logger->error('FluidEmailMailer: ', ['message' => $e->getMessage(), 'trace' => $e->getTrace()]);
        }
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
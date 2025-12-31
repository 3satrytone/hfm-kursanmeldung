<?php

namespace Hfm\Kursanmeldung\App\Mail\Business\Mailer;

use Hfm\Kursanmeldung\App\Dto\MailDto;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Mail\MailerInterface as TypoMailerInterface;

class FluidEmailMailer implements MailerInterface
{
    /**
     * @param \Hfm\Kursanmeldung\App\Dto\MailDto $mailDto
     * @return void
     */
    public function send(MailDto $mailDto): void
    {
        $email = new FluidEmail();
        $email
            ->to('steffenschneider.orig@web.de')
            ->from(new Address('meisterkurse@hfm-weimar.de', 'HfM WMK'))
            ->subject('HfM WMK Infomail')
            ->format(FluidEmail::FORMAT_BOTH) // send HTML and plaintext mail
            ->setTemplate('Info')
            ->assign('mySecretIngredient', 'Tomato and TypoScript');
        $email->setRequest($mailDto->getRequest());
        GeneralUtility::makeInstance(TypoMailerInterface::class)->send($email);
    }
}
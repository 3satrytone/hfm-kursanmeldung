<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Hfm\Kursanmeldung\Domain\Model\Mailhist;

class MailingController extends ActionController
{
    public function listAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }

    public function showAction(Mailhist $mailhist): void
    {
        // Implement showing a single Mailhist record when templates are available.
        $this->view->assign('mailhist', $mailhist);
    }
}

<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Hfm\Kursanmeldung\Domain\Model\Teilnehmer;

class TeilnehmerController extends ActionController
{
    public function listAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }

    public function showAction(Teilnehmer $teilnehmer): void
    {
        // Implement showing a single Teilnehmer record when templates are available.
        $this->view->assign('teilnehmer', $teilnehmer);
    }
}

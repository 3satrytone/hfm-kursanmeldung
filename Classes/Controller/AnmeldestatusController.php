<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Hfm\Kursanmeldung\Domain\Model\Anmeldestatus;

class AnmeldestatusController extends ActionController
{
    public function listAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }

    public function showAction(Anmeldestatus $anmeldestatus): void
    {
        // Implement showing a single Anmeldestatus record when templates are available.
        $this->view->assign('anmeldestatus', $anmeldestatus);
    }
}

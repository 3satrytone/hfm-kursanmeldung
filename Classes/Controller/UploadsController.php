<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Hfm\Kursanmeldung\Domain\Model\Uploads;

class UploadsController extends ActionController
{
    public function listAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }

    public function showAction(Uploads $uploads): void
    {
        // Implement showing a single Uploads record when templates are available.
        $this->view->assign('uploads', $uploads);
    }
}

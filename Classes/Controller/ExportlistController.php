<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class ExportlistController extends ActionController
{
    public function listAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }

    public function showAction(): void
    {
        // Implement showing a single Exportlist record when templates are available.
    }
}

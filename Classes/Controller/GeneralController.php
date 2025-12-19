<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class GeneralController extends ActionController
{
    public function indexAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }
}

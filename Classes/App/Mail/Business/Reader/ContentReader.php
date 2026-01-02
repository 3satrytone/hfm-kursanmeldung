<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\App\Mail\Business\Reader;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class ContentReader
{
    public int $defaultCol = 0;

    /**
     * @param int $pid
     * @param \TYPO3\CMS\Extbase\Mvc\RequestInterface $request
     * @return string
     */
    public function getContentFromPid(int $pid, RequestInterface $request): string
    {
        /** @var ContentObjectRenderer $cObj */
        $cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $cObj->setRequest($request);

        $contentConfig = [
            'table' => 'tt_content',
            'select.' => [
                'pidInList' => $pid,
                'orderBy' => 'sorting',
                'where' => 'colPos=' . $this->defaultCol,
            ],
        ];

        return str_replace("\n", '', $cObj->cObjGetSingle('CONTENT', $contentConfig));
    }
}
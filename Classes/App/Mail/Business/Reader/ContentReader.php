<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\App\Mail\Business\Reader;

use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class ContentReader
{
    public int $defaultCol = 0;
    public function __construct(
        protected readonly ModuleTemplateFactory $moduleTemplateFactory
    ) {}

    /**
     * @param int $pid
     * @param \TYPO3\CMS\Extbase\Mvc\RequestInterface $request
     * @return string
     */
    public function getContentFromPid(int $pid, RequestInterface $request): string
    {
        if(!$GLOBALS['TSFE'] ?? null) {
            $contentRecords = $this->getContentFromPage($pid);
            $bodytext = '';
            foreach ($contentRecords as $record) {
                $bodytext .= ($record['bodytext'] ?? '');
            }

            return str_replace("\n", '', $bodytext);
        }
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

    protected function getContentFromPage(int $pid): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');

        return $queryBuilder
            ->select('*')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid)),
                $queryBuilder->expr()->eq('sys_language_uid', 0), // Optional: Nur Standardsprache
                $queryBuilder->expr()->eq('hidden', 0),
                $queryBuilder->expr()->eq('deleted', 0)
            )
            ->orderBy('colPos')
            ->addOrderBy('sorting')
            ->executeQuery()
            ->fetchAllAssociative();
    }
}
<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;

class AnmeldestatusRepository extends Repository
{
    public function setStoragePageIds(array $storagePageIds)
    {
        $querySettings = $this->createQuery()->getQuerySettings();
        $querySettings->setRespectStoragePage(false);
        $querySettings->setStoragePageIds($storagePageIds);
        $this->setDefaultQuerySettings($querySettings);
    }
}

<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class AnmeldestatusRepository extends Repository
{
    public function setStoragePageIds(array $storagePageIds): void
    {
        $querySettings = $this->createQuery()->getQuerySettings();
        $querySettings->setRespectStoragePage(false);
        $querySettings->setStoragePageIds($storagePageIds);
        $this->setDefaultQuerySettings($querySettings);
    }

    public function findAnmeldeStatusByProfPrefix(): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->getQuerySettings()->setRespectSysLanguage(FALSE);

        // Mit Extbase-Query die LIKE-Einschränkung auf die Spalte "kurz" anwenden
        $query->matching(
            $query->like('kurz', 'prof_%')
        );

        return $query->execute();
    }
}

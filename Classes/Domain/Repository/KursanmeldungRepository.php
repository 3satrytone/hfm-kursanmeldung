<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Repository;

use Hfm\Kursanmeldung\Constants\Constants;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class KursanmeldungRepository extends Repository
{
    /**
     * @param array $storagePageIds
     * @return void
     */
    public function setStoragePageIds(array $storagePageIds): void
    {
        $querySettings = $this->createQuery()->getQuerySettings();
        $querySettings->setRespectStoragePage(false);
        $querySettings->setStoragePageIds($storagePageIds);
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * @param int $kurs
     */
    public function getParticipantsByKurs($kursId): QueryResultInterface {
        $kurs = intval($kursId);
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectSysLanguage(FALSE);
        $query->matching(
            $query->equals(Constants::DB_FIELD_KURS, $kurs)
        );

        return $query->execute();
    }
}

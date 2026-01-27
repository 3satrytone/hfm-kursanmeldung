<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Repository;

use Hfm\Kursanmeldung\Domain\Model\ProfStatus;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * The repository for ProfStatuses
 */
class ProfStatusRepository extends Repository
{
    /**
     * @param bool $value
     * @return void
     */
    public function setRespectStoragePage(bool $value): void
    {
        $querySettings = $this->createQuery()->getQuerySettings();
        $querySettings->setRespectStoragePage($value);
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * @param int $kursanmeldung
     * @param int $feuser
     * @return ProfStatus|null
     */
    public function findOneByKursanmeldungAndFeuser(int $kursanmeldung, int $feuser): ?ProfStatus
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching(
            $query->logicalAnd(
                $query->equals('kursanmeldung', $kursanmeldung),
                $query->equals('feuser', $feuser)
            )
        );
        /** @var ProfStatus|null $result */
        $result = $query->execute()->getFirst();
        return $result;
    }
}

<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Repository;

use Hfm\Kursanmeldung\Constants\Constants;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class KursanmeldungRepository extends Repository
{
    private const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

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
    public function getParticipantsByKurs($kursId): QueryResultInterface
    {
        $kurs = intval($kursId);
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectSysLanguage(false);
        $query->matching(
            $query->equals(Constants::DB_FIELD_KURS, $kurs)
        );

        return $query->execute();
    }

    public function getParticipantsByMail(?int $kursUid, string $email)
    {
        $kurs = intval($kursUid);

        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectSysLanguage(false);

        return $query->matching(
            $query->logicalAnd(
                $query->equals('kurs', $kurs),
                $query->like('tn.email', $email)
            )
        )
        ->execute();
    }

    /**
     * @param int $pid
     * @param string $hash
     * @param int $id
     * @param int $ts
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function getRegistration(string $hash,int $id, int $ts): QueryResultInterface
    {
        $dateIn = new \DateTime();
        $dateIn->setTimestamp($ts);
        $dateIn->format(self::DATE_TIME_FORMAT);

        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('uid', $id),
                $query->equals('registrationkey', $hash),
                $query->equals('datein', $dateIn)
            )
        );

        return $query->execute();
    }
}

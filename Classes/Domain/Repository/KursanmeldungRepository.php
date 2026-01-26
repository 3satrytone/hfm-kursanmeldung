<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\Domain\Repository;

use Hfm\Kursanmeldung\Constants\Constants;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
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

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findAllSortedByUid(): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->setOrderings([
            'uid' => QueryInterface::ORDER_DESCENDING,
        ]);

        return $query->execute();
    }

    /**
     * Suche über mehrere Felder in Kursanmeldung und verknüpften Modellen.
     * Unterstützte Feld-Schlüssel (Mapping siehe $fieldMap):
     *  - tn.vorname, tn.nachname, tn.gebdate
     *  - kurs.professor.name
     *  - datein, teilnahmeart, anmeldestatus.kurz, gezahlt, uid
     *
     * @param string|null $search Der Suchstring
     * @param array $fields Liste ausgewählter Felder (Schlüssel aus dem Mapping)
     */
    public function searchAll(?string $search, array $fields): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->setOrderings(['uid' => QueryInterface::ORDER_DESCENDING]);
        if ($search === null || trim($search) === '') {
            return $query->execute();
        }

        $search = trim($search);
        $fieldMap = [
            'tn.vorname' => 'tn.vorname',
            'tn.nachname' => 'tn.nachname',
            'tn.gebdate' => 'tn.gebdate',
            'kurs.professor' => 'kurs.professor.name',
            'datein' => 'datein',
            'teilnahmeart' => 'teilnahmeart',
            'anmeldestatus' => 'anmeldestatus.kurz',
            'gezahlt' => 'gezahlt',
            'uid' => 'uid',
        ];

        $selected = array_values(array_intersect(array_keys($fieldMap), $fields));
        if (empty($selected)) {
            // Fallback: alle Felder durchsuchen
            $selected = array_keys($fieldMap);
        }

        $constraints = [];

        // Datum erkennen (dd.mm.yyyy)
        $asDate = null;
        if (preg_match('/^(\d{1,2})\.(\d{1,2})\.(\d{4})$/', $search, $m)) {
            try {
                $asDate = new \DateTime(sprintf('%04d-%02d-%02d', (int)$m[3], (int)$m[2], (int)$m[1]));
            } catch (\Throwable $e) {
                $asDate = null;
            }
        }

        $isNumeric = is_numeric(str_replace([',', '.'], '', $search));

        foreach ($selected as $key) {
            $prop = $fieldMap[$key];
            if (in_array($key, ['tn.vorname', 'tn.nachname', 'kurs.professor', 'teilnahmeart', 'anmeldestatus'], true)) {
                $constraints[] = $query->like($prop, '%' . $search . '%');
                continue;
            }
            if (in_array($key, ['uid'], true)) {
                if (ctype_digit($search)) {
                    $constraints[] = $query->equals($prop, (int)$search);
                }
                continue;
            }
            if (in_array($key, ['gezahlt'], true)) {
                if ($isNumeric) {
                    // Dezimaltrennzeichen deutsch -> Punkt
                    $norm = (float)str_replace(['.', ','], ['', '.'], $search);
                    $constraints[] = $query->equals($prop, $norm);
                }
                continue;
            }
            if (in_array($key, ['tn.gebdate', 'datein'], true)) {
                if ($asDate instanceof \DateTime) {
                    $start = clone $asDate;
                    $start->setTime(0, 0, 0);
                    $end = clone $asDate;
                    $end->setTime(23, 59, 59);
                    $constraints[] = $query->logicalAnd(
                        $query->greaterThanOrEqual($prop, $start),
                        $query->lessThanOrEqual($prop, $end)
                    );
                }
                continue;
            }
        }

        if (empty($constraints)) {
            // Keine sinnvollen Constraints ableitbar → keine Ergebnisse einschränken
            return $query->execute();
        }

        $query->matching($query->logicalOr(...$constraints));
        return $query->execute();
    }

    /**
     * Wie searchAll, zusätzlich gefiltert auf einen Kurs.
     */
    public function getParticipantsByKursFiltered(int $kursId, ?string $search, array $fields): QueryResultInterface
    {
        $kurs = (int)$kursId;
        $query = $this->createQuery();
        $query->setOrderings(['uid' => QueryInterface::ORDER_DESCENDING]);

        $constraints = [$query->equals(Constants::DB_FIELD_KURS, $kurs)];

        if ($search !== null && trim($search) !== '') {
            $search = trim($search);
            // Wiederverwendung der Logik aus searchAll (dupliziert, da Extbase-Query kein Subquery erlaubt)
            $fieldMap = [
                'tn.vorname' => 'tn.vorname',
                'tn.nachname' => 'tn.nachname',
                'tn.gebdate' => 'tn.gebdate',
                'kurs.professor' => 'kurs.professor.name',
                'kurs.instrument' => 'kurs.instrument',
                'datein' => 'datein',
                'teilnahmeart' => 'teilnahmeart',
                'anmeldestatus' => 'anmeldestatus.kurz',
                'gezahlt' => 'gezahlt',
                'uid' => 'uid',
            ];
            $selected = array_values(array_intersect(array_keys($fieldMap), $fields));
            if (empty($selected)) {
                $selected = array_keys($fieldMap);
            }

            $or = [];
            $asDate = null;
            if (preg_match('/^(\d{1,2})\.(\d{1,2})\.(\d{4})$/', $search, $m)) {
                try {
                    $asDate = new \DateTime(sprintf('%04d-%02d-%02d', (int)$m[3], (int)$m[2], (int)$m[1]));
                } catch (\Throwable $e) {
                    $asDate = null;
                }
            }
            $isNumeric = is_numeric(str_replace([',', '.'], '', $search));

            foreach ($selected as $key) {
                $prop = $fieldMap[$key];
                if (in_array($key, ['tn.vorname', 'tn.nachname', 'kurs.professor', 'kurs.instrument', 'teilnahmeart', 'anmeldestatus'], true)) {
                    $or[] = $query->like($prop, '%' . $search . '%');
                    continue;
                }
                if ($key === 'uid') {
                    if (ctype_digit($search)) {
                        $or[] = $query->equals($prop, (int)$search);
                    }
                    continue;
                }
                if ($key === 'gezahlt') {
                    if ($isNumeric) {
                        $norm = (float)str_replace(['.', ','], ['', '.'], $search);
                        $or[] = $query->equals($prop, $norm);
                    }
                    continue;
                }
                if (in_array($key, ['tn.gebdate', 'datein'], true)) {
                    if ($asDate instanceof \DateTime) {
                        $start = clone $asDate;
                        $start->setTime(0, 0, 0);
                        $end = clone $asDate;
                        $end->setTime(23, 59, 59);
                        $or[] = $query->logicalAnd(
                            $query->greaterThanOrEqual($prop, $start),
                            $query->lessThanOrEqual($prop, $end)
                        );
                    }
                    continue;
                }
            }

            if (!empty($or)) {
                $constraints[] = $query->logicalOr(...$or);
            }
        }

        $query->matching($query->logicalAnd(...$constraints));
        return $query->execute();
    }

    public function findByKursNotPassive(int $kurs): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->getQuerySettings()->setRespectSysLanguage(FALSE);

        $joQuery = $query->logicalAnd(
            $query->equals('kurs', $kurs),
            $query->equals('teilnahmeart', 0),
            $query->logicalNot(
                $query->in('anmeldestatus', array(2,5))
            )
        );

        $query->matching(
            $joQuery
        );
        return $query->execute();
    }
}

<?php

namespace Hfm\Kursanmeldung\Utility;

use Hfm\Kursanmeldung\Domain\Model\Kurs;

class ParticipantUtility
{
    /**
     * @param Kurs|null $kurs
     * @param array $kursTn
     * @return array
     */
    public function checkKursParticipant(?Kurs $kurs, array $kursTn): array
    {
        $tnactionArr = [];
        if ($kurs != null) {
            $kursTnActive = $kurs->getAktivtn();
            $kursTnPassiv = $kurs->getPassivtn();

            // Reduzierung der Maximalen Plätze um registrierte Teilnehmer
            if (!empty($kursTn)) {
                foreach ($kursTn as $tn) {
                    if ($tn->getTeilnahmeart() == 0) {
                        $kursTnActive -= 1;
                    } else {
                        $kursTnPassiv -= 1;
                    }
                }
            }
            if ($kursTnActive > 0) {
                $tnactionArr['aktiveTn'] = $kursTnActive;
            }
            if ($kursTnPassiv > 0) {
                $tnactionArr['passivTn'] = $kursTnPassiv;
            }
        }

        return $tnactionArr;
    }
}
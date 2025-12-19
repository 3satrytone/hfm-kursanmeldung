<?php

namespace Hfm\Kursanmeldung\Utility;

use Hfm\Kursanmeldung\Domain\Model\Kurs;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;

class FormatUtility
{
    /**
     * @param Kurs|null $kurs
     */
    public function buildCBFromTCA(?Kurs $kurs): array
    {
        $checkBoxArr = $GLOBALS['TCA']['tx_kursanmeldung_domain_model_kurs']['columns']['ensemble']['config']['items'];
        $cbArr = [];
        $checkedEnsemble = $this->getBitCheckedByArray($checkBoxArr, $kurs ? intval($kurs->getEnsemble()) : 0);

        if (!empty($checkBoxArr)) {
            foreach ($checkBoxArr as $key => $value) {
                $cbArr[$key] = [
                    'item' => $key,
                    'checked' => (in_array($key, $checkedEnsemble) ? 1 : 0)
                ];
            }
        }

        return $cbArr;
    }

    /**
     * @param array $arr
     * @param int $bitVal
     * @return array
     */
    public function getBitCheckedByArray(array $arr = [], int $bitVal = 0): array
    {
        $checked = [];
        if (!empty($arr)) {
            $max = count($arr);
            for ($i = 0; $i < $max; $i++) {
                if ($bitVal & pow(2, $i)) {
                    $checked[] = $i;
                }
            }
        }

        return $checked;
    }



    /**
     * @param RequestInterface $request
     * @return int|object|float
     */
    public function getBitmaskFromRequest(RequestInterface $request): int|object|float
    {
        // Ensemble checkbox Array to Bitmask
        $ensembleBitmask = 0;

        if ($request->hasArgument('ensemble')) {
            $ensemble = $request->getArgument('ensemble');
            if (!empty($ensemble)) {
                foreach ($ensemble as $key => $value) {
                    if ($value == 1) {
                        $ensembleBitmask += pow(2, $key);
                    }
                }
            }
        }

        return $ensembleBitmask;
    }
}
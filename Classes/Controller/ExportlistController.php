<?php
declare(strict_types=1);

namespace Hfm\Kursanmeldung\Controller;

use Hfm\Kursanmeldung\Domain\Model\Kursanmeldung;
use Hfm\Kursanmeldung\Domain\Repository\KursanmeldungRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Country\CountryProvider;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class ExportlistController extends ActionController
{
    public function __construct(
        private readonly KursanmeldungRepository $kursanmeldungRepository,
        private readonly CountryProvider $countryProvider,
    ) {
    }

    public function listAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }

    public function showAction(): void
    {
        // Implement showing a single Exportlist record when templates are available.
    }

    public function ajaxListAction(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $fieldsParam = $queryParams['fields'] ?? '';
        $fields = array_values(array_filter(array_map('trim', explode(',', (string)$fieldsParam))));
        $filtersParam = $queryParams['filters'] ?? [];
        $filters = is_array($filtersParam) ? $filtersParam : [];

        if (empty($fields)) {
            return new HtmlResponse('<div class="alert alert-warning">Keine Felder ausgewählt.</div>');
        }

        $exportFieldsMapping = $this->getExportFieldsMapping();
        $this->kursanmeldungRepository->setRespectStoragePage(false);
        $participants = $this->kursanmeldungRepository->findAllSortedByUid();

        $html = '<table class="table table-sm table-striped" id="exportlisteTable"><thead><tr>';
        foreach ($fields as $field) {
            $html .= '<th>' . htmlspecialchars($exportFieldsMapping[$field] ?? $field) . '</th>';
        }
        $html .= '</tr><tr class="exportliste-filter-row">';
        foreach ($fields as $field) {
            $currentFilter = $filters[$field] ?? '';
            $html .= '<th><input type="text" class="form-control form-control-sm exportliste-col-filter" placeholder="Filter..." data-col="' . htmlspecialchars($field) . '" value="' . htmlspecialchars($currentFilter) . '"></th>';
        }
        $html .= '</tr></thead><tbody>';
        foreach ($participants as $reg) {
            $row = [];
            foreach ($fields as $field) {
                $row[$field] = $this->getFieldValue($reg, $field);
            }
            $match = true;
            foreach ($filters as $filterField => $filterValue) {
                if ($filterValue === '' || $filterValue === null) continue;
                $cellValue = isset($row[$filterField]) ? mb_strtolower((string)$row[$filterField]) : '';
                if (strpos($cellValue, mb_strtolower((string)$filterValue)) === false) {
                    $match = false;
                    break;
                }
            }
            if ($match) {
                $html .= '<tr data-uid="' . (int)$reg->getUid() . '">';
                foreach ($fields as $field) {
                    $html .= '<td>' . htmlspecialchars((string)$row[$field]) . '</td>';
                }
                $html .= '</tr>';
            }
        }
        $html .= '</tbody></table>';

        return new HtmlResponse($html);
    }

    private function getExportFieldsMapping(): array
    {
        return [
            'register.uid' => 'Reg. Nr.',
            'register.datein' => 'Anmeldedatum',
            'register.teilnahmeart' => 'Teilnahmeart',
            'register.gebuehr' => 'ANG Gebühr',
            'register.gezahlt' => 'ANG Gezahlt',
            'register.gebuehrag' => 'TNG Gebühr',
            'register.gezahltag' => 'TNG Gezahlt',
            'register.zahlart' => 'Zahlart',
            'register.zahltbis' => 'Zahlt bis',
            'register.room' => 'Zimmer',
            'register.roomwith' => 'Zimmer mit',
            'register.roomfrom' => 'Zimmer von',
            'register.roomto' => 'Zimmer bis',
            'register.comment' => 'Kommentar',
            'register.notice' => 'Notiz',
            'register.programm' => 'Programm',
            'register.orchesterstudio' => 'Orchesterstudio',
            'register.stipendiat' => 'Stipendiat',
            'register.studentship' => 'Studentship',
            'register.studystat' => 'Studienstatus',
            'register.ensemble' => 'Ensemble',
            'register.anmeldestatus' => 'Anmeldestatus',
            'register.bezahlt' => 'Bezahlt',
            'register.profstatus' => 'Prof-Status',
            'register.savedata' => 'Datenspeicherung',
            'tn.vorname' => 'Vorname',
            'tn.nachname' => 'Nachname',
            'tn.anrede' => 'Anrede',
            'tn.titel' => 'Titel',
            'tn.gebdate' => 'Geb. Datum',
            'tn.matrikel' => 'Matrikel',
            'tn.email' => 'E-Mail',
            'tn.mobil' => 'Mobil',
            'tn.telefon' => 'Telefon',
            'tn.telefax' => 'Telefax',
            'tn.adresse1' => 'Strasse',
            'tn.hausnr' => 'Hausnr.',
            'tn.adresse2' => 'Adresszusatz',
            'tn.plz' => 'PLZ',
            'tn.ort' => 'Ort',
            'tn.land' => 'Land',
            'tn.nation' => 'Nationalität',
            'tn.sprache' => 'Sprache',
            'kurs.kursnr' => 'Kurs Nr.',
            'kurs.instrument' => 'Instrument',
            'kurs.professor.name' => 'Professor',
            'kurs.kurszeitstart' => 'Kursstart',
            'kurs.kurszeitend' => 'Kursende',
            'kurs.gebuehr' => 'Kurs Gebühr',
            'kurs.kursort' => 'Kursort',
        ];
    }

    private function getFieldValue(Kursanmeldung $reg, string $fieldPath): string
    {
        $parts = explode('.', $fieldPath);
        $current = $reg;

        if ($parts[0] === 'register') {
            // Sonderfall: profstatus ist ein ObjectStorage
            if (isset($parts[1]) && $parts[1] === 'profstatus') {
                $profstatusStorage = $reg->getProfstatus();
                if (!$profstatusStorage instanceof ObjectStorage || $profstatusStorage->count() === 0) return '';
                $items = [];
                foreach ($profstatusStorage as $ps) {
                    $items[] = method_exists($ps, 'getKurz') ? $ps->getKurz() : (string)$ps->getUid();
                }
                return implode(', ', $items);
            }
            // Sonderfall: anmeldestatus ist ein verknüpftes Objekt
            if (isset($parts[1]) && $parts[1] === 'anmeldestatus') {
                $anmeldestatusStorage = $reg->getAnmeldestatus();
                if (!$anmeldestatusStorage instanceof ObjectStorage || $anmeldestatusStorage->count() === 0) return '';
                $anmeldestatusStorage->rewind();
                $anmeldestatus = $anmeldestatusStorage->current();
                if (!$anmeldestatus) return '';
                return (string)($anmeldestatus->getKurz() ?: $anmeldestatus->getAnmeldestatus() ?: '');
            }
            array_shift($parts);
        } elseif ($parts[0] === 'tn') {
            $tnStorage = $reg->getTn();
            if ($tnStorage instanceof ObjectStorage && $tnStorage->count() > 0) {
                $tnStorage->rewind();
                $current = $tnStorage->current();
            } else {
                $current = null;
            }
            array_shift($parts);
        } elseif ($parts[0] === 'kurs') {
            $current = $reg->getKurs();
            array_shift($parts);
        }

        foreach ($parts as $part) {
            if (!$current) return '';
            $getter = 'get' . ucfirst($part);
            if (method_exists($current, $getter)) {
                $current = $current->$getter();
            } else {
                return '';
            }
        }

        switch ($fieldPath) {
            case 'tn.land':
            case 'tn.nation':
                try {
                    $country = $this->countryProvider->getByIsoCode((string)$current);
                    $current = $country ? $country->getName() : (string)$current;
                } catch (\Throwable $e) {
                    $current = (string)$current;
                }
                break;
            case 'tn.anrede':
                $current = $current === 0 ? 'Frau' : 'Herr';
                break;
            case 'register.datein':
                $current = $current?->format('d.m.Y H:i') ?? '';
                break;
            case 'register.teilnahmeart':
                $current = $current === 0 ? 'passiv' : 'aktiv';
                break;
            case 'register.savedata':
                $current = $current === 0 ? 'Ja' : 'Nein';
                break;
            case 'register.room':
                $current = LocalizationUtility::translate('be_export.room.' . $current, 'kursanmeldung') ?? (string)$current;
                break;
            case 'register.roomfrom':
            case 'register.roomto':
                $current = $current != '' ? (new \DateTime($current))->format('d.m.Y') : '';
                break;
            case 'register.gezahlt':
            case 'register.gezahltag':
            case 'register.gebuehr':
            case 'register.gezahltos':
                $current = (string)$current;
                break;
        }

        if ($current instanceof \DateTime) {
            return $current->format('d.m.Y');
        }
        if ($current === true) return 'Ja';
        if ($current === false) return 'Nein';
        if ($current instanceof ObjectStorage) {
            $items = [];
            foreach ($current as $item) {
                if (method_exists($item, 'getName')) {
                    $items[] = $item->getName();
                } elseif (method_exists($item, 'getHotel')) {
                    $items[] = $item->getHotel();
                } elseif (method_exists($item, 'getEnname')) {
                    $items[] = $item->getEnname();
                } elseif (method_exists($item, 'getTitel')) {
                    $items[] = $item->getTitel();
                } else {
                    $items[] = (string)$item->getUid();
                }
            }
            return implode(', ', $items);
        }

        return (string)$current;
    }
}

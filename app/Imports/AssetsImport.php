<?php

namespace App\Imports;

use App\Models\Assets;
use App\Models\Approval;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class AssetsImport implements ToCollection
{
    /**
     * Convert Excel serial date to PHP date format.
     *
     * @param int $serialNumber The Excel serial number representing the date.
     * @return string The date formatted as 'Y-m-d'.
     */
    public function excelSerialDateToPHPDate($serialNumber)
    {
        return date('Y-m-d', strtotime('1899-12-30 +' . ($serialNumber) . ' days'));
    }

    public function startRow(): int
    {
        return 3; // Start reading from the second row
    }

    /**
     * @param Collection $rows
     *
     * @return void
     */
    public function collection(Collection $rows)
    {
        $defaultAverageDays = [
            'CORONA DISCHARGE' => 70,
            'ARCHING SOUND' => 25,
            'TRACKING SOUND' => 47,
            'HOTSPOT' => 39,
            'ULTRASOUND' => 52,
            'MECHANICAL VIBRATION' => 54,
        ];
        
        $duplicateRows = [];

        foreach ($rows as $index => $row) {
            if ($index >= 2) {
                $functionalLocation = $row[11];
                $switchgearBrand = $row[8];
                $substationName = $row[12];
                $tev = $row[4];
                $hotspot = $row[5];
                $defect = $row[3];
                $defect1 = $row[14];
                $defect2 = $row[15];
                $date = $this->excelSerialDateToPHPDate($row[1]); 
                $targetDate = $row[20]; 
                $completionStatus = $row[21];

                if (is_null($targetDate) && is_null($completionStatus)) {
                    $defectCategory = strtoupper($defect1);
                    $defaultDays = isset($defaultAverageDays[$defectCategory]) ? $defaultAverageDays[$defectCategory] : 0;

                    $reportDate = Carbon::parse($date);
                    $targetDate = $reportDate->addDays($defaultDays)->format('Y-m-d');
                } else {
                    $targetDate = $this->excelSerialDateToPHPDate($targetDate);
                    $completionStatus = $this->excelSerialDateToPHPDate($completionStatus);
                }

                $existingAssetQuery = Assets::where('Functional_Location', $functionalLocation)
                    ->where('Switchgear_Brand', $switchgearBrand)
                    ->where('Substation_Name', $substationName)
                    ->where('Defect', $defect)
                    ->where('Defect1', $defect1)
                    ->where('Defect2', $defect2)
                    ->where('Date', $date)
                    ->where('Target_Date', $targetDate)
                    ->where('completed_status', $completionStatus);

                if (is_null($tev)) {
                    $existingAssetQuery->whereNull('TEV');
                } else {
                    $existingAssetQuery->where('TEV', $tev);
                }

                if (is_null($hotspot)) {
                    $existingAssetQuery->whereNull('Hotspot');
                } else {
                    $existingAssetQuery->where('Hotspot', $hotspot);
                }

                $existingAsset = $existingAssetQuery->first();

                if ($existingAsset) {
                    $duplicateRows[] = $index + 1;
                    continue;
                }

                $functionalLocationExists = Assets::where('Functional_Location', $functionalLocation)->exists();

                if (!$functionalLocationExists) {
                    $assetEntry = new Assets([
                        'Functional_Location' => $functionalLocation,
                        'Switchgear_Brand' => $switchgearBrand,
                        'Substation_Name' => $substationName,
                        'TEV' => $tev,
                        'Hotspot' => $hotspot,
                        'Defect' => $defect,
                        'Defect1' => $defect1,
                        'Defect2' => $defect2,
                        'Date' => $date,
                        'Target_Date' => $targetDate,
                        'completed_status' => $completionStatus,
                    ]);
                    $assetEntry->save();
                    Session::flash('success', 'Data imported successfully and new asset created.');
                } else {
                    $defectsMatchQuery = Assets::where('Functional_Location', $functionalLocation)
                        ->where('Defect', $defect)
                        ->where('Defect1', $defect1)
                        ->where('Defect2', $defect2);

                    $defectsMatch = $defectsMatchQuery->exists();

                    if ($defectsMatch) {
                        $columnsNotMatch = $this->compareNonDefectColumns($functionalLocation, $tev, $hotspot ,$switchgearBrand, $substationName, $defect,  $defect1, $defect2, $date, $targetDate,$completionStatus,$row);

                        if (!$columnsNotMatch) {
                            $duplicate = Assets::where('Functional_Location', $functionalLocation)
                            ->where('Switchgear_Brand', $switchgearBrand)
                            ->where('Substation_Name', $substationName)
                            ->where('TEV', $tev)
                            ->where('Hotspot', $hotspot)
                            ->where('Date', $date)
                            ->where('Defect', $defect)
                            ->where('Defect1', $defect1)
                            ->where('Defect2', $defect2)
                            ->where('Target_Date', $targetDate)
                            ->exists();

                            if ($duplicate) {
                                Session::flash('error', 'Duplication found');
                            }

                            $approvalEntry = new Approval([
                                'Functional_Location' => $functionalLocation,
                                'Switchgear_Brand' => $switchgearBrand,
                                'Substation_Name' => $substationName,
                                'TEV' => $tev,
                                'Hotspot' => $hotspot,
                                'Defect' => $defect,
                                'Defect1' => $defect1,
                                'Defect2' => $defect2,
                                'Date' => $date,
                                'Target_Date' => $targetDate,
                                'completed_status' => $completionStatus,
                            ]);
                            $approvalEntry->save();
                            Session::flash('success', 'Data imported successfully and asset moved to approval.');
                        } else {
                            $duplicateRows[] = $index + 1;
                        }
                    } else {
                        $assetEntry = new Assets([
                            'Functional_Location' => $functionalLocation,
                            'Switchgear_Brand' => $switchgearBrand,
                            'Substation_Name' => $substationName,
                            'TEV' => $tev,
                            'Hotspot' => $hotspot,
                            'Defect' => $defect,
                            'Defect1' => $defect1,
                            'Defect2' => $defect2,
                            'Date' => $date,
                            'Target_Date' => $targetDate,
                            'completed_status' => $completionStatus,
                        ]);
                        $assetEntry->save();
                        Session::flash('success', 'Data imported successfully and new asset created.');
                    }
                }
            }
        }

        if (!empty($duplicateRows)) {
            Session::flash('error', 'Duplicate entries found at rows: ' . implode(', ', $duplicateRows) . '. Kindly check your file again.');
        }
    }

    /**
     * Compare non-defect columns between matched asset and current row.
     *
     * @param string $functionalLocation
     * @param array $row
     * @return bool
     */
    private function compareNonDefectColumns($functionalLocation, $tev, $hotspot, $switchgearBrand, $substationName, $defect, $defect1, $defect2, $date, $targetDate, $completionStatus, $row)
{
    try {
        $matchedAsset = Assets::where('Functional_Location', $functionalLocation)
            ->where('Switchgear_Brand', $switchgearBrand)
            ->where('Substation_Name', $substationName)
            ->where('Defect', $defect)
            ->where('Defect1', $defect1)
            ->where('Defect2', $defect2)
            ->where('Date', $date)
            ->where('Target_Date', $targetDate)
            ->where('completed_status', $completionStatus)
            ->where('TEV', $tev)
            ->where('Hotspot', $hotspot)
            ->first();

        if ($matchedAsset) {
            if ($matchedAsset->Switchgear_Brand != $row[8] ||
                $matchedAsset->Substation_Name != $row[12] ||
                $matchedAsset->TEV != $row[4] ||
                $matchedAsset->Hotspot != $row[5] ||
                $matchedAsset->completed_status != $row[21]) {
                return true;
            }
        }
        return false;
    } catch (\Exception $e) {
        Log::error('Error comparing non-defect columns: ' . $e->getMessage());
        throw $e;
    }
}

}

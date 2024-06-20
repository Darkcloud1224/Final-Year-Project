<?php

namespace App\Imports;

use App\Models\Assets;
use App\Models\Approval;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Session;

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
     * @param array $row
     *
     * @return void
     */
    public function collection(Collection $rows)
    {
        $duplicateRows = [];

        foreach ($rows as $index => $row) {
            if ($index >= 2) { 

                if (is_null($row[4]) || is_null($row[5])) {
                    Session::flash('error', 'TEV or Hotspot column cannot be null in row ' . ($index + 1));
                    continue;
                }

                $functionalLocation = $row[11];
                $switchgearBrand = $row[8];
                $substationName = $row[12];
                $tev = $row[4];
                $hotspot = $row[5];
                $defect = $row[3];
                $defect1 = $row[14];
                $defect2 = $row[15];
                $date = $this->excelSerialDateToPHPDate($row[1]); 
                $targetDate = $this->excelSerialDateToPHPDate($row[20]); 
                $completionStatus = $this->excelSerialDateToPHPDate($row[21]); 

                $existingAsset = Assets::where('Functional_Location', $functionalLocation)
                    ->where('Switchgear_Brand', $switchgearBrand)
                    ->where('Substation_Name', $substationName)
                    ->where('TEV', $tev)
                    ->where('Hotspot', $hotspot)
                    ->where('Defect', $defect)
                    ->where('Defect1', $defect1)
                    ->where('Defect2', $defect2)
                    ->where('Date', $date)
                    ->where('Target_Date', $targetDate)
                    ->where('completed_status', $completionStatus)
                    ->first();

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
                        'completed_status'=>$completionStatus,
                    ]);
                    $assetEntry->save();
                    Session::flash('success', 'Data imported successfully and new asset created.');
                } else {
                    $defectsMatch = Assets::where('Functional_Location', $functionalLocation)
                        ->where('Defect', $defect)
                        ->where('Defect1', $defect1)
                        ->where('Defect2', $defect2)
                        ->exists();

                    if ($defectsMatch) {
                        $columnsNotMatch = $this->compareNonDefectColumns($functionalLocation, $row);

                        if ($columnsNotMatch) {
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
                            'completed_status'=>$completionStatus,
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
    private function compareNonDefectColumns($functionalLocation, $row)
    {
        $matchedAsset = Assets::where('Functional_Location', $functionalLocation)
            ->first();

        if ($matchedAsset) {
            if ($matchedAsset->Switchgear_Brand != $row[8] ||
                $matchedAsset->Substation_Name != $row[12] ||
                $matchedAsset->TEV != $row[4] ||
                $matchedAsset->Hotspot != $row[5] ||
                $matchedAsset->Completion_Status != $row[21]) {
                return true; 
            }
        }

        return false; 
    }
}

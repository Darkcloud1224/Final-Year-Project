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
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        $existingFunctionalLocations = Assets::pluck('Functional_Location');
        $duplicateRows = [];

        $count = 0;
        foreach ($rows as $index => $row) {
            if ($count >= 2) { 
                if (is_null($row[4]) || is_null($row[5])) {
                    Session::flash('error', 'TEV or Hotspot column cannot be null in row. Please check back your uploaded excel file at column no ' . ($index + 1));
                    continue;
                }
                $date = $this->excelSerialDateToPHPDate($row[1]);
                $functionalLocation = $row[11];

                $existingAsset = Assets::where('Functional_Location', $functionalLocation)
                    ->where('Switchgear_Brand', $row[8])
                    ->where('Substation_Name', $row[12])
                    ->where('TEV', $row[4])
                    ->where('Hotspot', $row[5])
                    ->where('Defect', $row[3])
                    ->where('Defect1', $row[14])
                    ->where('Defect2', $row[15])
                    ->where('Date', $date)
                    ->first();

                if ($existingAsset) {
                    $duplicateRows[] = $index + 1;
                    continue;
                }

                $functionalLocationExists = Assets::where('Functional_Location', $functionalLocation)->exists();

                if ($functionalLocationExists) {
                    $defectMatch = Assets::where('Functional_Location', $functionalLocation)
                        ->where('Defect', $row[3])
                        ->where('Defect1', $row[14])
                        ->where('Defect2', $row[15])
                        ->exists();

                    if ($defectMatch) {
                        $approvalEntry = new Approval([
                            'Functional_Location' => $row[11],
                            'Switchgear_Brand' => $row[8],
                            'Substation_Name' => $row[12],
                            'TEV' => $row[4],
                            'Hotspot' => $row[5],
                            'Defect' => $row[3],
                            'Defect1' => $row[14],
                            'Defect2' => $row[15],
                            'Date' => $date,
                        ]);
                        $approvalEntry->save();
                        Session::flash('success', 'Data imported successfully and asset moved to approval.');
                    } else {
                        $assetEntry = new Assets([
                            'Functional_Location' => $row[11],
                            'Switchgear_Brand' => $row[8],
                            'Substation_Name' => $row[12],
                            'TEV' => $row[4],
                            'Hotspot' => $row[5],
                            'Defect' => $row[3],
                            'Defect1' => $row[14],
                            'Defect2' => $row[15],
                            'Date' => $date,
                        ]);
                        $assetEntry->save();
                        Session::flash('success', 'Data imported successfully and new asset created.');
                    }
                } else {
                    $assetEntry = new Assets([
                        'Functional_Location' => $row[11],
                        'Switchgear_Brand' => $row[8],
                        'Substation_Name' => $row[12],
                        'TEV' => $row[4],
                        'Hotspot' => $row[5],
                        'Defect' => $row[3],
                        'Defect1' => $row[14],
                        'Defect2' => $row[15],
                        'Date' => $date,
                    ]);
                    $assetEntry->save();
                    Session::flash('success', 'Data imported successfully and new asset created.');
                }
            } else {
                $count++;
            }
        }

        if (!empty($duplicateRows)) {
            Session::flash('error', 'Duplicate entries found at rows: ' . implode(', ', $duplicateRows) . '. Kindly check your file again.');
        }
    }
}

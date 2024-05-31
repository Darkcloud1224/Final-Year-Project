<?php

namespace App\Imports;

use App\Models\Assets;
use App\Models\Approval; // Corrected the model name
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Session;

class AssetsImport implements ToCollection
{
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


        $count = 0;
        foreach ($rows as $index => $row) {            
            if ($count > 1) {
                if (is_null($row[4]) || is_null($row[5])) {
                    Session::flash('error', 'TEV or Hotspot column cannot be null in row. Please check back your uploaded excel file at column no ' . ($index + 1));
                    continue; 
                }
                if (!$existingFunctionalLocations->contains($row[11])) {
                    $asset_Var = new Approval([ 
                        'Functional_Location' => $row[11],
                        'Switchgear_Brand' => $row[8],
                        'Substation_Name' => $row[12],
                        'TEV' => $row[4],
                        'Hotspot' => $row[5],
                    ]);
                    Session::flash('success', 'Data imported successfully and asset moved to approval.');
                } else {
                    $asset_Var = new Assets([
                        'Functional_Location' => $row[11],
                        'Switchgear_Brand' => $row[8],
                        'Substation_Name' => $row[12],
                        'TEV' => $row[4],
                        'Hotspot' => $row[5],
                    ]);
                    Session::flash('success', 'Data imported successfully and new asset created.');
                }
                try {
                    $saved = $asset_Var->save();
                } catch (\Exception $e) {
                    dd($e->getMessage()); // Display the SQL error message
                }
            }
            else {
                $count = $count + 1;
            }
        }
        return $asset_Var;
    }
}

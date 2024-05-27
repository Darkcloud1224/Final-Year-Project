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
        return 2; // Start reading from the second row
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
        foreach ($rows as $row) {
            if ($count > 1) {
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
                $saved = $asset_Var->save();
            }
            else {
                $count = $count + 1;
            }
        }
        return $asset_Var;
    }
}

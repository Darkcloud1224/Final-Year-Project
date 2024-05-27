<?php

namespace App\Exports;

use App\Models\Assets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AssetsExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Assets::select('Functional_Location','Switchgear_Brand','Substation_Name','Health_Status','TEV','Hotspot','Rectify_Status')->get();
    }

    /**
     * Define the headings for the export file.
     *
     * @return array
     */
    public function headings(): array
    {
        return ['Functional_Location','Switchgear Brand','Substation_Name','Health_Status','TEV','Hotspot','Rectify_Status'];
    }
}

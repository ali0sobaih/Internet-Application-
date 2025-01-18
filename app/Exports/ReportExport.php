<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportExport implements FromCollection, WithHeadings
{
    /**
     * Fetch data from the updates table.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return DB::table('updates')->select(
            'id',
            'user_id',
            'archive_id',
            'difference',
            'created_at',
            'updated_at'
        )->get();
    }

    /**
     * Define headings for the exported file.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'User ID',
            'Archive ID',
            'Difference',
            'Created At',
            'Updated At',
        ];
    }
}

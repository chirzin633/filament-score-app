<?php

namespace App\Exports;

use App\Models\Score;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Override;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ScoreExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Score::with(['student.classRoom', 'subject'])->get();
    }

    #[Override]
    public function headings(): array
    {
        return [
            'No',
            'Nama Siswa',
            'Kelas',
            'Mata Pelajaran',
            'Tugas',
            'UTS',
            'UAS',
            'Nilai Akhir',
            'Status'
        ];
    }

    #[Override]
    public function map($score): array
    {
        static $no = 1;
        return [
            $no++,
            $score->student?->name ?? '-',
            $score->student?->clasRoom?->name ?? '-',
            $score->subject?->name ?? '-',
            $score->tugas,
            $score->uts,
            $score->uas,
            $score->final_score,
            $score->final_score >= 75 ? 'LULUS' : 'REMEDIAL',
        ];
    }

    #[Override]
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '6366F1']]],
        ];
    }
}

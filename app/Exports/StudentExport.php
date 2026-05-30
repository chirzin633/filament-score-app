<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Override;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Student::with('classRoom')->get();
    }

    #[Override]
    public function headings(): array
    {
        return [
            'No',
            'NIS',
            'Nama Lengkap',
            'Jenis Kelamin',
            'Kelas',
            'Tanggal Terdaftar'
        ];
    }

    #[Override]
    public function map($student): array
    {
        static $no = 1;
        return [
            $no++,
            $student->nis,
            $student->name,
            $student->gender === 'L' ? 'Laki-laki' : 'Perempuan',
            $student->classRoom?->name ?? '-',
            $student->created_at->format('d M Y')
        ];
    }

    #[Override]
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '6366F1']]]
        ];
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\ClassRoom;
use App\Models\Score;
use Filament\Widgets\ChartWidget;

class ScoreChart extends ChartWidget
{
    protected static ?string $heading = 'Rata-rata Nilai Per Kelas';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $classes = ClassRoom::with(['students.scores'])
            ->get()
            ->map(function ($class) {
                $avgScore = Score::whereIn('student_id', $class->students->pluck('id'))
                    ->avg('final_score') ?? 0;

                return [
                    'label' => $class->name,
                    'value' => round($avgScore, 2)
                ];
            });
        return [
            'datasets' => [
                [
                    'label' => 'Rata-rata Nilai Akhir',
                    'data' => $classes->pluck('value')->toArray(),
                    'backgroundColor' => '#6366f1',
                    'borderColor' => '#4f46e5'
                ],
            ],
            'labels' => $classes->pluck('label')->toArray()
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}

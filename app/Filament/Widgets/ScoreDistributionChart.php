<?php

namespace App\Filament\Widgets;

use App\Models\Score;
use Filament\Widgets\ChartWidget;
use Override;

class ScoreDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Status Nilai';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '200px';

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => [
                    'display' => false,
                ],
                'y' => [
                    'display' => false,
                ],
            ],
        ];
    }

    protected function getData(): array
    {
        $lulus = Score::where('final_score', '>=', 75)->count();
        $remedial = Score::where('final_score', '<', 75)->count();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah',
                    'data' => [$lulus, $remedial],
                    'backgroundColor' => ['#22c55e', '#ef4444'],
                    'borderColor' => ['#16a34a', '#dc2626']
                ],
            ],

            'labels' => ['Lulus', 'Remedial']
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}

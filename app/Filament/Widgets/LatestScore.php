<?php

namespace App\Filament\Widgets;

use App\Models\Score;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestScore extends BaseWidget
{
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Score::with(['student.classRoom', 'subject', 'teacher'])
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('student.name')
                    ->label('Siswa'),

                TextColumn::make('subject.name')
                    ->label('Mapel'),

                TextColumn::make('final_score')
                    ->label('Nilai')
                    ->badge()
                    ->color(fn(float $state): string => $state >= 75 ? 'success' : 'danger')
                    ->numeric(decimalPlaces: 2),

                TextColumn::make('teacher.name')
                    ->label('Guru')
                    ->default('-'),

                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->since()
            ])
            ->heading('📅 5 Nilai Terbaru');
    }
}
<?php

namespace App\Filament\Widgets;

use App\Models\Score;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RemedialList extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Score::where('final_score', '<', 75)
                    ->with(['student.classRoom', 'subject'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->searchable(),

                TextColumn::make('student.classRoom.name')
                    ->label('Kelas'),

                TextColumn::make('subject.name')
                    ->label('Mata Pelajaran'),

                TextColumn::make('final_score')
                    ->label('Nilai')
                    ->badge()
                    ->color('danger')
                    ->numeric(decimalPlaces: 2)
            ])
            ->heading('10 Siswa Butuh Remedial Terbaru');
    }
}

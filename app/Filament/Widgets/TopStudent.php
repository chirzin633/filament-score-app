<?php

namespace App\Filament\Widgets;

use App\Models\Score;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TopStudent extends BaseWidget
{

    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'half';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Score::select('student_id as id', 'student_id', DB::raw('AVG(final_score) as avg_score'))
                    ->groupBy('student_id')
                    ->orderByDesc('avg_score')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('student.name')
                    ->label('Nama'),

                TextColumn::make('student.classRoom.name')
                    ->label('Kelas'),

                TextColumn::make('avg_score')
                    ->label('Rata-rata')
                    ->badge()
                    ->color('success')
                    ->numeric(decimalPlaces: 2)
                    ->alignCenter(),
            ])
            ->heading('🏆 5 Siswa Terbaik')
            ->paginated(false);
    }
}

<?php

namespace App\Filament\Resources;

use App\Exports\ScoreExport;
use App\Filament\Resources\ScoreResource\Pages;
use App\Filament\Resources\ScoreResource\RelationManagers;
use App\Models\Score;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Maatwebsite\Excel\Facades\Excel;
use Override;

class ScoreResource extends Resource
{
    protected static ?string $model = Score::class;
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $navigationLabel = 'Nilai';
    protected static ?string $pluralLabel = 'Input Nilai';
    protected static ?string $navigationGroup = 'Manajemen Nilai';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('student_id')
                    ->label('Siswa')
                    ->relationship('student', 'name')
                    ->searchable()
                    ->preload()
                    ->columnSpanFull()
                    ->required(),

                Select::make('subject_id')
                    ->label('Mata Pelajaran')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload()
                    ->columnSpanFull()
                    ->required(),

                TextInput::make('tugas')
                    ->label('Nilai Tugas')
                    ->live(onBlur: true)
                    ->rules(['required', 'numeric', 'gte:0', 'lte:100'])
                    ->default(0)
                    ->validationMessages([
                        'required' => 'Nilai tugas wajib diisi!',
                        'numeric' => 'Harus berupa angka.',
                        'lte' => 'Nilai maksimal 100',
                        'gte' => 'Nilai minimal 0'
                    ])
                    ->afterStateUpdated(fn(Get $get, Set $set) => self::updateFinalScore($get, $set))
                    ->afterStateHydrated(fn(Get $get, Set $set) => self::updateFinalScore($get, $set)),

                TextInput::make('uts')
                    ->label('Nilai UTS')
                    ->live(onBlur: true)
                    ->rules(['required', 'numeric', 'gte:0', 'lte:100'])
                    ->default(0)
                    ->validationMessages([
                        'required' => 'Nilai UTS wajib diisi!',
                        'numeric' => 'Harus berupa angka.',
                        'lte' => 'Nilai maksimal 100',
                        'gte' => 'Nilai minimal 0'
                    ])
                    ->afterStateUpdated(fn(Get $get, Set $set) => self::updateFinalScore($get, $set))
                    ->afterStateHydrated(fn(Get $get, Set $set) => self::updateFinalScore($get, $set)),

                TextInput::make('uas')
                    ->label('Nilai UAS')
                    ->live(onBlur: true)
                    ->default(0)
                    ->rules(['required', 'numeric', 'gte:0', 'lte:100'])
                    ->validationMessages([
                        'required' => 'Nilai UAS wajib diisi!',
                        'numeric' => 'Harus berupa angka.',
                        'lte' => 'Nilai maksimal 100',
                        'gte' => 'Nilai minimal 0'
                    ])
                    ->afterStateUpdated(fn(Get $get, Set $set) => self::updateFinalScore($get, $set))
                    ->afterStateHydrated(fn(Get $get, Set $set) => self::updateFinalScore($get, $set)),

                Placeholder::make('final_preview')
                    ->label('Nilai Akhir')
                    ->content(function (Get $get): string {
                        $tugas = (float) $get('tugas') ?: 0;
                        $uts = (float) $get('uts') ?: 0;
                        $uas = (float) $get('uas') ?: 0;
                        $final = round(($tugas * 0.3) + ($uts * 0.3) + ($uas * 0.4), 2);
                        $keterangan = $final >= 75 ? '✅ LULUS' : '❌ REMEDIAL';
                        return number_format($final, 2) . " - {$keterangan}";
                    })
                    ->columnSpanFull(),

                Hidden::make('teacher_id')
                    ->default(fn() => auth()->id()),

                Hidden::make('final_score')
            ])
            ->columns(3);
    }

    public static function updateFinalScore(Get $get, Set $set): void
    {
        $tugas = ((float) $get('tugas') ?? 0);
        $uts = ((float) $get('uts') ?? 0);
        $uas = ((float) $get('uas') ?? 0);

        $final = round(($tugas * 0.3) + ($uts * 0.3) + ($uas * 0.4), 2);

        $set('final_score', $final);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('subject.name')
                    ->label('Mata Pelajaran')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('tugas')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('uts')
                    ->label('UTS')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('uas')
                    ->label('UAS')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('final_score')
                    ->label('Final Skor')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(fn($record) => $record->final_score >= 75 ? 'LULUS' : 'REMEDIAL')
                    ->color(fn(string $state): string => match ($state) {
                        'LULUS' => 'success',
                        'REMEDIAL' => 'danger'
                    }),
                // TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status_filter')
                    ->label('Status Nilai')
                    ->options([
                        'lulus' => 'Lulus',
                        'remedial' => 'Remedial'
                    ])
                    ->query(function (Builder $query, array $data) {
                        return match ($data['value'] ?? null) {
                            'lulus' => $query->where('final_score', '>=', 75),
                            'remedial' => $query->where('final_Score', '<', 75),
                            default => $query
                        };
                    }),
            ])
            ->headerActions([
                Action::make('export_scores')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        return Excel::download(new ScoreExport, 'data-nilai.xlsx');
                    })
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScores::route('/'),
            'create' => Pages\CreateScore::route('/create'),
            'edit' => Pages\EditScore::route('/{record}/edit'),
        ];
    }

    #[Override]
    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();
        if ($user->isAdmin()) return true;
        return $record->teacher_id === $user->id;
    }

    #[Override]
    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();
        if ($user->isAdmin()) return true;
        return $record->teacher_id === $user->id;
    }

    #[Override]
    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return parent::getEloquentQuery();
        }
        return parent::getEloquentQuery()->where('teacher_id', $user->id);
    }
}

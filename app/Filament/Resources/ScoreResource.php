<?php

namespace App\Filament\Resources;

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
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ScoreResource extends Resource
{
    protected static ?string $model = Score::class;
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $navigationLabel = 'Nilai';
    protected static ?string $pluralLabel = 'Input Nilai';

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
                    ->minValue(0)
                    ->maxValue(100)
                    ->live(onBlur: true)
                    ->required()
                    ->numeric()
                    ->afterStateUpdated(fn(Get $get, Set $set) => self::updateFinalScore($get, $set))
                    ->default(0),

                TextInput::make('uts')
                    ->label('Nilai UTS')
                    ->minValue(0)
                    ->maxValue(100)
                    ->live(onBlur: true)
                    ->required()
                    ->numeric()
                    ->afterStateUpdated(fn(Get $get, Set $set) => self::updateFinalScore($get, $set))
                    ->default(0),

                TextInput::make('uas')
                    ->label('Nilai UAS')
                    ->minValue(0)
                    ->maxValue(100)
                    ->live(onBlur: true)
                    ->required()
                    ->numeric()
                    ->afterStateUpdated(fn(Get $get, Set $set) => self::updateFinalScore($get, $set))
                    ->default(0),

                Placeholder::make('final_preview')
                    ->label('Nilai Akhir')
                    ->content(function (Get $get): string {
                        $tugas = $get('tugas') ?? 0;
                        $uts = $get('uts') ?? 0;
                        $uas = $get('uas') ?? 0;
                        $final = ($tugas * 0.3) + ($uts * 0.3) + ($uas * 0.4);
                        $keterangan = $final >= 75 ? '✅ LULUS' : '❌ REMEDIAL';
                        return number_format($final, 2) . " - {$keterangan}";
                    })
                    ->columnSpanFull(),

                Hidden::make('final_score')
                    ->default(0.00),
            ])
            ->columns(3);
    }

    public static function updateFinalScore(Get $get, Set $set): void
    {
        $tugas = (float) ($get('tugas') ?? '0');
        $uts = (float) ($get('uts') ?? '0');
        $uas = (float) ($get('uas') ?? '0');
        $final = ($tugas * 0.3) + ($uts * 0.3) + ($uas * 0.4);

        $set('final_score', $final);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('subject.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tugas')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('uts')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('uas')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('final_score')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
}

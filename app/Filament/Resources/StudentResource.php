<?php

namespace App\Filament\Resources;

use App\Exports\StudentExport;
use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Maatwebsite\Excel\Facades\Excel;
use Override;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Siswa';
    protected static ?string $pluralLabel = 'Data Siswa';
    protected static ?string $navigationGroup = 'Data Master';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nis')
                    ->label('NIS')
                    ->disabled()
                    ->rules(['required'])
                    ->unique(ignoreRecord: true)
                    ->dehydrated(true)
                    ->validationAttribute('NIS')
                    ->maxLength(20),

                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->rules(['required', 'string'])
                    ->maxLength(255),

                Select::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan'
                    ])
                    ->rules(['required']),

                Select::make('class_id')
                    ->label('Kelas')
                    ->relationship('classRoom', 'name')
                    ->searchable()
                    ->rules(['required']),

                FileUpload::make('photo')
                    ->label('Foto')
                    ->image()
                    ->directory('students-photos')
                    ->maxSize(1024)
                    ->columnSpanFull()
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(fn() => 'https://ui-avatars.com/api/?name=' . urlencode('?') . '&color=fff&background=6366f1'),

                TextColumn::make('nis')
                    ->label('NIS')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('gender')
                    ->label('JK')
                    ->badge()
                    ->color(fn(string $state): string => $state === 'L' ? 'info' : 'danger'),

                TextColumn::make('classRoom.name')
                    ->label('Kelas')
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
                SelectFilter::make('class_id')
                    ->label('kelas')
                    ->relationship('classRoom', 'name'),

                SelectFilter::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ])

            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                Action::make('download_raport')
                    ->label('Raport')
                    ->icon('heroicon-o-printer')
                    ->color('warning')
                    ->url(fn($record) => route('raport.download', $record->id))
                    ->openUrlInNewTab()
            ])
            ->headerActions([
                Action::make('export_students')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        return Excel::download(new StudentExport, 'data-siswa.xlsx');
                    })
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }

    #[Override]
    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    #[Override]
    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    #[Override]
    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }
}

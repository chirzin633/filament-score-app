<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Models\Student;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Override;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    #[Override]
    protected function fillForm(): void
    {
        $this->form->fill([
            'nis' => $this->generateNis()
        ]);
    }

    #[Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['nis'])) {
            $data['nis'] = $this->generateNis();
        }
        return $data;
    }

    private function generateNis(): string
    {
        $date = Carbon::now()->format('Ymd');

        $lastStudent = Student::where('nis', 'like', $date . '%')->orderBy('nis', 'desc')->first();

        if ($lastStudent) {
            $lastNumber = (int) substr($lastStudent->nis, -2);
            $newNumber = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '01';
        }

        return $date . $newNumber;
    }

    #[Override]
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    #[Override]
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Student has been created successfully.';
    }
}

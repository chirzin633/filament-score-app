<?php

namespace App\Filament\Widgets;

use App\Models\ClassRoom;
use App\Models\Score;
use App\Models\Student;
use App\Models\Subject;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalStudents = Student::count();
        $totalClasses = ClassRoom::count();
        $totalSubjects = Subject::count();

        $remedialCount = Score::where('final_score', '<', 75)->distinct('student_id')->count('student_id');

        return [
            Stat::make('Total Siswa', $totalStudents)
                ->description('Siswa terdaftar')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary')
                ->chart([7, 3, 5, 8, 10, 12, $totalStudents]),

            Stat::make('Total Kelas', $totalClasses)
                ->description('Kelas aktif')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success')
                ->chart([3, 4, 3, 5, 4, 6, $totalClasses]),

            Stat::make('Mata Pelajaran', $totalSubjects)
                ->description('Mapel tersedia')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('warning')
                ->chart([5, 7, 6, 8, 7, 9, $totalSubjects]),

            Stat::make('Butuh Remedial', $remedialCount)
                ->description('Siswa remedial')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger')
                ->chart([2, 5, 3, 4, 2, 1, $remedialCount])

        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RaportController extends Controller
{
    public function download(Student $student): Response
    {
        $scores = $student->scores()->with('subject')->get();

        $avgScore = $scores->avg('final_score') ?? 0;
        $classRoom = $student->classRoom;

        $pdf = Pdf::loadView('raport', [
            'student' => $student,
            'scores' => $scores,
            'avgScore' => round($avgScore, 2),
            'classRoom' => $classRoom,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("raport-{$student->nis}-{$student->name}.pdf");
    }
}
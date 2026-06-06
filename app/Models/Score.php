<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

class Score extends Model
{
    /** @use HasFactory<\Database\Factories\ScoreFactory> */
    use HasFactory;

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    #[Override]
    protected static function booted(): void
    {
        static::creating(function (Score $score) {
            if (auth()->check() && empty($score->teacher_id)) {
                $score->teacher_id = auth()->id();
            }
        });
    }
}

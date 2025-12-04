<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\User;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'is_completed',
        'priority',
        'due_date',
        'user_id',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'due_date' => 'datetime',
    ];

    // Append computed attributes to the model's array form
    protected $appends = ['days_remaining', 'due_status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Returns the number of days from today to the due_date (signed).
     * Positive -> due in the future (days remaining)
     * Zero -> due today
     * Negative -> overdue (days since due)
     */
    public function getDaysRemainingAttribute()
    {
        if (! $this->due_date) {
            return null;
        }

        $due = $this->due_date->copy()->startOfDay();
        $now = Carbon::now()->startOfDay();

        // diffInDays with absolute=false returns signed difference
        return $now->diffInDays($due, false);
    }

    /**
     * Friendly due status text for display in UI
     */
    public function getDueStatusAttribute()
    {
        if (! $this->due_date) {
            return '-';
        }

        $dateFormatted = $this->due_date->format('d/m/Y');
        $days = $this->days_remaining;

        if ($days === null) {
            return "Vence: {$dateFormatted}";
        }

        if ($days > 1) {
            return "Vence em {$dateFormatted} (Faltam {$days} dias)";
        }

        if ($days === 1) {
            return "Vence em {$dateFormatted} (Falta 1 dia)";
        }

        if ($days === 0) {
            return "Vence hoje ({$dateFormatted})";
        }

        // overdue
        return "Venceu em {$dateFormatted} (Há " . abs($days) . " dias)";
    }
}

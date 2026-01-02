<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class ReviewSeason extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'review_seasons';

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
        'set_by_user_id',
        'created_by_id',
        'updated_by_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    protected $appends = ['range_label'];

    /* ─────────────────────────────────────────────────────────────
     |  Relationships
     * ───────────────────────────────────────────────────────────── */

    public function setBy()
    {
        return $this->belongsTo(User::class, 'set_by_user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public function fceerBatches()
    {
        return $this->hasMany(FceerBatch::class, 'review_season_id');
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'review_season_id');
    }

    /* ─────────────────────────────────────────────────────────────
     |  Accessors
     * ───────────────────────────────────────────────────────────── */

    /**
     * Get a formatted label for the date range (e.g., "Mar 2026 - Jun 2026").
     */
    public function getRangeLabelAttribute(): string
    {
        if (!$this->start_date || !$this->end_date) {
            return 'No dates set';
        }
        
        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);

        return $start->format('M Y') . ' – ' . $end->format('M Y');
    }

    /* ─────────────────────────────────────────────────────────────
     |  Scopes
     * ───────────────────────────────────────────────────────────── */

    /**
     * Scope to only include the active review season.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /* ─────────────────────────────────────────────────────────────
     |  Static Methods
     * ───────────────────────────────────────────────────────────── */

    /**
     * Get the currently active review season.
     */
    public static function getActive(): ?self
    {
        return static::active()->first();
    }

    /* ─────────────────────────────────────────────────────────────
     |  Instance Methods
     * ───────────────────────────────────────────────────────────── */

    /**
     * Check if a given date falls within this review season.
     */
    public function isDateWithinSeason($date): bool
    {
        if (!$this->start_date || !$this->end_date) {
            return true; // If no dates set, all dates are valid
        }
        
        $check = Carbon::parse($date)->startOfDay();

        return $check->gte($this->start_date) && $check->lte($this->end_date);
    }

    /**
     * Get all weekend dates (Saturday + Sunday) within this review season.
     *
     * @return array<string> Array of Y-m-d formatted date strings.
     */
    public function getWeekendDates(): array
    {
        if (!$this->start_date || !$this->end_date) {
            return []; // Return empty if no dates set
        }
        
        $dates = [];
        $current = $this->start_date->copy();
        $end = $this->end_date;

        while ($current->lte($end)) {
            if ($current->isSaturday() || $current->isSunday()) {
                $dates[] = $current->format('Y-m-d');
            }
            $current->addDay();
        }

        return $dates;
    }

    /**
     * Get weekend dates for a specific month within this season.
     *
     * @param int $year
     * @param int $month
     * @return array<string>
     */
    public function getWeekendDatesForMonth(int $year, int $month): array
    {
        $monthStart = Carbon::create($year, $month, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        // If season dates are null, return all weekends in the month
        if (!$this->start_date || !$this->end_date) {
            $dates = [];
            $current = $monthStart->copy();
            while ($current->lte($monthEnd)) {
                if ($current->isSaturday() || $current->isSunday()) {
                    $dates[] = $current->format('Y-m-d');
                }
                $current->addDay();
            }
            return $dates;
        }

        // Clamp to season boundaries
        $start = $monthStart->lt($this->start_date) ? $this->start_date->copy() : $monthStart;
        $end = $monthEnd->gt($this->end_date) ? $this->end_date->copy() : $monthEnd;

        $dates = [];
        $current = $start->copy();

        while ($current->lte($end)) {
            if ($current->isSaturday() || $current->isSunday()) {
                $dates[] = $current->format('Y-m-d');
            }
            $current->addDay();
        }

        return $dates;
    }

    /**
     * Set this review season as active and deactivate all others.
     */
    public function setAsActive(?int $userId = null): bool
    {
        // Deactivate all other seasons
        static::where('id', '!=', $this->id)->update(['is_active' => false]);

        // Activate this one
        $this->is_active = true;
        if ($userId) {
            $this->set_by_user_id = $userId;
        }

        return $this->save();
    }

    /**
     * Check if a date is a valid attendance date (weekend + within season).
     */
    public function isValidAttendanceDate($date): bool
    {
        $check = Carbon::parse($date);

        return $this->isDateWithinSeason($check) && ($check->isSaturday() || $check->isSunday());
    }
}

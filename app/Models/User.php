<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'remember_token',
        'created_by',
        'deleted_by_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function role()
    {
        return $this->belongsTo(UserRole::class, 'role_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(self::class, 'created_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(self::class, 'deleted_by_id');
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function uploadedAttachments()
    {
        return $this->hasMany(Attachment::class, 'uploaded_by_id');
    }

    public function highschoolRecords()
    {
        return $this->hasMany(HighschoolRecord::class);
    }

    public function highschoolSubjectRecords()
    {
        return $this->hasMany(HighschoolSubjectRecord::class);
    }

    public function educationalRecords()
    {
        return $this->hasMany(EducationalRecord::class);
    }

    public function professionalCredentials()
    {
        return $this->hasMany(ProfessionalCredential::class);
    }

    public function committeeMemberships()
    {
        return $this->hasMany(CommitteeMembership::class);
    }

    public function subjectTeachers()
    {
        return $this->hasMany(SubjectTeacher::class);
    }

    public function classroomResponsibilities()
    {
        return $this->hasMany(ClassroomResponsibility::class);
    }

    public function fceerProfile()
    {
        return $this->hasOne(FceerProfile::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'addresses';

    protected $fillable = [
        'house_number',
        'block_number',
        'street',
        'barangay_id',
        'city_id',
        'province_id',
    ];

    protected $appends = ['full_address'];

    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->house_number,
            $this->block_number,
            $this->street,
            optional($this->barangay)->name,
            optional($this->city)->name,
            optional($this->province)->name,
        ]);

        return implode(', ', $parts) ?: null;
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}


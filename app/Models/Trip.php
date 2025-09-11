<?php

namespace App\Models;

use App\Enums\TripStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;
    protected $fillable = ['company_id','driver_id','vehicle_id','starts_at','ends_at','status'];
    protected $casts = [
        'starts_at'=>'datetime',
        'ends_at'=>'datetime',
        'status' => TripStatus::class,

    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    // scope for overlapping window
    // for this query to work right, assume clock is 24 format, since I am not saving time is am or pm
    public function scopeOverlapping($q, $start, $end)
    {
        return $q->where('starts_at', '<', $end)
            ->where('ends_at', '>', $start);
    }

    public function scopeActive($q)
    {
        return $q->where('status', TripStatus::Active);
    }

    public function scopeNotCancelled($q)
    {
        return $q->where('status','<>',TripStatus::Cancelled);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;
    protected $fillable = ['company_id','plate_number','model'];
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }
    public function drivers()
    {
        return $this->belongsToMany(Driver::class, 'trips')
            ->withPivot(['starts_at', 'ends_at', 'status'])
            ->groupBy('drivers.id');
    }

    /*
    public function drivers()
    {
        return $this->trips()->groupBy('driver_id');
    }
    */
}

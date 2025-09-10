<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;
    protected $fillable = ['company_id','name','license_number'];
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

   public function vehicles()
    {
        // Drivers connect to vehicles through trips
        return $this->belongsToMany(Vehicle::class, 'trips')
            ->withPivot(['starts_at', 'ends_at', 'status'])
            ->groupBy('vehicles.id');
    }

    /*
    public function vehicles()
    {
        return $this->trips()->groupBy('vehicle_id');
    }
    */
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number',
        'description',
        'location_id',
    ];

    /**
     * The contacts that belong to the phone.
     */
    public function contacts()
    {
        return $this->belongsToMany(Contact::class);
    }

    /**
     * Get the postalCode that owns the address.
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

}

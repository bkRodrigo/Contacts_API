<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'addresses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'street_address',
        'description',
        'latitude',
        'longitude',
        'postalcode_id',
        'city_id',
        'state_id',
        'country_id',
    ];

    /**
     * Get the contacts associated with this address.
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * Get the postalCode that owns the address.
     */
    public function postalCode()
    {
        return $this->belongsTo(PostalCode::class, 'postalcode_id');
    }

    /**
     * Get the postalCode that owns the address.
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the postalCode that owns the address.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the postalCode that owns the address.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}

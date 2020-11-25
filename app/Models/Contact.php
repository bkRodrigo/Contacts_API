<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'avatar',
        'email',
        'birthday',
        'company_id',
        'address_id',
    ];

    /**
     * The phones that belong to the contact.
     */
    public function phones()
    {
        return $this->belongsToMany(Phone::class);
    }

    /**
     * Get the company that owns the contact.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the address that owns the contact.
     */
    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}

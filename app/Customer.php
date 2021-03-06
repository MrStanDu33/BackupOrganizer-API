<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'siren',
        'logo',
        'address_street_number',
        'address_street_name',
        'address_zip_code',
        'address_city',
        'address_country',
        'address_billing',
        'tva_number',
        'website',
        'source',
        'referent_name',
        'referent_email',
        'referent_number',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];
}

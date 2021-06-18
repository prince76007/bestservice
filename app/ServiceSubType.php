<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceSubType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'provider_name', 'image', 'price', 'description', 'status','service_type_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
         'created_at', 'updated_at'
    ];
}

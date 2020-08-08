<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BackupFrequency extends Model
{
    /**
     * The name of the table.
     *
     * @var array
     */
    protected $table = 'backup_frequencies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'value',
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

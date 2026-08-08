<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * An electoral ward, keyed by its ONS code. Reference data imported alongside
 * the postcodes — see the postcodes:import command.
 */
#[Fillable(['code', 'name'])]
class Ward extends Model
{
    protected $primaryKey = 'code';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;
}

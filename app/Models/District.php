<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * A local authority district — the town or city a postcode sits in — keyed by
 * its ONS code. Reference data imported alongside the postcodes.
 */
#[Fillable(['code', 'name'])]
class District extends Model
{
    protected $primaryKey = 'code';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;
}

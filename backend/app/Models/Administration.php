<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Administration extends Model
{
    protected $guarded = [];
    const UPDATED_AT = null;

    public function administratable()
    {
        return $this->morphTo();
    }
}

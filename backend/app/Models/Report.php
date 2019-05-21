<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $guarded = [];
    const UPDATED_AT = null;
    
    public function reportable()
    {
        return $this->morphTo();
    }
}

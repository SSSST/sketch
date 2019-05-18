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

    public function administrator()
    {
        return $this->belongsTo(User::class, 'administrator_id')->select('id', 'name', 'title_id');
    }

    public function administratee()
    {
        return $this->belongsTo(User::class, 'administratee_id')->select('id', 'name', 'title_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Administration extends Model
{
    protected $guarded = [];

    public function administrator()
    {
        $this->belongsTo(User::class, 'administrator_id')->select('id', 'name', 'title_id');
    }
}

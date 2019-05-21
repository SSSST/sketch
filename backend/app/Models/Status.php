<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Status extends Model
{
    use Traits\VoteTrait, SoftDeletes;

    protected $guarded = [];
    protected $dates = ['deleted_at'];

    const UPDATED_AT = null;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id','name','title_id');
    }
    /**
    * Get all of the owning attachable models.
    */
    public function attachable()
    {
        return $this->morphTo();
    }

    public function administrations()
    {
        return $this->morphMany('App\Models\Administration', 'administratable');
    }

    public function reports()
    {
        return $this->morphMany('App\Models\Report', 'reportable');
    }
}

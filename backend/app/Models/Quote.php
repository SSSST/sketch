<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use Traits\VoteTrait;
    protected $guarded = [];
    const UPDATED_AT = null;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id','name','title_id');
    }
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->select('id','name', 'title_id');
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

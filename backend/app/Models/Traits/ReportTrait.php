<?php

namespace App\Models\Traits;

use Carbon\Carbon;
use Auth;

trait ReportTrait
{
    public function reports()
    {
        return $this->morphMany('App\Models\Report', 'reportable');
    }
}

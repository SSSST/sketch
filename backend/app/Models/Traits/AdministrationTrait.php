<?php

namespace App\Models\Traits;

use Carbon\Carbon;
use Auth;

trait AdministrationTrait
{
    public function administrations()
    {
        return $this->morphMany('App\Models\Administration', 'administratable');
    }
}

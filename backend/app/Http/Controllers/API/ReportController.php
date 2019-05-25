<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReport;
use App\Http\Resources\ReportResource;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function report(StoreReport $form)
    {
        $report = $form->generate();
        return response()->success([
            'report' => new ReportResource($report),
        ]);
    }
}

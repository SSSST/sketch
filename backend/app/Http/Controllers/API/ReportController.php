<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReport;
use App\Http\Resources\ReportResource;
use App\Http\Resources\PostResource;
use App\Models\Report;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function report(StoreReport $form)
    {
        $data = $form->generate();
        return response()->success([
            'report' => new ReportResource($data['report']),
            'post' => new PostResource($data['post']),
        ]);
    }

    public function review(Report $report, StoreReport $form)
    {
        $data = $form->reviewReport($report);
        return response()->success([
            'report' => new ReportResource($data['report']),
            'post' => new PostResource($data['post']),
        ]);
    }
}

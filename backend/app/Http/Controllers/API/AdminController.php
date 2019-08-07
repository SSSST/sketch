<?php

namespace App\Http\Controllers\API;

use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Thread;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdministration;
use App\Http\Resources\AdministrationResource;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function manage(StoreAdministration $form)
    {
        $administration = $form->generate();
        return response()->success([
            'administration' => new AdministrationResource($administration),
        ]);
    }

    public function updateToReport(Request $request)
    {
        if(!auth('api')->user()->isAdmin()) {abort(403);}
        $this->validate($request,[
            $request->thread_id => 'unique:system_variables',
        ]);
        $type = $request->type;
        $thread_id = $request->thread_id;

        if(Thread::find($thread_id)->channel_id == 8) { // 如果帖子属于违规举报板块则可设置为当前举报楼
            DB::transaction(function() use($thread_id, $type) {
                DB::table('system_variables')->where('report_thread_type', $type)->where('is_valid', 1)->update(['is_valid' => 0]);
                DB::table('system_variables')->insert([
                    'report_thread_id' => $thread_id,
                    'report_thread_type' => $type,
                    'is_valid' => 1,
                    'created_at' => Carbon::now(),
                ]);
            });

            return response()->success(200);
        }

        return response()->error('config.412', 412);
    }
}

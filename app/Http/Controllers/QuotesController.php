<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Quote;
use Auth;
use Carbon\Carbon;

class QuotesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function store(Request $request)
    {
        $data = [];
        $this->validate($request, [
            'quote' => 'required|string|max:80|unique:quotes',
        ]);
        $data['quote'] = request('quote');
        $data['user_id'] = auth()->id();
        $data['notsad'] = request('notsad')? 1:0;
        if((Auth::user()->admin)){
            $data['approved']=true;
            $data['reviewed']=true;
        }
        if (request('anonymous')){
            $this->validate($request, [
                'majia' => 'required|string|max:10',
            ]);
            $data['anonymous'] = true;
            $data['majia'] = request('majia');
        }
        if ((Auth::user()->admin)||(!$this->isDuplicateQuote($data))){
            $quote = Quote::create($data);
        }else{
            return back()->with('warning','请不要重复提交题头！');
        }
        return back()->with('success','成功提交题头！');
    }

    public function isDuplicateQuote($data)
    {
        $last_quote = Quote::where('user_id', auth()->id())
        ->orderBy('id', 'desc')
        ->first();
        return (!empty($last_quote) && ((strcmp($last_quote->quote, $data['quote']) === 0)||($last_quote->created_at>Carbon::today()->subHours(2)->toDateTimeString())));
    }

    public function create(){
        return view('quotes.create');
    }
    public function edit(){

    }
    public function update(){

    }
    public function xianyu(Quote $quote, Request $request){
        $user = Auth::user();
        if ($user->xianyu<=0){
            return back()->with("info", "抱歉，您的咸鱼不足");
        }
        DB::transaction(function () use ($quote, $user){
            $quote->increment('xianyu');
            $user->decrement('xianyu');
            $user->increment('jifen', 2);
            $user->increment('experience_points', 2);
            $quote->creator->increment('xianyu');
            $quote->creator->increment('jifen',2);
            $quote->creator->increment('experience_points', 2);
        });
        return back()->with("success", "您已成功投掷咸鱼~");
    }
}

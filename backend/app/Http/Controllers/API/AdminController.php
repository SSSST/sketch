<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
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
}

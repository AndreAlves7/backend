<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ViewAuthUserController extends Controller
{
    //

    public function show_me(Request $request)
    {
        return response()->json($request->user());
    }
}

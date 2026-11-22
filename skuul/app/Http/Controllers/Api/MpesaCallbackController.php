<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Payments\MpesaDarajaService;
use Illuminate\Http\Request;

class MpesaCallbackController extends Controller
{
    use \App\Http\Controllers\Concerns\AuthorizesTenant;

    public function __invoke(Request $request, MpesaDarajaService $mpesa)
    {
        $mpesa->handleCallback($request->all());
        return response()->json(['ResultCode'=>0,'ResultDesc'=>'Accepted']);
    }
}


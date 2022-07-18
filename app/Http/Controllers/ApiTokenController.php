<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use App\ApiToken;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Nahid\EnvatoPHP\Facades\Envato;
use Illuminate\Support\Facades\Crypt;

class ApiTokenController extends Controller
{


    public function index()
    {
        $value = Crypt::encryptString(config('api.name',"qsdsdqsdqsd"));
        return response()->json($value, 200);
    }

    public function decryptString()
    {
        try {

        
            $value = Crypt::encryptString(config('api.name'));

            $decrypted = Crypt::decryptString($value);
        
            $data = ['status' => 200, 'body' => $decrypted];

            return response()->json($data, 200);
        } catch (DecryptException $e) {
            //
        }
        return response()->json($$data, 200);
    }

}
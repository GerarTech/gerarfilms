<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Laravel\Passport\Client;

class RegisterController extends Controller
{
    use IssueTokenTrait;

    private $client;

    public function __construct()
    {
        $this->client = Client::find(2);
        $this->middleware('doNotCacheResponse');

    }

    public function register(Request $request)
    {

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'string',
            'min:8',  
            'regex:/[a-z]/',      
            'regex:/[A-Z]/',    
            'regex:/[0-9]/',    
            'regex:/[@$!%*#?&]/']);

        $user = User::create([
            'name' => request('name'),
            'email' => request('email'),
            'avatar' => $request->root() . '/api/avatars/image/avatar_default.png',
            'premuim' => false,
            'manual_premuim' => false,
            'password' => bcrypt(request('password'))


        ]);

        return $this->issueToken($request, 'password');

    }
}

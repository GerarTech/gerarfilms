<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Client;
use Illuminate\Support\Carbon;
use App\User;
use App\Plan;
use App\Movie;
use App\Profile;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Razorpay\Api\Api;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

class LoginController extends Controller
{


    const MESSAGE = "successfully updated";

    use IssueTokenTrait;

    private $client;

    public function __construct()
    {
        $this->client = Client::find(2);
        $this->middleware('doNotCacheResponse');

    }

    public function loginFacebook(Request $request)
    {
        $provider = "facebook"; // or $request->input('provider_name') for multiple providers
    
        // get the provider's user. (In the provider server)
        $providerUser = Socialite::driver($provider)->userFromToken($request->token);
        // check if access token exists etc..
        // search for a user in our server with the specified provider id and provider name
        $user = User::where('provider_name', $provider)->where('provider_id', $providerUser->id)->first();
        // if there is no record with these data, create a new user
        if($user == null){
            $user = User::create([
                'name' => $providerUser->name,
                'email' => $providerUser->email,
                'avatar' => $providerUser->avatar,
                'premuim' => false,
                'manual_premuim' => false,
                'provider_name' => $provider,
                'provider_id' => $providerUser->id
            ]);
        

            $tokenResult = $user->createToken('facebook');
            return \response()->json([
                'token_type'    =>  'Bearer',
                'expires_in'    =>  $tokenResult->token->expires_at->diffInSeconds(Carbon::now()),
                'access_token'  =>  $tokenResult->accessToken,
                'refresh_token'  =>  $tokenResult->refreshToken,
                'type'          =>  'facebook'
            ]);
        }
        $tokenResult = $user->createToken('facebook');
        return \response()->json([
            'token_type'    =>  'Bearer',
            'expires_in'    =>  $tokenResult->token->expires_at->diffInSeconds(Carbon::now()),
            'access_token'  =>  $tokenResult->accessToken,
            'type'          =>  'facebook'
        ]);

    }




    public function loginGoogle(Request $request)
    {



        
        $provider = "google"; 
    

        $access_token = Socialite::driver($provider)->getAccessTokenResponse($request->token);
        $providerUser = Socialite::driver($provider)->userFromToken($access_token['access_token']);

        $user = User::where('provider_name', $provider)->where('provider_id', $providerUser->id)->first();
        // if there is no record with these data, create a new user
        if($user == null){
            $user = User::create([
                'name' => $providerUser->name,
                'email' => $providerUser->email,
                'avatar' => $providerUser->avatar,
                'premuim' => false,
                'manual_premuim' => false,
                'provider_name' => $provider,
                'provider_id' => $providerUser->id
            ]);
        

            $tokenResult = $user->createToken('google');
            return \response()->json([
                'token_type'    =>  'Bearer',
                'expires_in'    =>  $tokenResult->token->expires_at->diffInSeconds(Carbon::now()),
                'access_token'  =>  $tokenResult->accessToken,
                'refresh_token'  =>  $tokenResult->refreshToken,
                'type'          =>  'google'
            ]);
        }

        $tokenResult = $user->createToken('google');
        return \response()->json([
            'token_type'    =>  'Bearer',
            'expires_in'    =>  $tokenResult->token->expires_at->diffInSeconds(Carbon::now()),
            'access_token'  =>  $tokenResult->accessToken,
            'type'          =>  'google'
        ]);
        

    }

    public function login(Request $request)
    {

        $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);

        return $this->issueToken($request, 'password');

    }



    public function createNewProfile(Request $request) {

        $user = Auth()->user();

        
        $movieVideo = new Profile();
        $movieVideo->name = $request->name;
        $movieVideo->user_id = $user->id;
        $movieVideo->image = $user->image;
        $movieVideo->fill($request->all());
        $movieVideo->save();

        
        $data = [
            'status' => 200,
            self::MESSAGE,
            'body' => $user
        ];

        return response()->json($data, $data['status']);

    }


    public function refresh(Request $request)
    {
        $this->validate($request, [
            'refresh_token' => 'required'
        ]);

        return $this->issueToken($request, 'refresh_token');


    }

    public function update(Request $request,Plan $plan)
    {

       
        $accessToken = Auth::user()->token();


        DB::table('users')
            ->where('id', $accessToken->user_id)
            ->update(

                array( 
                    "premuim" => true,
                    "pack_name" => request('pack_name'),
                    "expired_in" => Carbon::now()->addDays(request('pack_duration'))
    
   )

            );
            


        return response()->json([], 204);

    }





    public function updatePaypal(Request $request,Plan $plan)
    {


        $this->validate($request, [
            'transaction_id' => 'required',
            'pack_id' => 'required',
            'pack_name' => 'required',
            "type" => 'required',
            "pack_duration" => 'required']);
        
       
        $accessToken = Auth::user()->token();


        DB::table('users')
            ->where('id', $accessToken->user_id)
            ->update(

                array( 
                    "premuim" => true,
                    "transaction_id" => request('transaction_id'),
                    "pack_id" => request('pack_id'),
                    "pack_name" => request('pack_name'),
                    "type" => 'paypal',
                    "expired_in" => Carbon::now()->addDays(request('pack_duration'))));
            
   return response()->noContent();

    }




    public function addPlanToUser(Request $request)
    {

        $stripeToken = $request->get('stripe_token');
        $user = Auth::user();
        $user->newSubscription($request->get('stripe_plan_id'), $request->get('stripe_plan_price'))->create($stripeToken);

        $accessToken = Auth::user()->token();

        DB::table('users')
        ->where('id', $accessToken->user_id)
        ->update(

            array( 
                "premuim" => true,
                "pack_name" => request('pack_name'),
                "pack_id" => request('stripe_plan_id'),
                "start_at" => request('start_at'),
                "type" => 'stripe',
                "expired_in" => Carbon::now()->addDays(request('pack_duration')))

        );

        return response()->json($user, 204);

    }



    public function cancelSubscription(Request $request)
    {

 
       $user = Auth::user();

        $accessToken = Auth::user()->token();

        $packId = Auth::user()->pack_id;
        
        $user->subscription($packId)->cancelNow();


        DB::table('users')
        ->where('id', $accessToken->user_id)
        ->update(

            array( 
                "premuim" => false,
                "pack_name" => "",
                "start_at" => "",
                "type" => "",
                "expired_in" => Carbon::now())

        );

         return response()->json($user, 204);

    }


    public function cancelSubscriptionPaypal(Request $request)
    {

 
       $user = Auth::user();

        $accessToken = Auth::user()->token();

        DB::table('users')
        ->where('id', $accessToken->user_id)
        ->update(

            array( 
                "premuim" => false,
                "pack_name" => "",
                "start_at" => "",
                "type" => "",
                "expired_in" => Carbon::now())

        );

        return $request->user();

    }



    public function profile(Request $request)
    {

        $user = User::find(1);
        $user->subscribedTo("1");

        return response()->json($user, 204);

    }



    public function update_avatar(Request $request){

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
        ]);

        $user = Auth::user();

        if (Storage::disk('avatars')->exists($user->avatar)) {

         Storage::delete($user->avatar);
  
        }

        $avatarName = $user->id.'_avatar.'.request()->avatar->getClientOriginalExtension();

        $request->avatar->storeAs('avatars',$avatarName);

        $user->avatar = $request->root() . '/api/avatars/image/' . $avatarName;
        $user->save();

        return response()->json([], 204);

    }


    public function user (Request $request){
        
        return $request->user();
     }


    public function logout(Request $request)
    {

        $accessToken = Auth::user()->token();

        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $accessToken->id)
            ->update(['revoked' => true]);

        $accessToken->revoke();

        return response()->json([], 204);

    }


    public function deleteUser(Request $request)
    {

        $accessToken = Auth::user();

        if ($accessToken != null) {

         User::find($accessToken->id)->delete();

            $data = ['status' => 200, 'message' => 'successfully deleted',];
        } else {
            
            $data = ['status' => 400, 'message' => 'could not be deleted',];
        }

        
        return response()->json($data, 200);


    }



    public function getImg($filename)
    {

        $image = Storage::disk('avatars')->get($filename);

        $mime = Storage::disk('avatars')->mimeType($filename);

        return (new Response($image, 200))->header('Content-Type', $mime);
    }
}

<?php

namespace App\Http\Middleware;
use Jenssegers\Agent\Agent;
use Closure;
use Illuminate\Support\Str;
use App\Setting;
class Token extends Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
     
    
    public function handle($request, Closure $next, ...$guards)
    {

  

       $token = $request->header('packagename', '');
       if (Str::startsWith($token, 'Bearer ')) {
       return Str::substr($token,  7);
       }

       
       $token2 = $request->header('APPURL', '');
       if (Str::startsWith($token2, 'Bearer ')) {
       return Str::substr($token2,  7);
       }



       $token3 = $request->header('ANDROIDAPPSIGNATURE', '');
       if (Str::startsWith($token3, 'Bearer ')) {
       return Str::substr($token3,  7);
       }


    $agent = new Agent();
   
    
    //if($agent->isAndroidOS() && env('API_KEY') == $request->code && $request->hasHeader('packagename') && $token == env('PACKAGE_NAME') &&
    
  //  $request->hasHeader('APPURL') && $token2 == env('APP_URL')) {

    return $next($request);
    
 //  } else {

 //   return response()->json("Unauthorized", 401);
            
 //}
    

   }
}

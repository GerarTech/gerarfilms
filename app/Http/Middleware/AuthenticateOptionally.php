<?php

namespace App\Http\Middleware;
use Closure;

class AuthenticateOptionally extends Authenticate
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
        
        //$token = $request->bearerToken();

       // if (env('API_KEY') == $request->code && $request->hasHeader('Authorization') && $token == env('TOKEN')){

    
        return $next($request);
        
    
      //  }else {

      //      return response()->json("Unauthorized", 401);
            
    //    }


    }
}

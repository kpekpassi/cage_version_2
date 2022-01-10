<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Closure;

class VerifyConnexion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      if(Cookie::get('id_user')!=0){
        return $next($request);
      }
        return redirect('/login');
     }
}

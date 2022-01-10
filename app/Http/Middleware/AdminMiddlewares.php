<?php

namespace App\Http\Middleware;

use Closure;

class AdminMiddlewares
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
		if (Cookie::get('id_user')!=0 && Cookie::get('id_user')==1){
            return $next($request);
		}
		
            return redirect('/login');
        
    }
}

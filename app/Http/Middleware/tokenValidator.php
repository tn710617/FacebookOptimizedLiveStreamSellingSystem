<?php

namespace App\Http\Middleware;

use App\Helpers;
use App\Token;
use Closure;

class tokenValidator {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Token::checkIfTokenReceived($request))
        {
            return Helpers::result(false, 'The token is required', 401);
        }

        $whetherTokenExists = Token::checkIfTokenExists($request);

        if ($whetherTokenExists > 0)
        {
            if (!Token::checkIfTokenExpired($request))
            {
                return $next($request);
            }
            Token::where('name', $request->bearerToken())->delete();

            return Helpers::result(false, 'The token has expired', 401);
        }

        return Helpers::result(false, 'The token is invalid', 401);
    }
}

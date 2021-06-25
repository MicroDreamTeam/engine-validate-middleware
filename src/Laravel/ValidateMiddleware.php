<?php

namespace Itwmw\Validate\Middleware\Laravel;

use Closure;
use Illuminate\Http\Request;
use Itwmw\Validate\Middleware\ValidateMiddlewareConfig;

class ValidateMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        list($controller, $scene) = explode('@', $request->route()->getActionName());

        $validator = ValidateMiddlewareConfig::instance()->getValidateFactory()->getValidate($controller, $scene);

        if ($validator) {
            $data = $validator->check($request->all());
            $request->offsetSet('__validate__data__', $data);
        }

        return $next($request);
    }
}

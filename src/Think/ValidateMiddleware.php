<?php

namespace Itwmw\Validate\Middleware\Think;

use Closure;
use Itwmw\Validate\Middleware\ValidateMiddlewareConfig;
use think\App;
use think\Request;
use think\Response;
use W7\Validate\Exception\ValidateException;

class ValidateMiddleware
{
    protected $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return Response
     * @throws ValidateException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $controller = $request->controller();
        $scene      = $request->action();
        $rule       = $request->rule();

        $suffix          = $rule->config('controller_suffix') ? 'Controller' : '';
        $controllerLayer = $rule->config('controller_layer') ?: 'controller';

        $class = $this->app->parseClass($controllerLayer, $controller . $suffix);

        $validator = ValidateMiddlewareConfig::instance()->getValidateFactory()->getValidate($class, $scene);

        if ($validator) {
            $data                        = $validator->check($request->all());
            $request->__validate__data__ = $data;
        }

        return $next($request);
    }
}

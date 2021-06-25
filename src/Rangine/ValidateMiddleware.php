<?php

namespace Itwmw\Validate\Middleware\Rangine;

use Itwmw\Validate\Middleware\ValidateMiddlewareConfig;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use W7\Facade\Context;
use W7\Core\Middleware\MiddlewareAbstract;
use W7\Http\Message\Server\Request;

class ValidateMiddleware extends MiddlewareAbstract
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $controller = $request->route->getController();
        $scene      = $request->route->getAction();

        $validator = ValidateMiddlewareConfig::instance()->getValidateFactory()->getValidate($controller, $scene);

        if ($validator) {
            $data = array_merge([], $request->getQueryParams(), $request->getParsedBody(), $request->getUploadedFiles());
            $data = $validator->check($data);
            /** @var Request $request */
            $request = $request->withAttribute('__validate__data__', $data);
            Context::setRequest($request);
        }
        
        return $handler->handle($request);
    }
}

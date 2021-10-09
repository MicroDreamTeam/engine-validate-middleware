<?php

namespace Itwmw\Validate\Middleware;

use W7\Validate\Exception\ValidateRuntimeException;
use W7\Validate\Validate;

class ValidateFactory implements ValidateFactoryInterface
{
    public function getValidate(string $controller, string $scene = '')
    {
        $haveLink                 = false;
        $validate                 = '';
        $validateMiddlewareConfig = ValidateMiddlewareConfig::instance();

        $validateLink = $validateMiddlewareConfig->getValidateLink($controller);
        if (!empty($validateLink)) {
            # Specified validator for the specified controller method
            if (isset($validateLink[$scene]) || isset($validateLink['!__other__'])) {
                if (isset($validateLink['!__other__'])) {
                    $method = '!__other__';
                } else {
                    $method = $scene;
                }

                # Specifies the validation scenario for the specified validator
                if (is_array($validateLink[$method])) {
                    if (count($validateLink[$method]) >= 2) {
                        $validate = $validateLink[$method][0];
                        $scene    = $validateLink[$method][1];
                        $haveLink = true;
                    }
                } else {
                    $validate = $validateLink[$method];
                    $haveLink = true;
                }
            }
        }

        if (false === $haveLink) {
            # Handles controllers with specified paths
            $controllerPath = '';
            $validatePath   = '';
            foreach ($validateMiddlewareConfig->getAutoValidatePath() as $_controllerPath => $_validatePath) {
                if (false !== strpos($controller, $_controllerPath)) {
                    $controllerPath = $_controllerPath;
                    $validatePath   = $_validatePath;
                    break;
                }
            }
            if (empty($controllerPath)) {
                return false;
            }

            $validate           = str_replace($controllerPath, '', $controller);
            $_namespace         = explode('\\', $validate);
            $regex              = $validateMiddlewareConfig->getControllerRegexFormat();
            $validatorClassName = $validateMiddlewareConfig->getValidatorFormat();
            if (preg_match($regex, array_pop($_namespace), $mc)) {
                for ($i = 0; $i < count($mc); $i++) {
                    $validatorClassName = str_replace('$' . $i, $mc[$i], $validatorClassName);
                }
            }
            $_namespace = implode('\\', $_namespace);
            $validate   = $validatePath . $_namespace . (!empty($_namespace) ? '\\' : '') . $validatorClassName;
        }

        if (class_exists($validate)) {
            if (is_subclass_of($validate, Validate::class)) {
                /** @var Validate $validator */
                $validator = new $validate();
                $validator->scene($scene);
                return $validator;
            }

            throw new ValidateRuntimeException("The given 'Validate' " . $validate . ' has to be a subtype of W7\Validate\Validate');
        }
        return false;
    }
}

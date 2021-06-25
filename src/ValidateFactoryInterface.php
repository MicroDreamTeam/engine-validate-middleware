<?php

namespace Itwmw\Validate\Middleware;

use W7\Validate\Validate;

interface ValidateFactoryInterface
{
    /**
     * Get validator based on controller
     *
     * @param string $controller
     * @param string $scene
     * @return false|Validate
     */
    public function getValidate(string $controller, string $scene = '');
}

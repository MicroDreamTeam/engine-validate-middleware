<?php

/**
 * WeEngine System
 *
 * (c) We7Team 2021 <https://www.w7.cc>
 *
 * This is not a free software
 * Using it under the license terms
 * visited https://www.w7.cc for more details
 */

use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Http\Request;
use W7\Validate\Support\Storage\ValidateCollection;

if (!function_exists('get_validate_data')) {
    /**
     * Get the result after verification
     *
     * @param ServerRequestInterface|Request $request Request Example
     * @return ValidateCollection
     */
    function get_validate_data($request = null): ValidateCollection
    {
        if ($request instanceof ServerRequestInterface) {
            $data = $request->getAttribute('__validate__data__');
        } elseif ($request instanceof Request) {
            $data = $request->offsetGet('__validate__data__');
        } else {
            $data = [];
        }

        return validate_collect($data);
    }
}

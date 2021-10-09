<?php

use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Http\Request;
use think\Request as ThinkRequest;
use Itwmw\Validation\Support\Collection\Collection;

if (!function_exists('get_validate_data')) {
    /**
     * Get the result after verification
     *
     * @param ServerRequestInterface|Request $request Request Example
     * @return Collection
     */
    function get_validate_data($request = null): Collection
    {
        if ($request instanceof ServerRequestInterface) {
            $data = $request->getAttribute('__validate__data__');
        } elseif ($request instanceof Request) {
            $data = $request->offsetGet('__validate__data__');
        } elseif ($request instanceof ThinkRequest) {
            $data = $request->__validate__data__;
        } else {
            $data = [];
        }

        return validate_collect($data);
    }
}

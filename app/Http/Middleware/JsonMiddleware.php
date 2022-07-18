<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class JsonMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function __construct($data = null, $status = 200, $headers = [], $options = 0)
    {
        $headers = ['Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'utf-8'];
        $options = JSON_UNESCAPED_UNICODE;
        $this->encodingOptions = $options;

        parent::__construct($data, $status, $headers);
    }
}

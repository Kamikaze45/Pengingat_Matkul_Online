<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class KernelVisitor extends HttpKernel
{
    /**
     * Global HTTP middleware stack khusus visitor
     */
    protected $middleware = [
        \App\Http\Middleware\LogVisitor::class, // Middleware khusus visitor

    ];

    /**
     * Tidak perlu grup web atau api lainnya
     */
    protected $middlewareGroups = [];

    /**
     * Tidak perlu route middleware lain
     */
    protected $routeMiddleware = [];
}

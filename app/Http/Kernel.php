// app/Http/Kernel.php
protected $routeMiddleware = [
    // ...
    'feature' => \App\Http\Middleware\CheckFeature::class,
];


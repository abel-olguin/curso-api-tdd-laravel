<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
                  ->withRouting(
                      api: __DIR__ . '/../routes/api.php',
                      commands: __DIR__ . '/../routes/console.php',
                      health: '/up',
                      apiPrefix: 'api/v1',
                  )
                  ->withMiddleware(function (Middleware $middleware) {
                      //
                  })
                  ->withExceptions(function (Exceptions $exceptions) {
                      $exceptions->render(function (\Illuminate\Validation\ValidationException $throwable) {
                          return jsonResponse(status: 422, message: $throwable->getMessage(),
                              errors: $throwable->errors());
                      });
                  })->create();

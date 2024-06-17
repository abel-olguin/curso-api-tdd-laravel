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
                      $middleware->api(prepend: [
                          \App\Http\Middleware\ForceJsonResponseMiddleware::class
                      ]);
                  })
                  ->withExceptions(function (Exceptions $exceptions) {
                      $exceptions->render(function (\Illuminate\Validation\ValidationException $throwable) {
                          return jsonResponse(status: 422, message: $throwable->getMessage(),
                              errors: $throwable->errors());
                      });

                      $exceptions->render(function (\Illuminate\Auth\AuthenticationException $throwable) {
                          return jsonResponse(status: 401, message: $throwable->getMessage());
                      });

                      $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $throwable) {
                          return jsonResponse(status: 404, message: $throwable->getMessage());
                      });
                  })->create();

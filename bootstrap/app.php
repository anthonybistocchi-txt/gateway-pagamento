<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
    $exceptions->render(function (\Throwable $e, Request $request) {
        if ($request->is('api/*') || $request->wantsJson()) {
            
            if ($e instanceof ValidationException) {
                return response()->json([
                    'status' => false,
                    'error' => 'Invalid data provided.',
                    'messages' => $e->errors(),
                ], 422);
            }

            if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                return response()->json([
                    'status' => false,
                    'error' => 'The requested resource was not found.'
                ], 404);
            }

            $statusCode = method_exists($e, 'getCode') ? $e->getCode() : 500;
            $message = $e->getMessage() ?: 'Internal server error.';

            return response()->json([
                'status' => false,
                'error' => $message
            ], $statusCode);
        }
    });
});

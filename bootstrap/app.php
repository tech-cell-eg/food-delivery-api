<?php

use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use App\Http\Middleware\ForceJsonResponse;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use App\Http\Middleware\IsCheif;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('api', ForceJsonResponse::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (JWTException $e, Request $request) {
            if ($request->is('api/*')) {
              return response()->json([
                'message' => 'Token not provided or invalid',
              ], 401);
            }
        });
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => (bool) false,
                    'message' => "Page not found",
                    'data'    => $e->getMessage(),
                ], 404);
            }
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => (bool) false,
                    'message' => "Record not found",
                    'data'    => $e->getMessage(),
                ], 404);
            }
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => (bool) false,
                    'message' => "Method not allowed",
                    'data'    => $e->getMessage(),
                ], 405);
            }
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => (bool) false,
                    'message' => "Validation error",
                    'data'    => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => (bool) false,
                    'message' => "Unauthorized access",
                    'data'    => $e->getMessage(),
                ], 401);
            }
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => (bool) false,
                    'message' => "Unexpected error",
                    'data'    => $e->getMessage(),
                ], 500);
            }
        });
    })->create();


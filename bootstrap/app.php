<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Http\Middleware\ForceJsonResponse;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    web: __DIR__ . '/../routes/web.php',
    api: __DIR__ . '/../routes/api.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware) {
    // Apply ForceJsonResponse to all API routes
    $middleware->appendToGroup('api', ForceJsonResponse::class);
    // Apply CheckToken to all API routes

    // Register middleware aliases

  })
  ->withExceptions(function (Exceptions $exceptions) {
    $exceptions->render(function (JWTException $e, Request $request) {
      if ($request->is('api/*')) {
        return response()->json([
          'message' => 'Token not provided or invalid',
        ], 401);
      }
    });

    // Handle MissingTokenException for API
    $exceptions->render(function (AuthenticationException $e, Request $request) {
      if ($request->is('api/*')) {
        return response()->json([
          'success' => false,
          'error' => [
            'code' => 'UNAUTHENTICATED',
            'message' => 'Authentication required',
          ]
        ], 401);
      }
    });

    // Handle AuthenticationException for API
    $exceptions->render(function (TokenInvalidException $e, Request $request) {
      return response()->json([
        'success' => false,
        'error' => [
          'code' => 'TOKEN_INVALID',
          'message' => 'Invalid token'
        ]
      ], 401);
    });

    // Handle RouteNotFoundException for API
    $exceptions->render(function (NotFoundHttpException $e, Request $request) {
      if ($request->is('api/*')) {
        return response()->json([
          'status' => 'error',
          'message' => 'Endpoint not found',
          'error_code' => 'ENDPOINT_NOT_FOUND'
        ], 404);
      }
    });
    $exceptions->render(function (UnauthorizedHttpException $e, Request $request) {
      if ($request->is('api/*') || $request->expectsJson()) {
        return response()->json([
          'status' => false,
          'message' => 'Unauthorized: Token is missing or invalid',
          'error_details' => $e->getMessage()
        ], 401);
      }
    });
  })->create();

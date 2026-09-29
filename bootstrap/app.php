<?php

use App\Exceptions\ConduitException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

$formatErrors = static function (array $errors): array {
    $formatted = [];

    foreach ($errors as $field => $messages) {
        $formatted[Str::afterLast($field, '.')] = $messages;
    }

    return $formatted;
};

$conduitValidationErrors = static function (ValidationException $exception) use ($formatErrors): array {
    $errors = $formatErrors($exception->errors());

    foreach ($exception->validator->failed() as $field => $rules) {
        $field = Str::afterLast($field, '.');

        if (array_key_exists('Required', $rules)) {
            $errors[$field] = ["can't be blank"];
        } elseif (array_key_exists('Unique', $rules)) {
            $errors[$field] = ['has already been taken'];
        }
    }

    return $errors;
};

$resourceName = static function (Request $request, ?NotFoundHttpException $exception = null): string {
    $previous = $exception?->getPrevious();

    if ($previous instanceof ModelNotFoundException) {
        return match (class_basename($previous->getModel())) {
            'Article' => 'article',
            'Comment' => 'comment',
            'User' => 'profile',
            default => 'resource',
        };
    }

    if ($request->route('comment') !== null) {
        return 'comment';
    }

    if ($request->is('api/articles/*')) {
        return 'article';
    }

    if ($request->is('api/profiles/*')) {
        return 'profile';
    }

    return 'resource';
};

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(
            fn (Request $request): ?string => $request->is('api/*') ? null : '/login',
        );
    })
    ->withExceptions(function (Exceptions $exceptions) use ($conduitValidationErrors, $resourceName): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request): bool => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (ConduitException $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return response()->json(['errors' => $exception->errors], $exception->status);
        });

        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return response()->json(['errors' => ['token' => ['is missing']]], 401);
        });

        $exceptions->render(function (AuthorizationException $exception, Request $request) use ($resourceName) {
            if (! $request->is('api/*')) {
                return null;
            }

            return response()->json(['errors' => [$resourceName($request) => ['forbidden']]], 403);
        });

        $exceptions->render(function (AccessDeniedHttpException $exception, Request $request) use ($resourceName) {
            if (! $request->is('api/*')) {
                return null;
            }

            return response()->json(['errors' => [$resourceName($request) => ['forbidden']]], 403);
        });

        $exceptions->render(function (ValidationException $exception, Request $request) use ($conduitValidationErrors) {
            if (! $request->is('api/*')) {
                return null;
            }

            $hasUniqueFailure = collect($exception->validator->failed())
                ->contains(fn (array $rules): bool => array_key_exists('Unique', $rules));
            $status = $request->isMethod('post') && $request->is('api/users') && $hasUniqueFailure
                ? 409
                : 422;

            return response()->json(['errors' => $conduitValidationErrors($exception)], $status);
        });

        $exceptions->render(function (ModelNotFoundException $exception, Request $request) use ($resourceName) {
            if (! $request->is('api/*')) {
                return null;
            }

            return response()->json(['errors' => [$resourceName($request) => ['not found']]], 404);
        });

        $exceptions->render(function (NotFoundHttpException $exception, Request $request) use ($resourceName) {
            if (! $request->is('api/*')) {
                return null;
            }

            return response()->json(['errors' => [$resourceName($request, $exception) => ['not found']]], 404);
        });
    })->create();

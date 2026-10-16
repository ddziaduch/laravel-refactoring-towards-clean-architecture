<?php

use App\Exceptions\ConduitException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
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

// Modules translate their own "not found" and "forbidden" cases into ConduitException.
// This only names the resource of a URL that matched no route at all.
$resourceName = static function (Request $request): string {
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

        $exceptions->render(function (NotFoundHttpException $exception, Request $request) use ($resourceName) {
            if (! $request->is('api/*')) {
                return null;
            }

            return response()->json(['errors' => [$resourceName($request) => ['not found']]], 404);
        });
    })->create();

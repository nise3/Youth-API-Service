<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Throwable $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {

        if ($e instanceof HttpResponseException) {
            $errors = [
                "code" => Response::HTTP_BAD_REQUEST,
                "message" => "Invalid Request Format",
            ];
            return \response()->json($errors);

        } elseif ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            $errors = [
                "code" => Response::HTTP_NOT_FOUND,
                "message" => "404 not found",
            ];
            return \response()->json($errors);
        } elseif ($e instanceof AuthorizationException) {
            $errors = [
                "code" => Response::HTTP_FORBIDDEN,
                "message" => "Don't have permission to access",
            ];
            return \response()->json($errors);
        }
        elseif ($e instanceof ValidationException) {
            $errors = [
                "code" => Response::HTTP_FORBIDDEN,
                "message" => "Validation Fail",
                'errors' => $e->errors()
            ];
            return \response()->json($errors);
        }
        elseif ($e instanceof BindingResolutionException) {
            $errors = [
                "code" => Response::HTTP_INTERNAL_SERVER_ERROR,
                "message" => "Unable to resolve dependency",
            ];
            return \response()->json($errors);
        }
        else if ($e instanceof Exception) {
            $errors = [
                "code" => Response::HTTP_INTERNAL_SERVER_ERROR,
                "message" => "Internal Server Error!",
            ];
            return \response()->json($errors);
        }

        return parent::render($request, $e);
    }
}

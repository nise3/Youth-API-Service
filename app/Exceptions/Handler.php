<?php

namespace App\Exceptions;

//use ErrorException;
use BadMethodCallException;
use ErrorException;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use ParseError;
use PDOException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use TypeError;

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
        RequestException::class
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Throwable $exception
     * @return void
     *
     * @throws Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return Response|JsonResponse
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof HttpResponseException) {
            $errors['_response_status'] = [
                'success' => false,
                "code" => ResponseAlias::HTTP_BAD_REQUEST,
                "message" => "Invalid Request Format",
                "query_time" => 0
            ];
            return response()->json($errors);

        } elseif ($e instanceof RequestException) {
            $errors['_response_status'] = [
                'success' => false,
                "code" => ResponseAlias::HTTP_CONFLICT,
                "query_time" => 0
            ];
            if ($e->getCode() == 409) {
                $errors["_response_status"]["message"] =$e->getMessage();
            }
            return response()->json($errors);
        }elseif ($e instanceof PDOException) {
            $errors['_response_status'] = [
                'success' => false,
                "code" => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                "message" => "PDO exception",
                "query_time" => 0
            ];
            return response()->json($errors);
        } elseif ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            $errors['_response_status'] = [
                'success' => false,
                "code" => ResponseAlias::HTTP_NOT_FOUND,
                "message" => $e->getMessage(),
                "query_time" => 0
            ];
            return response()->json($errors);
        } elseif ($e instanceof AuthorizationException) {
            $errors['_response_status'] = [
                'success' => false,
                "code" => ResponseAlias::HTTP_UNAUTHORIZED,
                "message" => "Unable to Access",
                "query_time" => 0
            ];
            return response()->json($errors);
        } elseif ($e instanceof ValidationException) {
            $errors['errors'] = $e->errors();
            $errors['_response_status'] = [
                'success' => false,
                "code" => ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                "message" => "validation Error",
                "query_time" => 0
            ];
            return response()->json($errors);
        } elseif ($e instanceof BindingResolutionException) {
            $errors['_response_status'] = [
                'success' => false,
                "code" => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                "message" => "Binding Resolution Error",
                "query_time" => 0
            ];
            return response()->json($errors);
        } elseif ($e instanceof ErrorException) {
            $errors['_response_status'] = [
                'success' => false,
                "code" => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                "message" => "Internal Server Side Error",
                "query_time" => 0
            ];
            return response()->json($errors);
        } elseif ($e instanceof TypeError) {
            $errors['_response_status'] = [
                'success' => false,
                "code" => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                "message" => "Type Error",
                "query_time" => 0
            ];
            return response()->json($errors);
        } elseif ($e instanceof ParseError) {
            $errors['_response_status'] = [
                'success' => false,
                "code" => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                "message" => "Parsing Error",
                "query_time" => 0
            ];
            return response()->json($errors);
        } elseif ($e instanceof BadMethodCallException) {
            $errors['_response_status'] = [
                'success' => false,
                "code" => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                "message" => "Call a Bad Method",
                "query_time" => 0
            ];
            return response()->json($errors);
        } elseif ($e instanceof Exception) {
            $errors['_response_status'] = [
                'success' => false,
                "code" => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                "message" => $e->getMessage(),
                "query_time" => 0
            ];
            return response()->json($errors);
        } else {
            $errors['_response_status'] = [
                'success' => false,
                "code" => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                "message" => "Unknown error",
                "query_time" => 0
            ];
            return response()->json($errors);
        }
    }
}

<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{

    use ApiResponser;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {

        if ($exception instanceof ValidationException){
            return $this->convertValidationExceptionToResponse($exception, $request);
        }
        // hanled the model not found exception
        if ($exception instanceof ModelNotFoundException) {
            $modeName = class_basename($exception->getModel());
            return $this->errorResponse("{$modeName} with specified identifier does not exist.",
                404);
        }
        // handle the authentication Exception
        if ($exception instanceof AuthenticationException){
            return $this->unauthenticated($request, $exception);
        }
        // handles authorization
        if ($exception instanceof AuthorizationException){
            // error code 403 represents unauthorized user
            return $this->errorResponse($exception->getMessage(), 403);
        }
        // handles the case when user requests an api URL with an invalid HTTP request method
        if ($exception instanceof MethodNotAllowedHttpException){
            return $this->errorResponse('The specified method for the request is invalid', 405);
        }
        // handles the case when a user requests for a URL that does not exist
        if ($exception instanceof NotFoundHttpException){
            return $this->errorResponse('The specified URL cannot be found', 404);
        }

        // handles all the possible exceptions that we did not cater for
        if ($exception instanceof HttpException){
            return $this->errorResponse($exception->getMessage(), $exception->getStatusCode());
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    // override the unauthenticated method
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $this->errorResponse('Unauthenticated',401);
    }

    /**
     * Create a response object from the given validation exception.
     *
     * @param  \Illuminate\Validation\ValidationException  $e
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    // converts validations exceptions to responses
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        if ($e->response) {
            return $e->response;
        }
        // get all the validation errors for the exception
        $errors = $e->validator->errors()->getMessages();

        return $this->errorResponse($errors, 422);
    }
}

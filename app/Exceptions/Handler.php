<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, string>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                return $this->handleApiException($request, $e);
            }
        });
    }

    /**
     * Handle API exceptions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleApiException(Request $request, Throwable $e)
    {
        if ($e instanceof HttpExceptionInterface) {
            $statusCode = $e->getStatusCode();
        } else {
            $statusCode = 500; // Internal Server Error by default
        }

        $response = [
            'message' => $e->getMessage() ?: 'Sorry, something went wrong.',
            'line'=>$e->getLine(),
            'file'=>$e->getFile(),
        ];

        // Optionally include the exception's stack trace in development environments
        if (config('app.debug')) {
            $response['trace'] = $e->getTrace();
        }

        return response()->json($response, $statusCode);
    }
}

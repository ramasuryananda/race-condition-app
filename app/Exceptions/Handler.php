<?php

namespace App\Exceptions;

use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
        $this->renderable(function (InvalidBalanceException $e) {
            return response()->json([
                "message"=>"request failed",
                "error" => $e->getMessage()
            ],$e->getCode());
        });
        
        $this->renderable(function (LockTimeoutException $e) {
            $errorMessage = $e->getMessage();
            Log::error("request:failed : $errorMessage");
            return response()->json([
                "message"=>"request failed",
                "error" => "failed storing transaction"
            ],Response::HTTP_BAD_REQUEST);
        });

        $this->renderable(function (Throwable $e){
            $errorMessage = $e->getMessage();
            Log::error("request:failed : $errorMessage");
            return response()->json([
                "message" => "request failed",
                "error" => "some error occurs"
            ]);
        });
    }
}

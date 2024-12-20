<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with custom render methods.
     *
     * @var array
     */
    protected $dontReport = [
        MembershipException::class,
        AdminException::class,
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (MembershipException $e, $request) {
            return $e->render();
        });

        $this->renderable(function (AdminException $e, $request) {
            return $e->render();
        });
    }
}

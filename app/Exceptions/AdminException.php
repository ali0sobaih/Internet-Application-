<?php

namespace App\Exceptions;

use Exception;

class AdminException extends Exception
{
    protected $message;
    protected $code;

    public function __construct(string $message = "User is not an admin of this group!", int $code = 403)
    {
        $this->message = $message;
        $this->code = $code;
        parent::__construct($this->message, $this->code);
    }

    public function render()
    {
        return response()->json([
            'data' => null,
            'message' => $this->message,
            'code' => $this->code
        ]);
    }
}

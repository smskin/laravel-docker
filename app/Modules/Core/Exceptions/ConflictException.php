<?php

namespace App\Modules\Core\Exceptions;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ConflictException extends Exception implements Responsable
{
    public function __construct(string $message = "", int $code = 409, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function toResponse($request): \Illuminate\Http\Response|Response|Application|ResponseFactory
    {
        return response([
            'message' => $this->message
        ], $this->code);
    }
}

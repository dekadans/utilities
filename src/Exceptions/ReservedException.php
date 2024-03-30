<?php

namespace tthe\UtilTool\Exceptions;

use Slim\Exception\HttpSpecializedException;

class ReservedException extends HttpSpecializedException
{
    protected $code = 403;
    protected $message = 'Forbidden';
    protected string $title = 'Forbidden';
    protected string $description = 'This URI is reserved for future use.';
}
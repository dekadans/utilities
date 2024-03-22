<?php

namespace tthe\UtilTool\Exceptions;

use Slim\Exception\HttpSpecializedException;

class HttpPayloadTooLargeException extends HttpSpecializedException
{
    protected $code = 413;
    protected $message = 'Payload Too Large';
    protected string $title = '413 Payload Too Large';
    protected string $description = 'The request body is larger than what is supported by this service.';
}
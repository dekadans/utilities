<?php

namespace tthe\UtilTool\Exceptions;

use Slim\Exception\HttpSpecializedException;

class HttpNotAcceptableException extends HttpSpecializedException
{
    protected $code = 406;
    protected $message = 'Not Acceptable.';
    protected string $title = '406 Not Acceptable';
    protected string $description = 'There\'s no representation that would be acceptable to the user agent.';
}
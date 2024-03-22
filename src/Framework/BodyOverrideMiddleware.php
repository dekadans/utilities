<?php

namespace tthe\UtilTool\Framework;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;

class BodyOverrideMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $streamFactory = new \Slim\Psr7\Factory\StreamFactory();

        $params = $request->getParsedBody();
        $body = $params['_body'] ?? null;

        if ($body) {
            $request = $request
            ->withBody($streamFactory->createStream($body))
            ->withHeader('Content-Type', 'text/plain')
            ->withHeader('Content-Length', strlen($body))
            ->withParsedBody(null);
        }

        return $handler->handle($request);
    }
}
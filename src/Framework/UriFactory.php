<?php

namespace tthe\UtilTool\Framework;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteParserInterface;

readonly class UriFactory
{
    public function __construct(
        private ServerRequestInterface $request,
        private RouteParserInterface $routeParser
    ) {

    }
    
    public function uriForRoute(string $name, array $data): string
    {
        return $this->routeParser->fullUrlFor($this->request->getUri(), $name, $data);
    }
    
    public function xmlSchema(): string
    {
        return $this->uriForRoute('schema', ['format' => 'xml']);
    }
    
     public function jsonSchema(): string
     {
         return $this->uriForRoute('schema', ['format' => 'json']);
     }
}
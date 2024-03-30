<?php

namespace tthe\UtilTool\Serializers;

use Psr\Http\Message\ResponseInterface;
use tthe\UtilTool\ServiceResponse;

class TextSerializer implements SerializerInterface
{
    public const CONTENT_TYPE = 'text/plain';
    private ResponseInterface $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function serialize(ServiceResponse $data): ResponseInterface
    {
        $method = $data->request->getMethod();
        $version = $data->request->getProtocolVersion();
        $uri = $data->request->getRequestTarget();
        $body = $data->body->getRaw();
        
        $textData = "$method $uri HTTP/$version";
        
        foreach ($data->request->getHeaders() as $header => $lines) {
            foreach ($lines as $line) {
                $textData .= "\n$header: $line";
            }
        }
        
        if ($body) {
            $textData .= "\n\n$body";
        }
        
        $this->response->getBody()->write($textData);
        return $this->response
            ->withHeader('Content-Type', self::CONTENT_TYPE);
    }
}
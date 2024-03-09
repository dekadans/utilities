<?php

namespace tthe\UtilTool\Serializers;

use Psr\Http\Message\ResponseInterface;
use tthe\UtilTool\ServiceResponse;

class JsonSerializer implements SerializerInterface
{
    use ArraySerializer;

    public const CONTENT_TYPE = 'application/json';
    private ResponseInterface $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function serialize(ServiceResponse $data): ResponseInterface
    {
        $jsonData = json_encode(
            $this->toArray($data),
            JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
        );
        
        $this->response->getBody()->write($jsonData);
        return $this->response
            ->withHeader('Content-Type', self::CONTENT_TYPE);
    }
}
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
        $textData = $data->httpRepr->generateForText();
        
        $this->response->getBody()->write($textData);
        return $this->response
            ->withHeader('Content-Type', self::CONTENT_TYPE);
    }
}
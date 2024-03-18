<?php

namespace tthe\UtilTool\Serializers;

use Psr\Http\Message\ResponseInterface;
use tthe\UtilTool\ServiceResponse;
use tthe\UtilTool\UriFactory;

class JsonSerializer implements SerializerInterface
{
    use ArraySerializer;

    public const CONTENT_TYPE = 'application/json';
    private ResponseInterface $response;
    private UriFactory $uriFactory;

    public function __construct(ResponseInterface $response, UriFactory $uriFactory)
    {
        $this->response = $response;
        $this->uriFactory = $uriFactory;
    }

    public function serialize(ServiceResponse $data): ResponseInterface
    {
        $links = [
            '_links' => [
                'describedby' => [
                    'href' => $this->uriFactory->jsonSchema(),
                    'title' => 'JSON Schema'
                ]
            ]
        ];

        $jsonData = json_encode(
            array_merge($links, $this->toArray($data)),
            JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
        );
        
        $this->response->getBody()->write($jsonData);
        return $this->response
            ->withHeader('Content-Type', self::CONTENT_TYPE);
    }
}
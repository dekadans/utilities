<?php

namespace tthe\UtilTool\Serializers;

use Psr\Http\Message\ResponseInterface;
use tthe\UtilTool\ServiceResponse;

class JsonSerializer implements SerializerInterface
{
    public const CONTENT_TYPE = 'application/json';
    private ResponseInterface $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function serialize(ServiceResponse $data): ResponseInterface
    {
        $jsonData = [
            'status' => [
                'code' => $data->status->code,
                'message' => $data->status->message
            ],
            'utilities' => [
                'date' => [
                    'iso' => $data->utilities->getTimeIso(),
                    'http' => $data->utilities->getTimeHttp(),
                    'unix' => $data->utilities->getTimeUnix(),
                ],
                'random' => [
                    'uuid' => $data->utilities->getUuid(),
                    'string' => $data->utilities->getPassword(),
                    'phrase' => $data->utilities->getPhrase(),
                    'sentence' => $data->utilities->getPlaceholder(),
                    'bytes' => [
                        'hex' => $data->utilities->getBytesHex(),
                        'int' => $data->utilities->getBytesInt()
                    ]
                ]
            ]
        ];
        
        $this->response->getBody()->write(json_encode($jsonData));
        return $this->response
            ->withHeader('Content-Type', self::CONTENT_TYPE);
    }
}
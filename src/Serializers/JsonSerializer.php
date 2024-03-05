<?php

namespace tthe\UtilTool\Serializers;

use tthe\UtilTool\ServiceResponse;

class JsonSerializer implements SerializerInterface
{
    public const CONTENT_TYPE = 'application/json';
    
    public function serialize(ServiceResponse $response): string
    {
        $data = [
            'status' => [
                'code' => $response->status->code,
                'message' => $response->status->message
            ],
            'utilities' => [
                'date' => [
                    'iso' => $response->utilities->getTimeIso(),
                    'http' => $response->utilities->getTimeHttp(),
                    'unix' => $response->utilities->getTimeUnix(),
                ],
                'random' => [
                    'uuid' => $response->utilities->getUuid(),
                    'string' => $response->utilities->getPassword(),
                    'phrase' => $response->utilities->getPhrase(),
                    'sentence' => $response->utilities->getPlaceholder(),
                    'bytes' => [
                        'hex' => $response->utilities->getBytesHex(),
                        'int' => $response->utilities->getBytesInt()
                    ]
                ]
            ]
        ];
        
        return json_encode($data);
    }

    public function getContentType(): string
    {
        return self::CONTENT_TYPE;
    }
}
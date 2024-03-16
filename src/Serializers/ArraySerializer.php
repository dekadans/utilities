<?php

namespace tthe\UtilTool\Serializers;

use Psr\Http\Message\ServerRequestInterface;
use tthe\UtilTool\RequestBody;
use tthe\UtilTool\ServiceResponse;
use tthe\UtilTool\Status;
use tthe\UtilTool\Utilities;

trait ArraySerializer
{
    private function toArray(ServiceResponse $data): array
    {
        return [
            'status' => $this->serializeStatus($data->status),
            'date' => $this->serializeDate($data->utilities),
            'random' => $this->serializeRandom($data->utilities),
            'request' => $this->serializeRequest($data->request, $data->body)
        ];
    }
    
    private function serializeStatus(Status $status): array
    {
        return [
            'code' => $status->code,
            'message' => $status->message
        ];
    }
    
    private function serializeRequest(ServerRequestInterface $request, RequestBody $body): array
    {
        if ($body->hasBody()) {
            $bodySerialized = [
                'raw' => $body->getRaw(),
                'parsed' => $body->getParsed(),
                'md5' => md5($body->getRaw()),
                'sha1' => sha1($body->getRaw()),
                'sha256' => hash('sha256', $body->getRaw()),
                'base64' => base64_encode($body->getRaw())
            ];
        }
        
        return [
            'method' => $request->getMethod(),
            'headers' => $request->getHeaders(),
            'query' => $request->getQueryParams(),
            'body' => $bodySerialized ?? null
        ];
    }
    
    private function serializeDate(Utilities $utilities): array
    {
        return [
            'iso' => $utilities->getTimeIso(),
            'http' => $utilities->getTimeHttp(),
            'unix' => $utilities->getTimeUnix(),
        ];
    }

    private function serializeRandom(Utilities $utilities): array
    {
        return [
            'uuid' => $utilities->getUuid(),
            'string' => $utilities->getPassword(),
            'phrase' => $utilities->getPhrase(),
            'sentence' => $utilities->getSentences(),
            'bytes' => [
                'hex' => $utilities->getBytesHex(),
                'int' => $utilities->getBytesInt()
            ]
        ];
    }
}
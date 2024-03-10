<?php

namespace tthe\UtilTool\Serializers;

use Psr\Http\Message\ServerRequestInterface;
use tthe\UtilTool\ServiceResponse;
use tthe\UtilTool\Status;
use tthe\UtilTool\Utilities;

trait ArraySerializer
{
    private function toArray(ServiceResponse $data): array
    {
        return [
            'status' => $this->serializeStatus($data->status),
            'request' => $this->serializeRequest($data->request),
            'utilities' => [
                'date' => $this->serializeDate($data->utilities),
                'random' => $this->serializeRandom($data->utilities)
            ]
        ];
    }
    
    private function serializeStatus(Status $status): array
    {
        return [
            'code' => $status->code,
            'message' => $status->message
        ];
    }
    
    private function serializeRequest(ServerRequestInterface $request): array
    {
        $body = $request->getBody()->getContents() ?: null;
        
        if ($body) {
            $parsed = $request->getParsedBody();
            if ($parsed instanceof \SimpleXMLElement) {
                $parsed = json_decode(json_encode($parsed), true);
            }

            $bodySerialized = [
                'raw' => $body,
                'parsed' => $parsed,
                'md5' => md5($body),
                'sha1' => sha1($body),
                'sha256' => hash('sha256', $body)
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
            'sentence' => $utilities->getPlaceholder(),
            'bytes' => [
                'hex' => $utilities->getBytesHex(),
                'int' => $utilities->getBytesInt()
            ]
        ];
    }
}
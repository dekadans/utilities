<?php

namespace tthe\UtilTool\Serializers;

use Psr\Http\Message\ServerRequestInterface;
use tthe\UtilTool\RequestBody;
use tthe\UtilTool\ServiceResponse;
use tthe\UtilTool\Status;
use tthe\UtilTool\Utilities;

trait ArraySerializer
{
    private function toArray(ServiceResponse $data, bool $isXml = false): array
    {
        return [
            'about' => $data->getAbout(),
            'status' => $this->serializeStatus($data->status),
            'date' => $this->serializeDate($data->utilities),
            'random' => $this->serializeRandom($data->utilities),
            'request' => $this->serializeRequest($data->request, $data->body, $isXml)
        ];
    }
    
    private function serializeStatus(Status $status): array
    {
        return [
            'code' => $status->code,
            'message' => $status->message
        ];
    }
    
    private function serializeRequest(ServerRequestInterface $request, RequestBody $body, bool $isXml): array
    {
        if ($body->hasBody()) {
            $raw = $isXml ? ['_cdata' => $body->getRaw()] : $body->getRaw();
            $parsed = $isXml && empty($body->getParsed()) ? $this->xmlNil() : $body->getParsed();

            $bodySerialized = [
                'raw' => $raw,
                'parsed' => $parsed,
                'md5' => md5($body->getRaw()),
                'sha1' => sha1($body->getRaw()),
                'sha256' => hash('sha256', $body->getRaw()),
                'base64' => base64_encode($body->getRaw())
            ];
        } else {
            $bodySerialized = $isXml ? $this->xmlNil() : null;
        }
        
        if (!empty($request->getQueryParams())) {
            $querySerialized = [
                'raw' => $request->getUri()->getQuery(),
                'parsed' => $request->getQueryParams()
            ];
        } else {
            $querySerialized = $isXml ? $this->xmlNil() : null;
        }

        return [
            'method' => $request->getMethod(),
            'headers' => $request->getHeaders(),
            'query' => $querySerialized,
            'body' => $bodySerialized
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

    private function xmlNil(): array
    {
        return ['_attributes' => ['xsi:nil' => 'true']];
    }
}
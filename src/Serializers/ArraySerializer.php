<?php

namespace tthe\UtilTool\Serializers;

use Psr\Http\Message\ServerRequestInterface;
use tthe\UtilTool\RequestBody;
use tthe\UtilTool\ServiceResponse;
use tthe\UtilTool\Status;
use tthe\UtilTool\Utilities;

trait ArraySerializer
{
    private bool $isXml = false;

    private function toArray(ServiceResponse $data): array
    {
        return [
            'about' => $data->getAbout(),
            'status' => $this->serializeStatus($data->status),
            'datetime' => $this->serializeDate($data->utilities),
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
        return [
            'method' => $request->getMethod(),
            'uri' => (string) $request->getUri(),
            'headers' => $this->serializeHeaders($request->getHeaders()),
            'query' => $this->serializeQuery($request),
            'body' => $this->serializeBody($body)
        ];
    }

    private function serializeHeaders(array $headers): array
    {
        if (!$this->isXml) {
            return $headers;
        }

        $xmlHeaders = [];
        foreach ($headers as $name => $lines) {
            foreach ($lines as $line) {
                $xmlHeaders[] = [
                    '_attributes' => [
                        'name' => $name
                    ],
                    '_value' => $line
                ];
            }
        }

        return ['header' => $xmlHeaders];
    }

    private function serializeQuery(ServerRequestInterface $request): ?array
    {
        if (empty($request->getQueryParams())) {
            return $this->null();
        }

        return [
            'raw' => $request->getUri()->getQuery(),
            'parsed' => $request->getQueryParams()
        ];
    }

    private function serializeBody(RequestBody $body): ?array
    {
        if (!$body->hasBody()) {
            return $this->null();
        }

        $raw = $this->isXml ? ['_cdata' => $body->getRaw()] : $body->getRaw();
        return [
            'raw' => $raw,
            'parsed' => $body->getParsed() ?: $this->null(),
            'md5' => md5($body->getRaw()),
            'sha1' => sha1($body->getRaw()),
            'sha256' => hash('sha256', $body->getRaw()),
            'base64' => base64_encode($body->getRaw())
        ];
    }

    private function serializeDate(Utilities $utilities): array
    {
        return [
            'iso' => $utilities->getTimeIso(),
            'http' => $utilities->getTimeHttp(),
            'unix' => $utilities->getTimeUnix(),
            'week' => $utilities->getWeek(),
            'world' => $this->serializeWorldTime($utilities->getWorldTime())
        ];
    }

    private function serializeWorldTime(array $worldTime): array
    {
        if (!$this->isXml) {
            return $worldTime;
        }

        return [
            'time' => array_map(function($data, $tz) {
                return [
                    '_attributes' => [
                        'tz' => $tz,
                        'offset' => $data['offset']
                    ],
                    '_value' => $data['time']
                ];
            }, $worldTime, array_keys($worldTime))
        ];
    }

    private function serializeRandom(Utilities $utilities): array
    {
        return [
            'bool' => $this->isXml ? (int) $utilities->getBool() : $utilities->getBool(),
            'uuid' => $utilities->getUuid(),
            'string' => $utilities->getPassword(),
            'phrase' => $utilities->getPhrase(),
            'color' => $utilities->getRandomColor(),
            'lorem' => $this->serializeLoremIpsum($utilities->getSentences()),
            'bytes' => [
                'hex' => $utilities->getBytesHex(),
                'int' => $utilities->getBytesInt()
            ]
        ];
    }

    private function serializeLoremIpsum(array $sentences): array
    {
        if (!$this->isXml) {
            return $sentences;
        }

        return ['sentence' => $sentences];
    }

    private function null(): ?array
    {
        return $this->isXml ? ['_attributes' => ['xsi:nil' => 'true']] : null;
    }
}
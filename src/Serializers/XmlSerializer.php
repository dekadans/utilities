<?php

namespace tthe\UtilTool\Serializers;

use Psr\Http\Message\ResponseInterface;
use Spatie\ArrayToXml\ArrayToXml;
use tthe\UtilTool\ServiceResponse;

class XmlSerializer implements SerializerInterface
{
    use ArraySerializer;

    public const CONTENT_TYPE = 'application/xml';
    private ResponseInterface $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function serialize(ServiceResponse $data): ResponseInterface
    {
        $arrayData = $this->toArray($data);

        $rawData = $arrayData['request']['body']['raw'] ?? null;
        if ($rawData) {
            $arrayData['request']['body']['raw'] = ['_cdata' => $rawData];
        }

        $xmlData = new ArrayToXml(
            $arrayData,
            'response',
            true,
            'UTF-8'
        );

        $this->response->getBody()->write($xmlData->prettify()->toXml());
        return $this->response
            ->withHeader('Content-Type', self::CONTENT_TYPE);
    }
}
<?php

namespace tthe\UtilTool\Serializers;

use Psr\Http\Message\ResponseInterface;
use Spatie\ArrayToXml\ArrayToXml;
use tthe\UtilTool\ServiceResponse;
use tthe\UtilTool\UriFactory;

class XmlSerializer implements SerializerInterface
{
    use ArraySerializer;

    public const CONTENT_TYPE = 'application/xml';
    public const NAMESPACE = 'tag:tthe.se,2024:projects:utiltool:xmlns';
    private ResponseInterface $response;
    private UriFactory $uriFactory;

    public function __construct(ResponseInterface $response, UriFactory $uriFactory)
    {
        $this->response = $response;
        $this->uriFactory = $uriFactory;
    }

    public function serialize(ServiceResponse $data): ResponseInterface
    {
        $root = [
            'rootElementName' => 'response',
            '_attributes' => [
                'xmlns' => self::NAMESPACE,
                'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                'xsi:schemaLocation' => self::NAMESPACE . ' ' . $this->uriFactory->xmlSchema(),
            ]
        ];

        $arrayData = $this->toArray($data);

        $rawData = $arrayData['request']['body']['raw'] ?? null;
        if ($rawData) {
            $arrayData['request']['body']['raw'] = ['_cdata' => $rawData];
        }

        $xmlData = new ArrayToXml(
            $arrayData,
            $root,
            true,
            'UTF-8'
        );

        $this->response->getBody()->write($xmlData->prettify()->toXml());
        return $this->response
            ->withHeader('Content-Type', self::CONTENT_TYPE);
    }
}
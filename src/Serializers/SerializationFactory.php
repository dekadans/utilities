<?php

namespace tthe\UtilTool\Serializers;

use Negotiation\Negotiator;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use tthe\UtilTool\Exceptions\HttpNotAcceptableException;
use tthe\UtilTool\Framework\UriFactory;

class SerializationFactory
{
    public static function available(): array
    {
        return [
            JsonSerializer::CONTENT_TYPE => 'JSON version',
            HtmlSerializer::CONTENT_TYPE => 'HTML version',
            TextSerializer::CONTENT_TYPE => 'Text version (Only HTTP request inspection)',
            XmlSerializer::CONTENT_TYPE => 'XML version',
            YamlSerializer::CONTENT_TYPE => 'YAML version'
        ];
    }

    public static function make(
        RequestInterface $request,
        ResponseInterface $response,
        UriFactory $uriFactory
    ): SerializerInterface {
        $accept = $request->getQueryParams()['_accept']
            ?? implode(',', $request->getHeader('Accept'))
            ?: '*/*';

        $mediaType = (new Negotiator())->getBest($accept, array_keys(self::available()));

        if ($mediaType === null) {
            throw new HttpNotAcceptableException($request);
        }

        return match ($mediaType->getValue()) {
            HtmlSerializer::CONTENT_TYPE => new HtmlSerializer($response),
            JsonSerializer::CONTENT_TYPE => new JsonSerializer($response),
            TextSerializer::CONTENT_TYPE => new TextSerializer($response),
            XmlSerializer::CONTENT_TYPE => new XmlSerializer($response, $uriFactory),
            YamlSerializer::CONTENT_TYPE => new YamlSerializer($response),
            default => throw new \RuntimeException('Invalid serialization'),
        };
    }
}
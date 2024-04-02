<?php

namespace tthe\UtilTool\Serializers;

use Negotiation\Negotiator;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use tthe\UtilTool\Exceptions\HttpNotAcceptableException;
use tthe\UtilTool\Framework\UriFactory;

class SerializationFactory
{
    private static function available(): array
    {
        return [
            JsonSerializer::CONTENT_TYPE,
            HtmlSerializer::CONTENT_TYPE,
            TextSerializer::CONTENT_TYPE,
            XmlSerializer::CONTENT_TYPE,
            YamlSerializer::CONTENT_TYPE
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

        $mediaType = (new Negotiator())->getBest($accept, self::available());

        if ($mediaType === null) {
            throw new HttpNotAcceptableException($request);
        }

        return match ($mediaType->getValue()) {
            HtmlSerializer::CONTENT_TYPE => new HtmlSerializer($response),
            JsonSerializer::CONTENT_TYPE => new JsonSerializer($response, $uriFactory),
            TextSerializer::CONTENT_TYPE => new TextSerializer($response),
            XmlSerializer::CONTENT_TYPE => new XmlSerializer($response, $uriFactory),
            YamlSerializer::CONTENT_TYPE => new YamlSerializer($response),
            default => throw new \RuntimeException('Invalid serialization'),
        };
    }
}
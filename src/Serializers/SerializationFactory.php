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
            'html' => HtmlSerializer::CONTENT_TYPE,
            'json' => JsonSerializer::CONTENT_TYPE,
            'xml' => XmlSerializer::CONTENT_TYPE
        ];
    }

    public static function make(
        RequestInterface $request,
        ResponseInterface $response,
        UriFactory $uriFactory
    ): SerializerInterface {
        $supported = self::available();
        $inQuery = $request->getQueryParams()['format'] ?? null;
        
        if (isset($supported[$inQuery])) {
            $selected = $supported[$inQuery];
        } else {
            $negotiator = new Negotiator();
            $accept = implode(',', $request->getHeader('Accept')) ?: '*/*';
            $mediaType = $negotiator->getBest($accept, $supported);
            
            if ($mediaType === null) {
                throw new HttpNotAcceptableException($request);
            }
            
            $selected = $mediaType->getValue();
        }

        return match ($selected) {
            HtmlSerializer::CONTENT_TYPE => new HtmlSerializer($response),
            JsonSerializer::CONTENT_TYPE => new JsonSerializer($response, $uriFactory),
            XmlSerializer::CONTENT_TYPE => new XmlSerializer($response, $uriFactory),
            default => throw new \RuntimeException('Invalid serialization'),
        };
    }
}
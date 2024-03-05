<?php

namespace tthe\UtilTool\Serializers;

use tthe\UtilTool\ServiceResponse;

class HtmlSerializer implements SerializerInterface
{
    public const CONTENT_TYPE = 'text/html; charset=UTF-8';

    public function serialize(ServiceResponse $response): string
    {
        return 'html';
    }

    public function getContentType(): string
    {
        return self::CONTENT_TYPE;
    }
}
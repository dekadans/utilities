<?php

namespace tthe\UtilTool\Serializers;

use tthe\UtilTool\ServiceResponse;

interface SerializerInterface
{
    public function getContentType(): string;
    
    public function serialize(ServiceResponse $response): string;
}
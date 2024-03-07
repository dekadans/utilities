<?php

namespace tthe\UtilTool\Serializers;

use Psr\Http\Message\ResponseInterface;
use tthe\UtilTool\ServiceResponse;

interface SerializerInterface
{
    public function serialize(ServiceResponse $data): ResponseInterface;
}
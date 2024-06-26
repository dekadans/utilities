<?php

namespace tthe\UtilTool\Serializers;

use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Yaml\Yaml;
use tthe\UtilTool\ServiceResponse;

class YamlSerializer implements SerializerInterface
{
    use ArraySerializer;

    public const CONTENT_TYPE = 'application/yaml';
    private ResponseInterface $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function serialize(ServiceResponse $data): ResponseInterface
    {
        $yamlData = Yaml::dump($this->toArray($data), 3);
        
        $this->response->getBody()->write($yamlData);
        return $this->response
            ->withHeader('Content-Type', self::CONTENT_TYPE)
            ->withHeader('Content-Disposition', 'inline; filename="utilities.yaml"');
    }
}
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
        $yamlData = "%YAML 1.2\n---\n" . Yaml::dump($this->toArray($data), 3);
        
        $this->response->getBody()->write($yamlData);
        return $this->response
            ->withHeader('Content-Type', 'application/yaml')
            ->withHeader('Content-Disposition', 'filename="utilities.yaml"');
    }
}
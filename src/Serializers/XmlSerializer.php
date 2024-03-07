<?php

namespace tthe\UtilTool\Serializers;

use Psr\Http\Message\ResponseInterface;
use Slim\Views\PhpRenderer;
use tthe\UtilTool\ServiceResponse;

class XmlSerializer implements SerializerInterface
{
    public const CONTENT_TYPE = 'application/xml';
    private ResponseInterface $response;
    private PhpRenderer $renderer;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
        $this->renderer = new PhpRenderer('../templates');
    }

public function serialize(ServiceResponse $data): ResponseInterface
{
    return $this->renderer->render($this->response, 'xml_view.php', ['data' => $data])
            ->withHeader('Content-Type', self::CONTENT_TYPE);
}
}
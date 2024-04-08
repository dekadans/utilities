<?php

namespace tthe\UtilTool\Serializers;

use Psr\Http\Message\ResponseInterface;
use Slim\Views\PhpRenderer;
use tthe\UtilTool\ServiceResponse;

class HtmlSerializer implements SerializerInterface
{
    public const CONTENT_TYPE = 'text/html';
    private ResponseInterface $response;
    private PhpRenderer $renderer;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
        $this->renderer = new PhpRenderer('../templates');
    }

    public function serialize(ServiceResponse $data): ResponseInterface
    {
        return $this->renderer->render($this->response, "html_view.php", $data->asViewData())
            ->withHeader('Content-Type', self::CONTENT_TYPE);
    }
}
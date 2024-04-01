<?php

namespace tthe\UtilTool;

use Psr\Http\Message\ServerRequestInterface;

class HttpRepresentation
{
    private const METHOD = 'method';
    private const PATH = 'path';
    private const PROTOCOL = 'protocol';
    private const HEADER = 'header';
    private const HEADER_VALUE = 'header-value';
    private const BODY = 'body';
    
    private ServerRequestInterface $request;
    private RequestBody $requestBody;
    
    private string $method;
    private string $path;
    private string $protocol;
    private array $headers;
    private ?string $body;

    public function __construct(ServerRequestInterface $request, RequestBody $body)
    {
        $this->request = $request;
        $this->requestBody = $body;
    }
    
    private function setProperties(): void
    {
        $this->method = $this->request->getMethod();
        $this->path = $this->request->getRequestTarget();
        $this->protocol = "HTTP/" . $this->request->getProtocolVersion();
        $this->body = $this->requestBody->getRaw();

        foreach ($this->request->getHeaders() as $header => $lines) {
            foreach ($lines as $line) {
                $this->headers[] = [$header . ':', $line];
            }
        }
    }
    
    private function wrap(string &$data, string $type): void
    {
        $data = "<span class=\"http-$type\">" . htmlspecialchars($data) . '</span>';
    }
    
    public function generateForHtml(): string
    {
        $this->setProperties();
        $this->wrap($this->method, self::METHOD);
        $this->wrap($this->path, self::PATH);
        $this->wrap($this->protocol, self::PROTOCOL);
        
        array_walk($this->headers, function(&$h) {
            $this->wrap($h[0], self::HEADER);
            $this->wrap($h[1], self::HEADER_VALUE);
        });
        
        if ($this->body) {
            $this->wrap($this->body, self::BODY);
        }
        
        return $this->generate();
    }
    
    public function generateForText(): string
    {
        $this->setProperties();
        return $this->generate();
    }
    
    private function generate(): string
    {
        $repr = implode(' ', [$this->method, $this->path, $this->protocol]);
        
        foreach ($this->headers as $h) {
            $repr .= "\n" . implode(' ', $h);
        }
        
        if ($this->body) {
            $repr .= "\n\n" . $this->body;
        }
        
        return $repr;
    }
}
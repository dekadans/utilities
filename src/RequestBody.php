<?php

namespace tthe\UtilTool;

use Psr\Http\Message\ServerRequestInterface;

class RequestBody
{
    private ServerRequestInterface $request;
    private ?string $value;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
        $this->request->getBody()->rewind();
        $this->value = $this->request->getBody()->getContents() ?: null;
    }
    
    public function hasBody(): bool
    {
        if (!empty($this->value) && in_array($this->request->getMethod(), ['POST', 'PUT', 'PATCH'])) {
            return true;
        }
        
        return false;
    }
    
    public function getRaw(): ?string
    {
        return $this->hasBody() ? $this->value : null;
    }
    
    public function getParsed(): ?array
    {
        if (!$this->hasBody()) {
            return null;
        }
        
        $parsed = $this->request->getParsedBody();
        if ($parsed instanceof \SimpleXMLElement) {
            $parsed = json_decode(json_encode($parsed), true);
        }
        
        return $parsed;
    }
}
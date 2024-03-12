<?php

namespace tthe\UtilTool;

use Psr\Http\Message\ServerRequestInterface;

readonly class ServiceResponse
{
    public Status $status;
    public RequestBody $body;
    public Utilities $utilities;
    public ServerRequestInterface $request;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
        $this->status = new Status($this->request);
        $this->body = new RequestBody($this->request);
        $this->utilities = new Utilities();
    }

    public function asViewData(): array
    {
        return [
            'status' => $this->status,
            'utilities' => $this->utilities,
            'request' => $this->request,
            'body' => $this->body
        ];
    }
}
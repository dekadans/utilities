<?php

namespace tthe\UtilTool;

use Psr\Http\Message\ServerRequestInterface;

readonly class ServiceResponse
{
    public Status $status;
    public Utilities $utilities;
    public ServerRequestInterface $request;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
        $this->status = new Status($this->request);
        $this->utilities = new Utilities();
    }
}
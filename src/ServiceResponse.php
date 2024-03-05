<?php

namespace tthe\UtilTool;

use Psr\Http\Message\RequestInterface;

readonly class ServiceResponse
{
    public Status $status;

    public Utilities $utilities;
    public RequestInterface $request;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
        $this->status = new Status($this->request);
        $this->utilities = new Utilities();
    }
}
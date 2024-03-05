<?php

namespace tthe\UtilTool;

use Lukasoppermann\Httpstatus\Httpstatus;
use Psr\Http\Message\RequestInterface;
use Slim\Exception\HttpBadRequestException;

readonly class Status
{
    public int $code;
    public string $message;

    public function __construct(RequestInterface $request)
    {
        $statusHelper = new Httpstatus();

        $query = $request->getQueryParams();
        $requestedStatus = (int) ($query['status'] ?? 200);

        if (!$statusHelper->hasStatusCode($requestedStatus)) {
            throw new HttpBadRequestException($request, 'Invalid status code');
        }

        $this->code = $requestedStatus;
        $this->message = $statusHelper->getReasonPhrase($requestedStatus);
    }
}
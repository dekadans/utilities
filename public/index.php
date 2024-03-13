<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Middleware\ContentLengthMiddleware;
use tthe\UtilTool\BodyOverrideMiddleware;
use tthe\UtilTool\Serializers\SerializationFactory;
use tthe\UtilTool\ServiceResponse;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();
$app->add(new BodyOverrideMiddleware());
$errorMiddleware = $app->addErrorMiddleware(true, false, false);

$contentLengthMiddleware = new ContentLengthMiddleware();
$app->add($contentLengthMiddleware);

$app->any('/', function (Request $request, Response $response, $args) {
    $serializer = SerializationFactory::make($request, $response);
    $data = new ServiceResponse($request);
    $response = $serializer->serialize($data);

    return $response->withStatus($data->status->code);
});

$app->run();

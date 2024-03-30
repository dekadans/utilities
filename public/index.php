<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use Slim\Middleware\ContentLengthMiddleware;
use Slim\Psr7\Factory\StreamFactory;
use tthe\UtilTool\Framework\BodyOverrideMiddleware;
use tthe\UtilTool\Exceptions\HttpPayloadTooLargeException;
use tthe\UtilTool\Serializers\SerializationFactory;
use tthe\UtilTool\ServiceResponse;
use tthe\UtilTool\Framework\UriFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();
$app->add(new BodyOverrideMiddleware());
$errorMiddleware = $app->addErrorMiddleware(false, false, false);

$contentLengthMiddleware = new ContentLengthMiddleware();
$app->add($contentLengthMiddleware);


$app->get('/meta/schemas/{format}', function (Request $request, Response $response, $args) {
    $format = $args['format'];
    $file = "../schemas/schema.$format";

    if (is_file($file)) {
        $resource = (new StreamFactory())->createStreamFromFile($file);
        return $response
            ->withBody($resource)
            ->withHeader('Content-Type', "application/$format");
    } else {
        throw new HttpNotFoundException($request);
    }
})->setName('schema');


$app->map(
    ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'],
    '/[{params:.*}]',
    function (Request $request, Response $response, $args) use ($app) {
        if (strlen($request->getBody()->getContents()) > 10000) {
            throw new HttpPayloadTooLargeException($request);
        }

        $uriFactory = new UriFactory(
            $request,
            $app->getRouteCollector()->getRouteParser()
        );

        $serializer = SerializationFactory::make($request, $response, $uriFactory);
        $data = new ServiceResponse($request);
        $response = $serializer->serialize($data);

        return $response->withStatus($data->status->code);
    }
)->setName('main');


$app->run();

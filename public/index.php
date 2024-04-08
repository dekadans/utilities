<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use Slim\Middleware\ContentLengthMiddleware;
use Slim\Psr7\Factory\StreamFactory;
use tthe\UtilTool\Exceptions\ReservedException;
use tthe\UtilTool\Framework\BodyOverrideMiddleware;
use tthe\UtilTool\Exceptions\HttpPayloadTooLargeException;
use tthe\UtilTool\Framework\CorsMiddleware;
use tthe\UtilTool\Linkset;
use tthe\UtilTool\Serializers\SerializationFactory;
use tthe\UtilTool\ServiceResponse;
use tthe\UtilTool\Framework\UriFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();
$app->add(new BodyOverrideMiddleware());
$app->add(new CorsMiddleware());
$errorMiddleware = $app->addErrorMiddleware(false, false, false);
$contentLengthMiddleware = new ContentLengthMiddleware();
$app->add($contentLengthMiddleware);


/**
 * Serving JSON and XML schema resources.
 */
$app->get('/meta/schemas/{format}', function (Request $request, Response $response, $args) {
    $format = $args['format'];
    $file = "../schemas/schema.$format";

    if (is_file($file)) {
        $mediaType = match ($format) {
            'xml' => 'application/xml',
            'json' => 'application/schema+json'
        };

        $resource = (new StreamFactory())->createStreamFromFile($file);
        return $response
            ->withBody($resource)
            ->withHeader('Content-Type', $mediaType);
    } else {
        throw new HttpNotFoundException($request);
    }
})->setName('schema');


/**
 * Linkset (RFC 9264)
 */
$app->get('/meta/linkset', function (Request $request, Response $response, $args) use ($app) {
    $uriFactory = new UriFactory(
        $request,
        $app->getRouteCollector()->getRouteParser()
    );

    $linkset = new Linkset($uriFactory);
    $response->getBody()->write($linkset->toJson());
    return $response->withHeader('Content-Type', 'application/linkset+json');
})->setName('linkset');


/**
 * Any other paths under /meta are reserved for future use.
 */
$app->any('/meta[/{params:.*}]', function (Request $request, Response $response, $args) {
    throw new ReservedException($request);
});


/**
 * Every other path will generate the main resource.
 */
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
        $linksetUri = $uriFactory->linkset();
        $rootUri = $uriFactory->root();

        $serializer = SerializationFactory::make($request, $response, $uriFactory);
        $data = new ServiceResponse($request);
        $response = $serializer->serialize($data);

        return $response
            ->withStatus($data->status->code)
            ->withHeader(
                'Link',
                "<$linksetUri>; rel=\"linkset\", <$rootUri>; rel=\"canonical\""
            );
    }
)->setName('main');


/**
 * CORS preflights.
 */
$app->options(
    '/[{params:.*}]',
    function (Request $request, Response $response, $args) use ($app) {
        return $response->withStatus(204);
    }
);


$app->run();

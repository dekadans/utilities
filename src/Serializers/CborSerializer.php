<?php

namespace tthe\UtilTool\Serializers;

use CBOR\CBORObject;
use CBOR\ListObject;
use CBOR\MapItem;
use CBOR\MapObject;
use CBOR\NegativeIntegerObject;
use CBOR\OtherObject\FalseObject;
use CBOR\OtherObject\NullObject;
use CBOR\OtherObject\TrueObject;
use CBOR\TextStringObject;
use CBOR\UnsignedIntegerObject;
use Psr\Http\Message\ResponseInterface;
use tthe\UtilTool\ServiceResponse;

class CborSerializer implements SerializerInterface
{
    use ArraySerializer;
    
    public const CONTENT_TYPE = 'application/cbor';
    
    private ResponseInterface $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }
    
    private function toCbor(mixed $data): CBORObject
    {
        if (is_string($data)) {
            return TextStringObject::create($data);
        } elseif (is_integer($data)) {
            if ($data >= 0) {
                return UnsignedIntegerObject::create($data);
            } else {
                return NegativeIntegerObject::create($data);
            }
        } elseif (is_array($data)) {
            if (array_is_list($data)) {
                return ListObject::create(
                    array_map($this->toCbor(...), $data)
                );
            } else {
                return MapObject::create(
                    array_map(function($key, $value) {
                        return MapItem::create(
                            $this->toCbor($key),
                            $this->toCbor($value)
                        );
                    }, array_keys($data), $data)
                );
            }
        } elseif (is_bool($data)) {
            return $data ? TrueObject::create() : FalseObject::create();
        } elseif (is_null($data)) {
            return NullObject::create();
        }
        
        throw new \RuntimeException('Failed to serialize to CBOR');
    }
    
    public function serialize(ServiceResponse $data): ResponseInterface
    {
        $cbor = $this->toCbor(
            $this->toArray($data)
        );
        
        $this->response->getBody()->write((string) $cbor);
        
        return $this->response
            ->withHeader('Content-Type', self::CONTENT_TYPE)
            ->withHeader('Content-Disposition', 'attachment; filename="utilities.cbor"');
    }
}
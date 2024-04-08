<?php

namespace tthe\UtilTool;

use tthe\UtilTool\Framework\UriFactory;
use tthe\UtilTool\Serializers\SerializationFactory;

class Linkset
{
    private UriFactory $uriFactory;
    private string $root;

    public function __construct(UriFactory $uriFactory)
    {
        $this->uriFactory = $uriFactory;
        $this->root = $this->uriFactory->root();
    }
    
    private function getAlternates(): array
    {
        $alternates = [];

        foreach (SerializationFactory::available() as $mediaType => $text) {
            $alternates[] = [
                'title' => $text,
                'href' => $this->root,
                'type' => $mediaType
            ];
        }
        
        return $alternates;
    }
    
    private function getDescribedBy(): array
    {
        return [
            [
                'title' => 'JSON Schema',
                'href' => $this->uriFactory->jsonSchema(),
                'type' => 'application/schema+json'
            ],
            [
                'title' => 'XML Schema',
                'href' => $this->uriFactory->xmlSchema(),
                'type' => 'application/xml'
            ]
        ];
    }
    
    public function toJson(): string
    {
        $data = [
            'linkset' => [
                [
                    'anchor' => $this->root,
                    'alternate' => $this->getAlternates(),
                    'describedby' => $this->getDescribedBy()
                ]
            ]
        ];
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
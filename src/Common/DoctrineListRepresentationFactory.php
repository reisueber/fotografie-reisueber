<?php

declare(strict_types=1);

namespace App\Common;

use Sulu\Component\Rest\ListBuilder\Doctrine\DoctrineListBuilderFactoryInterface;
use Sulu\Component\Rest\ListBuilder\Metadata\FieldDescriptorFactoryInterface;
use Sulu\Component\Rest\ListBuilder\PaginatedRepresentation;
use Sulu\Component\Rest\RestHelperInterface;

class DoctrineListRepresentationFactory
{
    public function __construct(
        private DoctrineListBuilderFactoryInterface $listBuilderFactory,
        private FieldDescriptorFactoryInterface $fieldDescriptorFactory,
        private RestHelperInterface $restHelper
    ) {
    }

    public function createDoctrineListRepresentation(
        string $resourceKey,
        array $filters = [],
        array $parameters = []
    ): PaginatedRepresentation {
        $listBuilder = $this->listBuilderFactory->create($parameters['entity_class'] ?? null);
        $fieldDescriptors = $this->fieldDescriptorFactory->getFieldDescriptors($resourceKey);
        $this->restHelper->initializeListBuilder($listBuilder, $fieldDescriptors);

        foreach ($filters as $key => $value) {
            $listBuilder->where($fieldDescriptors[$key], $value);
        }

        return new PaginatedRepresentation(
            $listBuilder->execute(),
            $resourceKey,
            $listBuilder->getCurrentPage(),
            $listBuilder->getLimit(),
            $listBuilder->count()
        );
    }
}

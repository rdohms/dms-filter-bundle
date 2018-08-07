<?php
declare(strict_types=1);

namespace DMS\Bundle\FilterBundle\Rule;

use DMS\Bundle\FilterBundle\Filter\ContainerFilter;
use DMS\Filter\Rules\Rule;

/**
 * Service Rule
 *
 * @Annotation
 */
class Service extends Rule
{
    /**
     * @var string
     */
    public $service;

    /**
     * @var string
     */
    public $method;

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions(): array
    {
        return ['service', 'method'];
    }

    public function getFilter(): string
    {
        return ContainerFilter::class;
    }
}

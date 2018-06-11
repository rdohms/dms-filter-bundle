<?php

namespace DMS\Bundle\FilterBundle\Rule;

use DMS\Bundle\FilterBundle\Filter\ContainerFilter;
use DMS\Filter\Rules\Rule;

/**
 * Service Rule
 *
 * @package DMS\Bundle\FilterBundle\Rule
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
        return array('service', 'method');
    }

    /**
     * @return string
     */
    public function getFilter(): string
    {
        return ContainerFilter::class;
    }
}

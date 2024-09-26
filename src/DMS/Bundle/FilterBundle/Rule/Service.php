<?php
declare(strict_types=1);

namespace DMS\Bundle\FilterBundle\Rule;

use Attribute;
use DMS\Bundle\FilterBundle\Filter\ContainerFilter;
use DMS\Filter\Rules\Rule;

/**
 * Service Rule
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Service extends Rule
{
    public function __construct(
        public string $service,
        public string $method
    ){
    }

    public function getFilter(): string
    {
        return ContainerFilter::class;
    }
}

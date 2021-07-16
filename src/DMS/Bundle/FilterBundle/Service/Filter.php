<?php
declare(strict_types=1);

namespace DMS\Bundle\FilterBundle\Service;

use DMS\Filter\FilterInterface;
use DMS\Filter\Rules\Rule;

/**
 * Filter Service
 *
 * Provides filtering result based on annotation in the class.
 */
class Filter
{
    private FilterInterface $filterExecutor;

    /**
     * Instantiates the Filter Service
     */
    public function __construct(FilterInterface $filter)
    {
        $this->filterExecutor = $filter;
    }

    /**
     * Filter an object based on its annotations
     */
    public function filterEntity(object $object): void
    {
        $this->filterExecutor->filterEntity($object);
    }

    /**
     * Filters only a selected property of an entity
     */
    public function filterProperty(object $object, string $property): void
    {
        $this->filterExecutor->filterProperty($object, $property);
    }

    /**
     * Runs a given value through one or more filter rules returning the
     * filtered value
     *
     * @param mixed       $value
     * @param Rule[]|Rule $filter
     *
     * @return mixed
     */
    public function filterValue($value, $filter)
    {
        return $this->filterExecutor->filterValue($value, $filter);
    }

    /**
     * Retrieve the actual filter executor instance
     */
    public function getFilterExecutor(): FilterInterface
    {
        return $this->filterExecutor;
    }
}

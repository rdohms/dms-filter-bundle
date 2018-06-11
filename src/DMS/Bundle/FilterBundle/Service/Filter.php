<?php

namespace DMS\Bundle\FilterBundle\Service;

use DMS\Filter\FilterInterface;

/**
 * Filter Service
 *
 * Provides filtering result based on annotation in the class.
 *
 * @package DMS
 * @subpackage Bundle
 */
class Filter
{

    /**
     * @var FilterInterface
     */
    private $filterExecutor;

    /**
     * Instantiates the Filter Service
     * @param FilterInterface $filter
     */
    public function __construct(FilterInterface $filter)
    {
        $this->filterExecutor = $filter;
    }

    /**
     * Filter an object based on its annotations
     *
     * @param object $object
     */
    public function filterEntity($object): void
    {
        $this->filterExecutor->filterEntity($object);
    }

    /**
     * Filters only a selected property of an entity
     *
     * @param object $object
     * @param string $property
     */
    public function filterProperty($object, $property): void
    {
        $this->filterExecutor->filterProperty($object, $property);
    }

    /**
     * Runs a given value through one or more filter rules returning the
     * filtered value
     *
     * @param mixed $value
     * @param \DMS\Filter\Rules\Rule[]|\DMS\Filter\Rules\Rule $filter
     *
     * @return mixed
     */
    public function filterValue($value, $filter)
    {
        return $this->filterExecutor->filterValue($value, $filter);
    }

    /**
     * Retrieve the actual filter executor instance
     *
     * @return FilterInterface
     */
    public function getFilterExecutor(): FilterInterface
    {
        return $this->filterExecutor;
    }
}

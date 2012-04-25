<?php

namespace DMS\Bundle\FilterBundle\Service;

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
     * @var \DMS\Filter\Filter
     */
    private $filterExecutor;

    /**
     * Instantiates the Filter Service
     */
    public function __construct()
    {
        //Get Doctrine Reader
        $reader = new \Doctrine\Common\Annotations\AnnotationReader();

        //Load AnnotationLoader
        $loader = new \DMS\Filter\Mapping\Loader\AnnotationLoader($reader);
        $this->loader = $loader;

        //Get a MetadataFactory
        $metadataFactory = new \DMS\Filter\Mapping\ClassMetadataFactory($loader);

        //Get a Filter
        $this->filterExecutor = new \DMS\Filter\Filter($metadataFactory);
    }

    /**
     * Filter an object based on its annotations
     *
     * @param object $object
     */
    public function filterEntity($object)
    {
        $this->filterExecutor->filterEntity($object);
    }

    /**
     * Filters only a selected property of an entity
     *
     * @param object $object
     * @param string $property
     */
    public function filterProperty($object, $property)
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
     * @return \DMS\Filter\Filter
     */
    public function getFilterExecutor()
    {
        return $this->filterExecutor;
    }
}
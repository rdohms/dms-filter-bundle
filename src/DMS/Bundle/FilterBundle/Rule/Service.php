<?php

namespace DMS\Bundle\FilterBundle\Rule;

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
    public function getRequiredOptions()
    {
        return array('service', 'method');
    }

    /**
     * @return string
     */
    public function getFilter()
    {
        return 'dms.filter.container_filter';
    }
}

<?php

namespace DMS\Bundle\FilterBundle\Form;

use Symfony\Component\Form\Extension\Validator\Type;
use Symfony\Component\Form\AbstractExtension;
use DMS\Bundle\FilterBundle\Service\Filter;
use DMS\Bundle\FilterBundle\Form\Type\FormTypeFilterExtension;

/**
 * Filter Extension
 *
 * Enabled filtering in forms
 */
class FilterExtension extends AbstractExtension
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var boolean
     */
    protected $autoFilter;

    /**
     * {@inheritdoc}
     *
     * @param \DMS\Bundle\FilterBundle\Service\Filter $filterService
     * @param boolean $autoFilter
     */
    public function __construct(Filter $filterService, $autoFilter)
    {
        $this->filter     = $filterService;
        $this->autoFilter = $autoFilter;
    }

    /**
     * {@inheritdoc}
     */
    protected function loadTypeExtensions()
    {
        return array(
            new FormTypeFilterExtension($this->filter, true),
        );
    }
}

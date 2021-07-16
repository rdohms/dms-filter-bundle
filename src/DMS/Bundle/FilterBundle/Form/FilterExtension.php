<?php
declare(strict_types=1);

namespace DMS\Bundle\FilterBundle\Form;

use DMS\Bundle\FilterBundle\Form\Type\FormTypeFilterExtension;
use DMS\Bundle\FilterBundle\Service\Filter;
use Symfony\Component\Form\AbstractExtension;

/**
 * Filter Extension
 *
 * Enabled filtering in forms
 */
class FilterExtension extends AbstractExtension
{
    protected bool $autoFilter;
    private Filter $filter;

    /**
     * {@inheritdoc}
     *
     * @param Filter $filterService
     * @param bool   $autoFilter
     */
    public function __construct(Filter $filterService, $autoFilter)
    {
        $this->filter     = $filterService;
        $this->autoFilter = $autoFilter;
    }

    /**
     * {@inheritdoc}
     */
    protected function loadTypeExtensions(): array
    {
        return [
            new FormTypeFilterExtension($this->filter, $this->autoFilter),
        ];
    }
}

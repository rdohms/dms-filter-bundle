<?php

namespace DMS\Bundle\FilterBundle\Loader;

use DMS\Filter\Filters\BaseFilter;
use DMS\Filter\Filters\Loader\FilterLoader;
use DMS\Filter\Rules\Rule;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerAwareLoader extends FilterLoader implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Attempts to load Filter from Container or hands off to parent loader.
     *
     * @param Rule $rule
     * @return BaseFilter
     */
    public function getFilterForRule(Rule $rule)
    {
        $filterIdentifier = $rule->getFilter();

        if ($this->container === null || !$this->container->has($filterIdentifier)) {
            return parent::getFilterForRule($rule);
        }

        return $this->container->get($filterIdentifier);
    }

}

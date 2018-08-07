<?php
declare(strict_types=1);

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

    public function setContainer(?ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    /**
     * Attempts to load Filter from Container or hands off to parent loader.
     *
     * @return BaseFilter|null|\stdClass
     * @throws \UnexpectedValueException
     */
    public function getFilterForRule(Rule $rule)
    {
        $filterIdentifier = $rule->getFilter();

        if ($this->container === null || ! $this->container->has($filterIdentifier)) {
            return parent::getFilterForRule($rule);
        }

        return $this->container->get($filterIdentifier);
    }
}

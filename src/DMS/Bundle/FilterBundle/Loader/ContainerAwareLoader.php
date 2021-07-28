<?php
declare(strict_types=1);

namespace DMS\Bundle\FilterBundle\Loader;

use DMS\Filter\Filters\BaseFilter;
use DMS\Filter\Filters\Loader\FilterLoader;
use DMS\Filter\Rules\Rule;
use stdClass;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use UnexpectedValueException;

class ContainerAwareLoader extends FilterLoader implements ContainerAwareInterface
{
    protected ?ContainerInterface $container;

    public function setContainer(?ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    /**
     * Attempts to load Filter from Container or hands off to parent loader.
     *
     * @return BaseFilter|stdClass|null
     *
     * @throws UnexpectedValueException
     */
    public function getFilterForRule(Rule $rule): BaseFilter
    {
        $filterIdentifier = $rule->getFilter();

        if ($this->container === null || ! $this->container->has($filterIdentifier)) {
            return parent::getFilterForRule($rule);
        }

        return $this->container->get($filterIdentifier);
    }
}

<?php

namespace DMS\Bundle\FilterBundle\Filter;

use DMS\Bundle\FilterBundle\Rule\Service;
use DMS\Filter\Filters\BaseFilter;
use DMS\Filter\Rules\Rule;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerFilter extends BaseFilter implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Enforces the desired filtering on the the value
     * returning a filtered value.
     *
     * @param Service|Rule $rule
     * @param mixed $value
     *
     * @throws \Exception
     * @return mixed
     */
    public function apply(Rule $rule, $value)
    {
        if (!$this->container->has($rule->service)) {
            throw new \Exception("Unable to find service '{$rule->service}' to execute defined rule.");
        }

        $service = $this->container->get($rule->service);

        if (! method_exists($service, $rule->method)) {
            throw new \Exception("Unable to find the method '{$rule->method}' in service '{$rule->service}'.");
        }

        $method = $rule->method;

        return $service->$method($value);
    }


}

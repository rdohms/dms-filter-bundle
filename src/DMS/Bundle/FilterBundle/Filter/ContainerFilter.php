<?php
declare(strict_types=1);

namespace DMS\Bundle\FilterBundle\Filter;

use DMS\Filter\Filters\BaseFilter;
use DMS\Filter\Rules\Rule;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerInterface;

use function is_callable;
use function sprintf;

class ContainerFilter extends BaseFilter
{
    protected ?ContainerInterface $container;

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(?ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    /**
     * Enforces the desired filtering on the the value
     * returning a filtered value.
     *
     * @param Rule $rule
     * @param mixed $value
     *
     * @return mixed
     *
     */
    public function apply(Rule $rule, mixed $value): mixed
    {
        if ($this->container === null) {
            throw new RuntimeException("The container is unavailable");
        }

        if (! $this->container->has($rule->service)) {
            throw new RuntimeException(
                sprintf("Unable to find service '%s' to execute defined rule.", $rule->service)
            );
        }

        $service = $this->container->get($rule->service);

        if (! is_callable([$service, $rule->method])) {
            throw new RuntimeException(
                sprintf("Unable to find the method '%s' in service '%s'.", $rule->method, $rule->service)
            );
        }

        $method = $rule->method;

        return $service->$method($value, $rule->options);
    }
}

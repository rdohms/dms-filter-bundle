<?php
declare(strict_types=1);

namespace DMS\DMS\Bundle\FilterBundle\Tests\Integration;

use DMS\Bundle\FilterBundle\DependencyInjection\DMSFilterExtension;
use DMS\Bundle\FilterBundle\Service\Filter;
use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use function method_exists;

class ContainerTest extends TestCase
{
    use ProphecyTrait;

    private ContainerBuilder $container;

    /**
     * @before
     */
    public function buildContainer(): void
    {
        $this->container = new ContainerBuilder();

        // Cover external dependencies
        $this->container->set('annotation_reader', $this->prophesize(AnnotationReader::class)->reveal());

        $extension = new DMSFilterExtension();
        $extension->load([], $this->container);

        $this->container->compile();
    }

    public function testContainerBoots(): void
    {
        $this->container->get(Filter::class);

        if (method_exists($this->container, 'isCompiled')) {
            self::assertTrue($this->container->isCompiled());
        } else {
            self::assertTrue($this->container->isFrozen());
        }
    }
}

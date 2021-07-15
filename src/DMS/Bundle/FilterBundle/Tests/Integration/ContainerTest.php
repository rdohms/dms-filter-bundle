<?php

namespace DMS\DMS\Bundle\FilterBundle\Tests\Integration;

use DMS\Bundle\FilterBundle\DependencyInjection\DMSFilterExtension;
use DMS\Bundle\FilterBundle\Service\Filter;
use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ContainerTest extends TestCase
{

    use ProphecyTrait;

    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @before
     * @return void
     */
    public function buildContainer()
    {
        $this->container = new ContainerBuilder();

        // Cover external dependencies
        $this->container->set('annotation_reader', $this->prophesize(AnnotationReader::class)->reveal());

        $extension = new DMSFilterExtension();
        $extension->load([], $this->container);

        $this->container->compile();
    }

    public function testContainerBoots()
    {
        $this->container->get(Filter::class);

        if (method_exists($this->container, 'isCompiled')) {
            self::assertTrue($this->container->isCompiled());
        } else {
            self::assertTrue($this->container->isFrozen());
        }

    }
}

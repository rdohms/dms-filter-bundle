<?php

namespace DMS\DMS\Bundle\FilterBundle\Tests\Integration;

use DMS\Bundle\FilterBundle\DependencyInjection\DMSFilterExtension;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
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
        $this->container->get('dms.filter');

        if (method_exists($this->container, 'isCompiled')) {
            self::assertTrue($this->container->isCompiled());
        } else {
            self::assertTrue($this->container->isFrozen());
        }

    }
}

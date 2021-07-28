<?php
declare(strict_types=1);

namespace DMS\Bundle\FilterBundle\Tests\Loader;

use DMS\Bundle\FilterBundle\Loader\ContainerAwareLoader;
use DMS\Filter\Filters\StripTags as StripTagsFilter;
use DMS\Filter\Rules\StripTags;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerAwareLoaderTest extends TestCase
{
    protected MockObject $container;

    protected ContainerAwareLoader $loader;

    public function testGetFilterForRule(): void
    {
        $this->container->expects($this->once())->method('has')->will($this->returnValue(true));
        $this->container->expects($this->once())->method('get')->will($this->returnValue(new StripTagsFilter()));

        $filter = $this->loader->getFilterForRule(new StripTags());

        $this->assertInstanceOf(StripTagsFilter::class, $filter);
    }

    public function testGetFilterForRuleCascade(): void
    {
        $this->container->expects($this->once())->method('has')->will($this->returnValue(false));
        $this->container->expects($this->never())->method('get');

        $filter = $this->loader->getFilterForRule(new StripTags());

        $this->assertInstanceOf(StripTagsFilter::class, $filter);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();

        $this->loader = new ContainerAwareLoader();
        $this->loader->setContainer($this->container);
    }
}

<?php


namespace DMS\Bundle\FilterBundle\Tests\Loader;

use DMS\Bundle\FilterBundle\Loader\ContainerAwareLoader;
use DMS\Filter\Rules\StripTags;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerAwareLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $container;

    /**
     * @var ContainerAwareLoader
     */
    protected $loader;

    protected function setUp()
    {
        parent::setUp();

        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->loader = new ContainerAwareLoader();
        $this->loader->setContainer($this->container);
    }

    public function testGetFilterForRule()
    {
        $this->container->expects($this->once())->method('has')->will($this->returnValue(true));
        $this->container->expects($this->once())->method('get')->will($this->returnValue(new \stdClass()));

        $filter = $this->loader->getFilterForRule(new StripTags());

        $this->assertInstanceOf('\stdClass', $filter);
    }

    public function testGetFilterForRuleCascade()
    {
        $this->container->expects($this->once())->method('has')->will($this->returnValue(false));
        $this->container->expects($this->never())->method('get');

        $filter = $this->loader->getFilterForRule(new StripTags());

        $this->assertInstanceOf('\DMS\Filter\Filters\StripTags', $filter);
    }
}

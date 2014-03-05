<?php

namespace DMS\Bundle\FilterBundle\Tests\Form\Type;

use DMS\Bundle\FilterBundle\Service\Filter;
use Symfony\Component\Form\FormEvents;
use DMS\Bundle\FilterBundle\Tests\Dummy\AnnotatedClass;
use DMS\Bundle\FilterBundle\Form\FilterExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class FormTypeFilterExtensionTest extends TypeTestCase
{
    /**
     * @var Filter | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filter;

    /**
     * @var boolean
     */
    protected $autoFilter = true;

    protected function setUp()
    {
        $classMetadataFactory = $this->getMockBuilder('DMS\Filter\Mapping\ClassMetadataFactory')
            ->disableOriginalConstructor()->getMock();

        $filterLoader   = $this->getMock('DMS\Filter\Filters\Loader\FilterLoaderInterface');
        $filterExecutor = new \DMS\Filter\Filter($classMetadataFactory, $filterLoader);

        $this->filter = $this->getMockBuilder('DMS\Bundle\FilterBundle\Service\Filter')
                             ->setConstructorArgs(array($filterExecutor))->getMock();

        parent::setUp();
    }

    protected function tearDown()
    {
        $this->filter = null;
        $this->autoFilter = true;

        parent::tearDown();
    }

    protected function getExtensions()
    {
        return array_merge(parent::getExtensions(), array(
            new FilterExtension($this->filter, $this->autoFilter),
        ));
    }

    public function testFilterSubscriberDefined()
    {
        /** @var $form \Symfony\Component\Form\Form */
        $form =  $this->factory->create('form');

        $dispatcher = $form->getConfig()->getEventDispatcher();

        $listeners = $dispatcher->getListeners(FormEvents::POST_SUBMIT);

        $filter = function ($value) {
            return (get_class($value[0]) == "DMS\Bundle\FilterBundle\Form\EventListener\DelegatingFilterListener");
        };

        $filterListeners = array_filter($listeners, $filter);

        $this->assertEquals(1, count($filterListeners));
    }

    public function testFilterSubscriberDisabled()
    {
        $this->autoFilter = false;
        $this->setUp();

        /** @var $form \Symfony\Component\Form\Form */
        $form =  $this->factory->create('form');

        $dispatcher = $form->getConfig()->getEventDispatcher();

        $listeners = $dispatcher->getListeners(FormEvents::POST_SUBMIT);
    }

    public function testBindValidatesData()
    {
        $entity = new AnnotatedClass();
        $builder = $this->factory->createBuilder('form', $entity);
        $builder->add('name', 'form');
        $form = $builder->getForm();

        $this->filter->expects($this->atLeastOnce())
            ->method('filterEntity');

        // specific data is irrelevant
        $form->submit(array());
    }
}

<?php

namespace DMS\Bundle\FilterBundle\Tests\Form\Type;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvents;
use DMS\Bundle\FilterBundle\Tests\Dummy\AnnotatedClass;
use DMS\Bundle\FilterBundle\Form\FilterExtension;
use Symfony\Component\Form\Tests\Extension\Core\Type\TypeTestCase;

class FormTypeFilterExtensionTest extends TypeTestCase
{
    /**
     * @var \DMS\Bundle\FilterBundle\Service\Filter
     */
    protected $filter;

    /**
     * @var boolean
     */
    protected $autoFilter = true;

    protected function setUp()
    {
        $filterLoader = $this->getMock('DMS\Filter\Filters\Loader\FilterLoaderInterface');
        $this->filter = $this->getMockBuilder('DMS\Bundle\FilterBundle\Service\Filter')
                             ->setConstructorArgs(array($filterLoader))->getMock();

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

        $listeners = $dispatcher->getListeners(FormEvents::POST_BIND);

        $filter = function($value){

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

        $listeners = $dispatcher->getListeners(FormEvents::POST_BIND);
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
        $form->bind(array());
    }
}

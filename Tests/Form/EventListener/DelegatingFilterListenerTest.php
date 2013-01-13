<?php

namespace DMS\Bundle\FilterBundle\Tests\Form\EventListener;

use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Util\PropertyPath;
use DMS\Bundle\FilterBundle\Form\EventListener\DelegatingFilterListener;
use DMS\Bundle\FilterBundle\Service\Filter;
use DMS\Bundle\FilterBundle\Tests\Dummy\AnnotatedClass;
use Symfony\Component\Form\FormEvents;

class DelegatingFilterListenerTest extends \PHPUnit_Framework_TestCase
{
    private $dispatcher;

    private $factory;

    private $filterLoader;

    private $builder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | Filter
     */
    private $delegate;

    /**
     * @var DelegatingFilterListener
     */
    private $listener;

    private $message;

    private $params;

    protected function setUp()
    {
        if (!class_exists('Symfony\Component\EventDispatcher\Event')) {
            $this->markTestSkipped('The "EventDispatcher" component is not available');
        }

        $this->dispatcher   = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->factory      = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $this->filterLoader = $this->getMock('DMS\Filter\Filters\Loader\FilterLoaderInterface');
        $this->delegate     = $this->getMockBuilder('DMS\Bundle\FilterBundle\Service\Filter')
                                   ->setConstructorArgs(array($this->filterLoader))
                                   ->getMock();
        $this->listener     = new DelegatingFilterListener($this->delegate);

        $this->message = 'Message';
        $this->params = array('foo' => 'bar');
    }

    protected function getMockGraphWalker()
    {
        return $this->getMockBuilder('Symfony\Component\Validator\GraphWalker')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function getMockMetadataFactory()
    {
        return $this->getMock('Symfony\Component\Validator\Mapping\ClassMetadataFactoryInterface');
    }

    protected function getMockTransformer()
    {
        return $this->getMock('Symfony\Component\Form\DataTransformerInterface', array(), array(), '', false, false);
    }

    protected function getExecutionContext($propertyPath = null)
    {
        $graphWalker = $this->getMockGraphWalker();
        $metadataFactory = $this->getMockMetadataFactory();
        $globalContext = new GlobalExecutionContext('Root', $graphWalker, $metadataFactory);

        return new ExecutionContext($globalContext, null, $propertyPath, null, null, null);
    }

    protected function getConstraintViolation($propertyPath)
    {
        return new ConstraintViolation($this->message, $this->params, null, $propertyPath, null);
    }

    protected function getFormError()
    {
        return new FormError($this->message, $this->params);
    }

    protected function getBuilder($name = 'name', $propertyPath = null)
    {
        $builder = new FormBuilder($name, $this->factory, $this->dispatcher);
        $builder->setAttribute('property_path', new PropertyPath($propertyPath ?: $name));
        $builder->setAttribute('error_mapping', array());
        $builder->setErrorBubbling(false);

        return $builder;
    }

    protected function getForm($name = 'name', $propertyPath = null)
    {
        return $this->getBuilder($name, $propertyPath)->getForm();
    }

    protected function getMockForm()
    {
        return $this->getMock('Symfony\Component\Form\Tests\FormInterface');
    }

    public function testFilterIgnoresNonRoot()
    {
        $form = $this->getMockForm();
        $form->expects($this->once())
            ->method('isRoot')
            ->will($this->returnValue(false));

        $this->delegate->expects($this->never())
            ->method('filterEntity');

        $this->listener->onPostBind(new DataEvent($form, null));
    }

    public function testFilterIgnoresNoObject()
    {
        $form = $this->getMockForm();

        $form->expects($this->once())
            ->method('isRoot')
            ->will($this->returnValue(true));

        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue(array(1,2,3)));

        $this->delegate->expects($this->never())
            ->method('filterEntity');

        $this->listener->onPostBind(new DataEvent($form, null));
    }

    public function testFilterOnPostBind()
    {
        $entity = new AnnotatedClass();
        $form = $this->getMockForm();

        $form->expects($this->once())
            ->method('isRoot')
            ->will($this->returnValue(true));

        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($entity));

        $this->delegate->expects($this->once())
            ->method('filterEntity');

        $this->listener->onPostBind(new DataEvent($form, null));
    }

    public function testAssertEventsBinding()
    {
        $bindedEvents = $this->listener->getSubscribedEvents();

        $this->assertArrayHasKey(FormEvents::POST_BIND, $bindedEvents);
    }
}

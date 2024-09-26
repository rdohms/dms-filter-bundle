<?php
declare(strict_types=1);

namespace DMS\Bundle\FilterBundle\Tests\Form\EventListener;

use DMS\Bundle\FilterBundle\Form\EventListener\DelegatingFilterListener;
use DMS\Bundle\FilterBundle\Service\Filter;
use DMS\Bundle\FilterBundle\Tests\Dummy\AttributedClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyPath;

use Symfony\Contracts\EventDispatcher\Event;
use function class_exists;

class DelegatingFilterListenerTest extends TestCase
{
    /**
     * @var EventDispatcherInterface|MockObject
     */
    private $dispatcher;

    /**
     * @var FormFactoryInterface|MockObject
     */
    private $factory;

    /**
     * @var Filter|MockObject
     */
    private $delegate;

    private DelegatingFilterListener $listener;

    private string $message;

    /**
     * @var mixed[]
     */
    private array $params;

    protected function setUp(): void
    {
        if (! class_exists(Event::class)) {
            $this->markTestSkipped('The "EventDispatcher" component is not available');
        }

        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
                                   ->getMock();
        $this->factory    = $this->getMockBuilder(FormFactoryInterface::class)
                                   ->getMock();
        $this->delegate   = $this->getMockBuilder(Filter::class)
                                   ->disableOriginalConstructor()
                                   ->getMock();
        $this->listener   = new DelegatingFilterListener($this->delegate);

        $this->message = 'Message';
        $this->params  = ['foo' => 'bar'];
    }

    /**
     * @param PropertyPath|string $propertyPath
     */
    protected function getBuilder(string $name = 'name', $propertyPath = null): FormBuilder
    {
        $builder = new FormBuilder($name, '', $this->dispatcher, $this->factory);
        $builder->setAttribute('property_path', new PropertyPath($propertyPath ?: $name));
        $builder->setAttribute('error_mapping', []);
        $builder->setErrorBubbling(false);

        return $builder;
    }

    /**
     * @param PropertyPath|string $propertyPath
     */
    protected function getForm(string $name = 'name', $propertyPath = null): FormInterface
    {
        return $this->getBuilder($name, $propertyPath)->getForm();
    }

    protected function getMockForm(): MockObject
    {
        return $this->getMockBuilder(FormInterface::class)
                    ->getMock();
    }

    public function testFilterIgnoresNonRootWithCascadeOff(): void
    {
        $form       = $this->getMockForm();
        $parentForm = $this->getMockForm();
        $config     = $this->getMockBuilder(FormConfigInterface::class)
                       ->getMock();

        $form->expects($this->exactly(2))
            ->method('isRoot')
            ->willReturn(false);

        $form->expects($this->once())
            ->method('getParent')
            ->willReturn($parentForm);

        $form->expects($this->never())
            ->method('getData');

        $parentForm->expects($this->once())
            ->method('isRoot')
            ->willReturn(true);

        $parentForm->expects($this->once())
            ->method('getConfig')
            ->willReturn($config);

        $config->expects($this->once())
            ->method('getOption')
            ->willReturn(false);

        $this->delegate->expects($this->never())
            ->method('filterEntity');

        $this->listener->onPostSubmit(new FormEvent($form, null));
    }

    public function testFilterFiltersNonRootWithCascadeOn(): void
    {
        $entity     = new AttributedClass();
        $form       = $this->getMockForm();
        $parentForm = $this->getMockForm();
        $config     = $this->getMockBuilder(FormConfigInterface::class)
                       ->getMock();

        $form->expects($this->exactly(2))
            ->method('isRoot')
            ->willReturn(false);

        $form->expects($this->once())
            ->method('getParent')
            ->willReturn($parentForm);

        $form->expects($this->once())
            ->method('getData')
            ->willReturn($entity);

        $parentForm->expects($this->once())
            ->method('isRoot')
            ->willReturn(true);

        $parentForm->expects($this->once())
            ->method('getConfig')
            ->willReturn($config);

        $config->expects($this->once())
            ->method('getOption')
            ->willReturn(true);

        $this->delegate->expects($this->once())
            ->method('filterEntity');

        $this->listener->onPostSubmit(new FormEvent($form, null));
    }

    public function testFilterIgnoresNoObject(): void
    {
        $form = $this->getMockForm();

        $form->expects($this->once())
            ->method('isRoot')
            ->willReturn(true);

        $form->expects($this->once())
            ->method('getData')
            ->willReturn([1, 2, 3]);

        $this->delegate->expects($this->never())
            ->method('filterEntity');

        $this->listener->onPostSubmit(new FormEvent($form, null));
    }

    public function testFilterOnPostBind(): void
    {
        $entity = new AttributedClass();
        $form   = $this->getMockForm();

        $form->expects($this->once())
            ->method('isRoot')
            ->willReturn(true);

        $form->expects($this->once())
            ->method('getData')
            ->willReturn($entity);

        $this->delegate->expects($this->once())
            ->method('filterEntity');

        $this->listener->onPostSubmit(new FormEvent($form, null));
    }

    public function testAssertEventsBinding(): void
    {
        $bindedEvents = $this->listener->getSubscribedEvents();

        $this->assertArrayHasKey(FormEvents::POST_SUBMIT, $bindedEvents);
    }
}

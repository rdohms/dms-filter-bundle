<?php
declare(strict_types=1);

namespace DMS\Bundle\FilterBundle\Tests\Form\Type;

use DMS\Bundle\FilterBundle\Form\EventListener\DelegatingFilterListener;
use DMS\Bundle\FilterBundle\Form\FilterExtension;
use DMS\Bundle\FilterBundle\Service\Filter;
use DMS\Bundle\FilterBundle\Tests\Dummy\AnnotatedClass;
use DMS\Filter\Filters\Loader\FilterLoaderInterface;
use DMS\Filter\Mapping\ClassMetadataFactory;
use Symfony\Component\Form\AbstractExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Test\TypeTestCase;
use function array_filter;
use function array_merge;

class FormTypeFilterExtensionTest extends TypeTestCase
{
    /**
     * @var Filter | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filter;

    /**
     * @var bool
     */
    protected $autoFilter = true;

    protected function setUp(): void
    {
        $classMetadataFactory = $this->getMockBuilder(ClassMetadataFactory::class)
                                     ->disableOriginalConstructor()
                                     ->getMock();

        $filterLoader = $this->getMockBuilder(FilterLoaderInterface::class)
                               ->getMock();

        $filterExecutor = new \DMS\Filter\Filter($classMetadataFactory, $filterLoader);

        $this->filter = $this->getMockBuilder(Filter::class)
                             ->setConstructorArgs([$filterExecutor])
                             ->getMock();

        parent::setUp();
    }

    protected function tearDown(): void
    {
        $this->filter     = null;
        $this->autoFilter = true;

        parent::tearDown();
    }

    /**
     * @return AbstractExtension[]
     */
    protected function getExtensions(): array
    {
        return array_merge(parent::getExtensions(), [
            new FilterExtension($this->filter, $this->autoFilter),
        ]);
    }

    public function testFilterSubscriberDefined(): void
    {
        /** @var Form $form */
        $form =  $this->factory->create(FormType::class);

        $dispatcher = $form->getConfig()->getEventDispatcher();

        $listeners = $dispatcher->getListeners(FormEvents::POST_SUBMIT);
        $filter    = function ($value) {
            return $value[0] instanceof DelegatingFilterListener;
        };

        $filterListeners = array_filter($listeners, $filter);

        $this->assertCount(1, $filterListeners);
    }

    public function testFilterSubscriberDisabled(): void
    {
        $this->autoFilter = false;
        $this->setUp();

        /** @var Form $form */
        $form =  $this->factory->create(FormType::class);

        $dispatcher = $form->getConfig()->getEventDispatcher();

        $listeners = $dispatcher->getListeners(FormEvents::POST_SUBMIT);
        $filter    = function ($value) {
            return $value[0] instanceof DelegatingFilterListener;
        };

        $filterListeners = array_filter($listeners, $filter);

        $this->assertCount(0, $filterListeners);
    }

    public function testBindValidatesData(): void
    {
        $entity  = new AnnotatedClass();
        $builder = $this->factory->createBuilder(FormType::class, $entity);
        $builder->add('name', FormType::class);
        $form = $builder->getForm();

        $this->filter->expects($this->atLeastOnce())
            ->method('filterEntity');

        // specific data is irrelevant
        $form->submit([]);
    }
}

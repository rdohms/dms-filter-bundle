<?php
declare(strict_types=1);

namespace DMS\Bundle\FilterBundle\Tests\Form\Type;

use DMS\Bundle\FilterBundle\Form\FilterExtension;
use DMS\Bundle\FilterBundle\Service\Filter;
use DMS\Bundle\FilterBundle\Tests\Dummy\AnnotatedClass;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Test\TypeTestCase;

use function array_filter;
use function array_merge;
use function assert;
use function count;
use function get_class;

class FormTypeFilterExtensionTest extends TypeTestCase
{
    /**
     * @var Filter|MockObject|null
     */
    protected $filter;

    protected bool $autoFilter = true;

    protected function setUp(): void
    {
        $classMetadataFactory = $this->getMockBuilder('DMS\Filter\Mapping\ClassMetadataFactory')
                                     ->disableOriginalConstructor()
                                     ->getMock();

        $filterLoader = $this->getMockBuilder('DMS\Filter\Filters\Loader\FilterLoaderInterface')
                               ->getMock();

        $filterExecutor = new \DMS\Filter\Filter($classMetadataFactory, $filterLoader);

        $this->filter = $this->getMockBuilder('DMS\Bundle\FilterBundle\Service\Filter')
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
     * @return mixed[]
     */
    protected function getExtensions(): array
    {
        return array_merge(parent::getExtensions(), [
            new FilterExtension($this->filter, $this->autoFilter),
        ]);
    }

    public function testFilterSubscriberDefined(): void
    {
        $form =  $this->factory->create(FormType::class);
        assert($form instanceof Form);

        $dispatcher = $form->getConfig()->getEventDispatcher();

        $listeners = $dispatcher->getListeners(FormEvents::POST_SUBMIT);
        $filter    = static function ($value) {
            return get_class($value[0]) === 'DMS\Bundle\FilterBundle\Form\EventListener\DelegatingFilterListener';
        };

        $filterListeners = array_filter($listeners, $filter);

        $this->assertEquals(1, count($filterListeners));
    }

    public function testFilterSubscriberDisabled(): void
    {
        $this->autoFilter = false;
        $this->setUp();

        $form =  $this->factory->create(FormType::class);
        assert($form instanceof Form);

        $dispatcher = $form->getConfig()->getEventDispatcher();

        $listeners = $dispatcher->getListeners(FormEvents::POST_SUBMIT);
        $filter    = static function ($value) {
            return get_class($value[0]) === 'DMS\Bundle\FilterBundle\Form\EventListener\DelegatingFilterListener';
        };

        $filterListeners = array_filter($listeners, $filter);

        $this->assertEquals(0, count($filterListeners));
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
        $form->submit(['name' => 'test']);
    }
}

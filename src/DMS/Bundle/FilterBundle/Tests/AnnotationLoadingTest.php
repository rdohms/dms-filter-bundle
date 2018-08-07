<?php
declare(strict_types=1);

namespace DMS\Bundle\FilterBundle\Tests;

use DMS\Bundle\FilterBundle\Rule\Service;
use DMS\Bundle\FilterBundle\Tests\Dummy\AnnotatedClass;
use DMS\Filter\Mapping\ClassMetadataFactory;
use DMS\Filter\Mapping\ClassMetadataFactoryInterface;
use DMS\Filter\Mapping\Loader\AnnotationLoader;
use DMS\Filter\Mapping\Loader\LoaderInterface;
use DMS\Filter\Rules\Alpha;
use DMS\Filter\Rules\Rule;
use DMS\Filter\Rules\StripTags;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use PHPUnit\Framework\TestCase;
use function array_shift;

class AnnotationLoadingTest extends TestCase
{
    /**
     * @var AnnotationReader
     */
    protected $reader;

    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * @var ClassMetadataFactoryInterface
     */
    protected $factory;

    public function testRuleLoader(): void
    {
        $metadata = $this->factory->getClassMetadata(AnnotatedClass::class);

        $this->assertRules(2, [StripTags::class, Alpha::class], $metadata->getPropertyRules('name'));
        $this->assertRules(1, [StripTags::class], $metadata->getPropertyRules('nickname'));
        $this->assertRules(1, [StripTags::class], $metadata->getPropertyRules('description'));
        $this->assertRules(1, [Service::class], $metadata->getPropertyRules('serviceFiltered'));

        $rules = $metadata->getPropertyRules('description');

        /** @var StripTags $rule */
        $rule = array_shift($rules);
        $this->assertEquals('<b><i>', $rule->allowed);
    }

    /**
     * @param string[] $expectedRules
     * @param Rule[]   $rules
     */
    protected function assertRules(int $count, array $expectedRules, array $rules): void
    {
        $this->assertCount($count, $rules);

        for ($i = 0; $i < $count; $i++) {
            $this->assertInstanceOf($expectedRules[$i], $rules[$i]);
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        AnnotationRegistry::registerAutoloadNamespace('DMS\Bundle\FilterBundle\Rule', __DIR__ . '/../../../../');

        $this->reader  = new AnnotationReader();
        $this->loader  = new AnnotationLoader($this->reader);
        $this->factory = new ClassMetadataFactory($this->loader);
    }
}

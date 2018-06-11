<?php

namespace DMS\Bundle\FilterBundle\Tests;

use DMS\Filter\Mapping\ClassMetadataFactory;
use DMS\Filter\Mapping\ClassMetadataFactoryInterface;
use DMS\Filter\Mapping\Loader\AnnotationLoader;
use DMS\Filter\Mapping\Loader\LoaderInterface;
use DMS\Filter\Rules\StripTags;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use PHPUnit\Framework\TestCase;
use DMS\Bundle\FilterBundle\Tests\Dummy\AnnotatedClass;
use DMS\Bundle\FilterBundle\Rule\Service;
use DMS\Filter\Rules\Alpha;

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

    protected function setUp()
    {
        parent::setUp();
        AnnotationRegistry::registerAutoloadNamespace('DMS\Bundle\FilterBundle\Rule', __DIR__ . '/../../../../');

        $this->reader = new AnnotationReader();
        $this->loader = new AnnotationLoader($this->reader);
        $this->factory = new ClassMetadataFactory($this->loader);
    }

    public function testRuleLoader()
    {
        $metadata = $this->factory->getClassMetadata(AnnotatedClass::class);

        $this->assertRules(2, array(StripTags::class, Alpha::class), $metadata->getPropertyRules('name'));
        $this->assertRules(1, array(StripTags::class), $metadata->getPropertyRules('nickname'));
        $this->assertRules(1, array(StripTags::class), $metadata->getPropertyRules('description'));
        $this->assertRules(1, array(Service::class), $metadata->getPropertyRules('serviceFiltered'));

        $rules = $metadata->getPropertyRules('description');

        /** @var $rule StripTags */
        $rule = \array_shift($rules);
        $this->assertEquals('<b><i>', $rule->allowed);
    }

    protected function assertRules($count, $expectedRules, $rules)
    {
        $this->assertCount($count, $rules);

        for ($i=0; $i < $count; $i++) {
            $this->assertInstanceOf($expectedRules[$i], $rules[$i]);
        }
    }
}

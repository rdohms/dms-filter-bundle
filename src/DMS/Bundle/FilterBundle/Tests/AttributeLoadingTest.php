<?php

declare(strict_types=1);

namespace DMS\Bundle\FilterBundle\Tests;

use DMS\Bundle\FilterBundle\Rule\Service;
use DMS\Bundle\FilterBundle\Tests\Dummy\AttributedClass;
use DMS\Filter\Mapping\ClassMetadataFactory;
use DMS\Filter\Mapping\ClassMetadataFactoryInterface;
use DMS\Filter\Mapping\Loader\AttributeLoader;
use DMS\Filter\Mapping\Loader\LoaderInterface;
use DMS\Filter\Rules\Alpha;
use DMS\Filter\Rules\StripTags;
use PHPUnit\Framework\TestCase;

use function array_shift;
use function assert;

class AttributeLoadingTest extends TestCase
{
    protected LoaderInterface $loader;

    protected ClassMetadataFactoryInterface $factory;

    protected function setUp(): void
    {
        $this->loader  = new AttributeLoader();
        $this->factory = new ClassMetadataFactory($this->loader);
    }

    public function testRuleLoader(): void
    {
        $metadata = $this->factory->getClassMetadata(AttributedClass::class);

        $this->assertRules(2, [StripTags::class, Alpha::class], $metadata->getPropertyRules('name'));
        $this->assertRules(1, [StripTags::class], $metadata->getPropertyRules('nickname'));
        $this->assertRules(1, [StripTags::class], $metadata->getPropertyRules('description'));
        $this->assertRules(1, [Service::class], $metadata->getPropertyRules('serviceFiltered'));

        $rules = $metadata->getPropertyRules('description');

        $rule = array_shift($rules);
        assert($rule instanceof StripTags);
        $this->assertEquals('<b><i>', $rule->allowed);
    }

    /**
     * @param array<class-string> $expectedRules
     * @param array<class-string> $rules
     */
    protected function assertRules(int $count, array $expectedRules, array $rules): void
    {
        $this->assertCount($count, $rules);

        for ($i = 0; $i < $count; $i++) {
            $this->assertInstanceOf($expectedRules[$i], $rules[$i]);
        }
    }
}

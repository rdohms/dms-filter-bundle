<?php

declare(strict_types=1);

namespace DMS\Bundle\FilterBundle\Tests\Dummy;

use DMS\Bundle\FilterBundle\Rule as SfFilter;
use DMS\Filter\Rules as Filter;

class AttributedClass
{
    #[Filter\StripTags]
    #[Filter\Alpha]
    public string $name = '';

    #[Filter\StripTags]
    public string $nickname = '';

    #[Filter\StripTags(allowed: '<b><i>')]
    public string $description = '';

    #[SfFilter\Service(service: 'dms.sample', method: 'filterIt')]
    public string $serviceFiltered = '';
}

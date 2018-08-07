<?php
declare(strict_types=1);

namespace DMS\Bundle\FilterBundle\Tests\Dummy;

use DMS\Bundle\FilterBundle\Rule as SfFilter;
use DMS\Filter\Rules as Filter;

class AnnotatedClass
{
    /**
     * @Filter\StripTags()
     * @Filter\Alpha()
     *
     * @var string
     */
    public $name;

    /**
     * @Filter\StripTags()
     *
     * @var string
     */
    public $nickname;

    /**
     * @Filter\StripTags("<b><i>")
     *
     * @var string
     */
    public $description;

    /**
     * @var string
     *
     * @SfFilter\Service(service="dms.sample", method="filterIt")
     */
    public $serviceFiltered;
}

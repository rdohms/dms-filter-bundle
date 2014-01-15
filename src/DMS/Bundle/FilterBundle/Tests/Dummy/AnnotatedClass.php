<?php

namespace DMS\Bundle\FilterBundle\Tests\Dummy;

use DMS\Filter\Rules as Filter;
use DMS\Bundle\FilterBundle\Rule as SfFilter;

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

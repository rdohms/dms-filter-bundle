<?php

namespace DMS\Bundle\FilterBundle\Tests\Dummy;

use DMS\Filter\Rules as Filter;

class AnnotatedClass
{
    /**
     * @Filter\StripTags()
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

}
# DMS Filter Bundle

This bundle makes DMS/Filter available for use in your application for input filtering.

Current Status: [![Build Status](https://travis-ci.org/rdohms/dms-filter-bundle.png?branch=2.dev)](https://travis-ci.org/rdohms/dms-filter-bundle) [![Dependency Status](https://www.versioneye.com/php/dms:dms-filter-bundle/1.1.1/badge.png)](https://www.versioneye.com/php/dms:dms-filter-bundle/1.1.1)

## Install

### 1. Import libraries

Option A) Use Composer.

    composer require dms/dms-filter-bundle

### 2. Enable Bundle

Add this to your `AppKernel.php`

    new DMS\Bundle\FilterBundle\DMSFilterBundle(),

### 3. Configure

This bundle can now automatically filter your forms if it finds a annotated entity attached.

This is the default behaviour, if you want to disable it add this to your `config.yml`

    dms_filter:
        auto_filter_forms: false

## Usage

### Adding Annotations

To add annotations to your entity, import the namespace and add them like this:

```php
<?php

namespace App\Entity;

//Import Annotations
use DMS\Filter\Rules as Filter;

class User
{

    /**
    * @Filter\StripTags()
    * @Filter\Trim()
    * @Filter\StripNewlines()
    *
    * @var string
    */
    public $name;

    /**
    * @Filter\StripTags()
    * @Filter\Trim()
    * @Filter\StripNewlines()
    *
    * @var string
    */
    public $email;

}
```
### Manual Filtering

Use the `dms.filter` service along with annotations in the Entity to filter data.

```php
public function indexAction()
{

    $entity = new \Acme\DemoBundle\Entity\SampleEntity();
    $entity->name = "My <b>name</b>";
    $entity->email = " email@mail.com";

    $oldEntity = clone $entity;

    $filterService = $this->get('dms.filter');
    $filterService->filterEntity($entity);

    return array('entity' => $entity, "old" => $oldEntity);
}
```

### Auto filtering

This bundle can now automatically filter your forms if it finds a annotated entity attached. If enabled entities will be filtered before they are validated.

### Cascade Filtering

This Bundle automatically cascades filtering into all embedded forms that return valid entities. If you wish child
entities to be ignored, set the `cascade_filter` option on the form to false.


```php
class TaskType extends AbstractType
{
    // ...

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'cascade_filter' => false,
            ));
    }

    // ...
}
```

## Service based method

If you need to filter content using a method in a service, you do not need to create your own Annotations, you can
simply use the Service Filter, designed specifically for Symfony Services.

See below the usage example of the annotation, it takes 2 options: `service` and `method`.

```php
<?php

namespace App\Entity;

//Import Annotations
use DMS\Filter\Rules as Filter;

//Import Symfony Rules
use DMS\Bundle\FilterBundle\Rule as SfFilter;

class User
{
    /**
    * @Filter\StripTags()
    * @SfFilter\Service(service="dms.sample", method="filterIt")
    *
    * @var string
    */
    public $name;
}
```

The `filterIt` method can have any name, but it must take one paramter (the value) and return the filtered value.

## Compatibility

This is compatible with Symfony 2.8 and above, including 3.0.
For Symfony 2.3+ support use "^2.0".

## Contributing

Given you have composer, cloned the project repository and have a terminal open on it:

    composer.phar install --prefer-source --dev
    vendor/bin/phpunit
    vendor/bin/phpcs

The tests should be passing and you are ready to make contributions.

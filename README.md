# DMS Filter Bundle

This bundle makes DMS/Filter available for use in your application for input filtering.

Current Status: [![Build Status](https://travis-ci.org/rdohms/dms-filter-bundle.png?branch=2.dev)](https://travis-ci.org/rdohms/dms-filter-bundle) [![Dependency Status](https://www.versioneye.com/php/dms:dms-filter-bundle/1.1.1/badge.png)](https://www.versioneye.com/php/dms:dms-filter-bundle/1.1.1)

## Install

### 1. Import libraries

Option A) Use Composer.

    composer require dms/dms-filter-bundle

### 2. Enable Bundle

Add this to your `AppKernel.php`

    DMS\Bundle\FilterBundle\DMSFilterBundle::class => ['all' => true]

### 3. Configure

This bundle can now automatically filter your forms if it finds a annotated entity attached.

This is the default behaviour, if you want to disable it add this to your `config.yml`

    dms_filter:
        auto_filter_forms: false

## Usage

### Adding Annotations

To add attributes to your entity, import the namespace and add them like this:

```php
<?php

namespace App\Entity;

//Import Attributes
use DMS\Filter\Rules as Filter;

class User
{
    #[Filter\StripTags]
    #[Filter\Trim]
    #[Filter\StripNewlines]
    public string $name;

    #[Filter\StripTags]
    #[Filter\Trim]
    #[Filter\StripNewlines]
    public string $email;
}
```
### Manual Filtering

Use the `dms.filter.inner.filter` service along with attributes in the Entity to filter data.

```php
    public function userAction(#[Autowire(service: 'dms.filter.inner.filter')] Filter $filter): Response
    {
        $user = new User();
        $user->setName("My <b>name</b>");
        $user->setEmail(" email@mail.com");

        //Get a Filter
        $newUser = $filter->filterEntity($car);

        return new Response(
            $newUser->getModel()
        );
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

//Import Atributes
use DMS\Filter\Rules as Filter;

//Import Symfony Rules
use DMS\Bundle\FilterBundle\Rule as SfFilter;

class User
{
    #[Filter\StripTags]
    #[SfFilter\Service(service: 'dms.sample', method: 'filterIt')]
    public $name;
}
```

The `filterIt` method can have any name, but it must take one paramter (the value) and return the filtered value.

## Compatibility

This is compatible with Symfony 6.4 and above and PHP 8.2 and above

## Contributing

Given you have composer, cloned the project repository and have a terminal open on it:

    composer.phar install --prefer-source --dev
    vendor/bin/phpunit
    vendor/bin/phpcs

The tests should be passing and you are ready to make contributions.

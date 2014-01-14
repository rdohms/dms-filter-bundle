# DMS Filter Bundle

This bundle makes DMS/Filter available for use in your application for input filtering.

## Install

### 1. Import libraries

Option A) Use Composer.

    composer require dms/dms-filter-bundle:~2.0

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

## Contributing

Given you have composer, cloned the project repository and have a terminal open on it:

    composer.phar install --prefer-source --dev
    vendor/bin/phpunit

The tests should be passing and you are ready to make contributions.

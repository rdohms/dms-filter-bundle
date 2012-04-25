<?php
namespace DMS\Bundle\FilterBundle\Form\Type;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilder;
use DMS\Bundle\FilterBundle\Service\Filter;
use DMS\Bundle\FilterBundle\Form\EventListener\DelegatingFilterListener;

/**
 * Form Type Filter Extension
 *
 * Extends the Form Type and adds auto filtering to it.
 * It checks the dms_filter.auto_filter_forms parameter to see if it should or
 * not enable auto filtering.
 */
class FormTypeFilterExtension extends AbstractTypeExtension
{
    /**
     * @var \DMS\Bundle\FilterBundle\Service\Filter
     */
    protected $filterService;

    /**
     * @var boolean
     */
    protected $autoFilter;

    /**
     * @param \DMS\Bundle\FilterBundle\Service\Filter $filterService
     * @param boolean $autoFilter
     */
    public function __construct(Filter $filterService, $autoFilter)
    {
        $this->filterService = $filterService;
        $this->autoFilter    = $autoFilter;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        if ( ! $this->autoFilter) return;

        $builder->addEventSubscriber(new DelegatingFilterListener($this->filterService));
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
}

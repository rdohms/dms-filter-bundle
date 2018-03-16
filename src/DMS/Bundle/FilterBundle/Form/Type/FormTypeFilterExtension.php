<?php

namespace DMS\Bundle\FilterBundle\Form\Type;

use DMS\Bundle\FilterBundle\Form\EventListener\DelegatingFilterListener;
use DMS\Bundle\FilterBundle\Service\Filter;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
        $this->autoFilter = $autoFilter;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$this->autoFilter) {
            return;
        }

        $builder->addEventSubscriber(new DelegatingFilterListener($this->filterService));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'cascade_filter' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return FormType::class;
    }
}

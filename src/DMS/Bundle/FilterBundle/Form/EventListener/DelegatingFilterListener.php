<?php
declare(strict_types=1);

namespace DMS\Bundle\FilterBundle\Form\EventListener;

use DMS\Bundle\FilterBundle\Service\Filter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

use function is_object;

/**
 * Delegating Filter Listener
 *
 * This subscriber listens to form events to automatically run filtering
 * on the attached entity, like Validation is done.
 */
class DelegatingFilterListener implements EventSubscriberInterface
{
    protected Filter $filterService;

    public function __construct(Filter $filterService)
    {
        $this->filterService = $filterService;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::POST_SUBMIT => ['onPostSubmit', 1024],
        ];
    }

    /**
     * Listens to the Post Bind event and triggers filtering if adequate.
     *
     * POST_SUBMIT is fired for every level of the form, from fields to
     * embedded forms. this method will filter any level that returns an
     * entity, or will only filter the root entity if 'cascade_filter'
     * is set to false.
     */
    public function onPostSubmit(FormEvent $event): void
    {
        $form = $event->getForm();

        if (! $form->isRoot() && ! $this->getRootFormCascadeOption($form)) {
            return;
        }

        $clientData = $form->getData();

        if (! is_object($clientData)) {
            return;
        }

        $this->filterService->filterEntity($clientData);
    }

    /**
     * Navigates to the Root form to define if cascading should be done.
     */
    public function getRootFormCascadeOption(FormInterface $form): bool
    {
        if (! $form->isRoot()) {
            return $this->getRootFormCascadeOption($form->getParent());
        }

        return $form->getConfig()->getOption('cascade_filter', false);
    }
}

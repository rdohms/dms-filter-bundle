<?php
namespace DMS\Bundle\FilterBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use DMS\Bundle\FilterBundle\Service\Filter;

/**
 * Delegating Filter Listener
 *
 * This subscriber listens to form events to automatically run filtering
 * on the attached entity, like Validation is done.
 */
class DelegatingFilterListener implements EventSubscriberInterface
{
    /**
     * @var \DMS\Bundle\FilterBundle\Service\Filter
     */
    protected $filterService;

    /**
     * @param \DMS\Bundle\FilterBundle\Service\Filter $filterService
     */
    public function __construct(Filter $filterService)
    {
        $this->filterService = $filterService;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_SUBMIT => array("onPostSubmit", 1024),
        );
    }

    /**
     * Listens to the Post Bind event and triggers filtering if adequate.
     *
     * @param FormEvent $event
     */
    public function onPostSubmit(FormEvent $event)
    {
        $form = $event->getForm();

        if (! $form->isRoot()) {
            return;
        }

        $clientData = $form->getData();

        if (! is_object($clientData)) {
            return;
        }

        // now check children to see if we need to filter embedded entities
        $getChildEntities = function($form) use (&$getChildEntities) {

            $entities = array();
            $children = $form->getChildren();

            foreach ($children as $child) {
                $childData = $child->getData();

                if (is_object($childData)) {
                    $entities[] = $childData;

                    if ($child->hasChildren()) {
                        $entities = array_merge($entities, $getChildEntities($child));
                    }
                }
            }

            return $entities;
        };

        $entitiesToFilter = array_merge(array($clientData), $getChildEntities($form));

        foreach ($entitiesToFilter as $entityToFilter) {
            $this->filterService->filterEntity($entityToFilter);
        }
    }

}

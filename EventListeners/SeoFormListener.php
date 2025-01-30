<?php


namespace SEOne\EventListeners;

use SEOne\SEOne;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\Action\BaseAction;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\TheliaFormEvent;
use Thelia\Core\Event\UpdateSeoEvent;
use Thelia\Model\MetaDataQuery;

class SeoFormListener extends BaseAction implements EventSubscriberInterface
{
    public function __construct(protected RequestStack $requestStack)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TheliaEvents::FORM_AFTER_BUILD.'.thelia_seo' => ['addCanonicalField', 128],
            TheliaEvents::CATEGORY_UPDATE_SEO => ['saveCategorySeoFields', 128],
            TheliaEvents::BRAND_UPDATE_SEO => ['saveBrandSeoFields', 128],
            TheliaEvents::CONTENT_UPDATE_SEO => ['saveContentSeoFields', 128],
            TheliaEvents::FOLDER_UPDATE_SEO => ['saveFolderSeoFields', 128],
            TheliaEvents::PRODUCT_UPDATE_SEO => ['saveProductSeoFields', 128],
        ];
    }

    public function saveCategorySeoFields(UpdateSeoEvent $event, $eventName, EventDispatcherInterface $dispatcher): void
    {
        $this->saveSeoFields($event, $eventName, $dispatcher, 'category');
    }

    public function saveBrandSeoFields(UpdateSeoEvent $event, $eventName, EventDispatcherInterface $dispatcher): void
    {
        $this->saveSeoFields($event, $eventName, $dispatcher, 'brand');
    }

    public function saveContentSeoFields(UpdateSeoEvent $event, $eventName, EventDispatcherInterface $dispatcher): void
    {
        $this->saveSeoFields($event, $eventName, $dispatcher, 'content');
    }

    public function saveFolderSeoFields(UpdateSeoEvent $event, $eventName, EventDispatcherInterface $dispatcher): void
    {
        $this->saveSeoFields($event, $eventName, $dispatcher, 'folder');
    }

    public function saveProductSeoFields(UpdateSeoEvent $event, $eventName, EventDispatcherInterface $dispatcher): void
    {
        $this->saveSeoFields($event, $eventName, $dispatcher, 'product');
    }

    protected function saveSeoFields(UpdateSeoEvent $event, $eventName, EventDispatcherInterface $dispatcher, $elementKey): void
    {
        $form = $this->requestStack->getCurrentRequest()->get('thelia_seo');

        if (null === $form || !\array_key_exists('id', $form) || !\array_key_exists('canonical', $form)) {
            return;
        }

        $canonicalValues = [];

        $canonicalMetaData = MetaDataQuery::create()
            ->filterByMetaKey(SEOne::SEO_CANONICAL_META_KEY)
            ->filterByElementKey($elementKey)
            ->filterByElementId($form['id'])
            ->findOneOrCreate();

        if (!$canonicalMetaData->isNew()) {
            $canonicalValues = json_decode($canonicalMetaData->getValue(), true);
        }

        $locale = $form['locale'];
        $canonicalValues[$locale] = $form['canonical'];

        $canonicalMetaData
            ->setIsSerialized(0)
            ->setValue(json_encode($canonicalValues))
            ->save();
    }

    public function addCanonicalField(TheliaFormEvent $event): void
    {
        $event->getForm()->getFormBuilder()
            ->add(
                'canonical',
                TextType::class
            );
    }
}

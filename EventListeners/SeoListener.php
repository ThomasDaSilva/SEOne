<?php


namespace SEOne\EventListeners;

use SEOne\Model\SeoneQuery;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Core\HttpFoundation\Request;

class SeoListener implements EventSubscriberInterface
{
    /** @var Request */
    protected $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    public function removeHrefLang($event): void
    {
        $objectType = $this->request->get('_view');
        $objectId = $this->request->get($objectType.'_id');

        $betterSeoObject = $this->getSEOneObject($objectType, $objectId);
    }

    public function checkSiteMap($event): void
    {
        $objectId = $event->getRewritingUrl()->getViewId();
        $objectType = $event->getRewritingUrl()->getView();

        $betterSeoObject = $this->getSEOneObject($objectType, $objectId);

        if (null !== $betterSeoObject) {
            if ($betterSeoObject->getNoindex() === 1) {
                $event->setHide(true);
            }
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        $events = [];
        /*if (class_exists('Sitemap\Event\SitemapEvent')) {
            $events[SitemapEvent::SITEMAP_EVENT] = ['checkSiteMap', 128];
        }
        if (class_exists('AlternateHreflang\Event\AlternateHreflangEvent')) {
            $events[AlternateHreflangEvent::BASE_EVENT_NAME] = ['removeHrefLang', 128];
        }*/

        return $events;
    }

    protected function getSEOneObject($objectType, $objectId)
    {
        $lang = $this->request->getSession()->getLang()->getLocale();

        $betterSeoObject = SeoneQuery::create()
            ->filterByObjectType($objectType)
            ->filterByObjectId($objectId)
            ->findOne();
        if (null !== $betterSeoObject) {
            $betterSeoObject->setLocale($lang);
        }

        return $betterSeoObject;
    }
}

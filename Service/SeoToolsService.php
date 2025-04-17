<?php

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SEOne\Service;

use SEOne\Event\SEOneSpecificEvents\SEOneMicroDataEvent;
use SEOne\Event\SEOneSpecificEvents\SEOnePageDescEvent;
use SEOne\Event\SEOneSpecificEvents\SEOnePageH1Event;
use SEOne\Event\SEOneSpecificEvents\SEOnePageTitleEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class SeoToolsService
{
    public function __construct(
        private RequestStack $requestStack,
        private EventDispatcherInterface $dispatcher,
        private SeoManager $seoDefaultManager,
    ) {
    }

    public function getSeoPageTitle(string $view, ?int $view_id): string
    {
        $pageTitleEvent = new SEOnePageTitleEvent(view: $view, view_id: $view_id);

        $this->dispatcher->dispatch(
            event: $pageTitleEvent,
            eventName: SEOnePageTitleEvent::BETTER_SEO_PAGE_TITLE
        );

        return $pageTitleEvent->getTitle() ?? '';
    }

    public function getSeoPageH1(string $view, ?int $view_id): string
    {
        $pageH1Event = new SEOnePageH1Event(view: $view, view_id: $view_id);

        $this->dispatcher->dispatch(
            event: $pageH1Event,
            eventName: SEOnePageH1Event::BETTER_SEO_PAGE_H1
        );

        return $pageH1Event->getTitle() ?? '';
    }

    public function getSeoPageDesc(string $view, ?int $view_id): string
    {
        $pageDescEvent = new SEOnePageDescEvent(view: $view, view_id: $view_id);
        $this->dispatcher->dispatch(
            event: $pageDescEvent,
            eventName: SEOnePageDescEvent::BETTER_SEO_PAGE_DESC
        );

        return $pageDescEvent->getTitle() ?? '';
    }

    public function getSeoMicroData(string $view, ?int $view_id, array $params = []): string
    {
        $microDataEvent = new SEOneMicroDataEvent(view: $view, view_id: $view_id, parameters: $params);

        $this->dispatcher->dispatch(
            event: $microDataEvent,
            eventName: SEOneMicroDataEvent::BETTER_SEO_MICRO_DATA
        );

        return $microDataEvent->getTitle() ?? '';
    }

    public function getPageId(string $view): ?string
    {
        $key = $view.'_id';
        $type = $this->getRequest()->get($key);
        if ($type) {
            return $type;
        }
        $seoService = $this->seoDefaultManager->getSeoServiceByView(view: $view);

        if (!$seoService) {
            return null;
        }

        return $this->getRequest()->get($seoService?->getIdentifier()) ?? null;
    }

    public function getPageView(): string
    {
        return $this->getRequest()->get('_view');
    }

    private function getRequest(): Request
    {
        return $this->requestStack->getCurrentRequest();
    }

    public function getPageCanonical(): string
    {
        $canonicalUrlEvent = new SEOneUrlEvent();

        $this->dispatcher->dispatch(
            event: $canonicalUrlEvent,
            eventName: SEOneUrlEvents::GENERATE_CANONICAL
        );
        return $canonicalUrlEvent->getUrl();
    }
}
